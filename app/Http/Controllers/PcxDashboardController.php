<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;
use App\Models\MasterDivision;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PcxDashboardController extends Controller
{
    /**
     * Show PCX Dashboard
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'current_year');
        $division = $request->input('division');

        // Base query for active employees in onboarding
        $query = Employee::query()
            ->where('vnb_status', 'active')
            ->with(['department.division', 'vnbPlans.items']);

        if ($division) {
            $query->whereHas('department', function ($q) use ($division) {
                $q->where('division_id', $division);
            });
        }

        $employees = $query->get();

        // Calculate headline statistics
        $stats = $this->calculateStats($employees, $period);

        // Calculate behaviour mastery data for radar chart
        $behaviourData = $this->calculateBehaviourMastery($employees);

        // Calculate divisional heatmap data (divisions × phases)
        $heatmapData = $this->calculateDivisionHeatmap();

        // Calculate completion velocity
        $velocityData = $this->calculateVelocity($employees);

        // Get all divisions for filter
        $divisions = MasterDivision::all();

        return view('dashboard.pcx.index', [
            'stats' => $stats,
            'behaviourData' => $behaviourData,
            'heatmapData' => $heatmapData,
            'velocityData' => $velocityData,
            'employees' => $employees,
            'divisions' => $divisions,
            'selectedDivision' => $division,
            'selectedPeriod' => $period,
        ]);
    }

    /**
     * Calculate headline statistics
     */
    private function calculateStats($employees, $period)
    {
        $totalActive = $employees->count();
        
        // Calculate average completion rate
        $totalPlans = VnbPlanItem::whereIn('plan_id', 
            VnbPlan::whereIn('employee_id', $employees->pluck('id'))->pluck('id')
        )->get();
        
        $avgCompletion = $totalPlans->count() > 0 
            ? $totalPlans->avg('completion_percentage') 
            : 0;

        // Count critical alerts (progress < 50% past midpoint)
        $criticalCount = $employees->filter(function ($emp) {
            $daysElapsed = $emp->vnb_period_start 
                ? now()->diffInDays($emp->vnb_period_start) 
                : 0;
            $midpoint = 45; // Half of 90 days
            
            if ($daysElapsed > $midpoint) {
                $planItems = VnbPlanItem::whereIn('plan_id',
                    VnbPlan::where('employee_id', $emp->id)->pluck('id')
                )->get();
                
                $avgProgress = $planItems->count() > 0 
                    ? $planItems->avg('completion_percentage') 
                    : 0;
                
                return $avgProgress < 50;
            }
            return false;
        })->count();

        // Find top division
        $topDivision = $employees->groupBy(function ($emp) {
            return $emp->department?->division_id ?? null;
        })->map(function ($group) {
            $plans = VnbPlanItem::whereIn('plan_id',
                VnbPlan::whereIn('employee_id', $group->pluck('id'))->pluck('id')
            )->get();
            
            $division = $group->first()->department?->division;
            
            return [
                'avg' => $plans->count() > 0 ? $plans->avg('completion_percentage') : 0,
                'div' => $division,
            ];
        })->sortByDesc('avg')->first();

        return [
            'total_active' => $totalActive,
            'avg_completion' => round($avgCompletion, 1),
            'critical_alerts' => $criticalCount,
            'top_department' => $topDivision['div']->name ?? 'N/A',
            'top_department_progress' => round($topDivision['avg'] ?? 0, 1),
        ];
    }

    /**
     * Calculate behaviour mastery data for radar chart
     */
    private function calculateBehaviourMastery($employees)
    {
        // Get all 7 behaviours from framework
        $behaviours = VnbFrameworkItem::distinct()
            ->pluck('behaviour')
            ->sort()
            ->values()
            ->toArray();

        if (empty($behaviours)) {
            $behaviours = ['Empathy', 'Speak with Data', 'Collaborative', 'Decisive', 
                          'Be Ambassador', 'Integrity', 'Innovation'];
        }

        // Calculate average completion for each behaviour
        $behaviourScores = [];
        foreach ($behaviours as $behaviour) {
            $items = VnbPlanItem::whereIn('plan_id',
                VnbPlan::whereIn('employee_id', $employees->pluck('id'))->pluck('id')
            )
            ->whereHas('frameworkItem', function ($q) use ($behaviour) {
                $q->where('behaviour', $behaviour);
            })
            ->get();

            $score = $items->count() > 0 
                ? $items->avg('completion_percentage') 
                : 0;
            
            $behaviourScores[] = round($score, 1);
        }

        return [
            'labels' => $behaviours,
            'data' => $behaviourScores,
        ];
    }

    /**
     * Calculate divisional heatmap data (divisions × phases)
     */
    private function calculateDivisionHeatmap()
    {
        $divisions = MasterDivision::all();
        $phases = [1, 2, 3];
        
        $heatmapData = [];

        foreach ($divisions as $div) {
            $row = ['division' => $div->name, 'phases' => []];
            
            foreach ($phases as $phase) {
                // Get employees in this division
                $divisionEmployeeIds = Employee::whereHas('department', function ($q) use ($div) {
                    $q->where('division_id', $div->id);
                })->pluck('id');
                
                $items = VnbPlanItem::whereIn('plan_id',
                    VnbPlan::where('phase_number', $phase)
                        ->whereIn('employee_id', $divisionEmployeeIds)
                        ->pluck('id')
                )
                ->get();

                $avgProgress = $items->count() > 0 
                    ? $items->avg('completion_percentage') 
                    : 0;

                $row['phases'][] = [
                    'phase' => "Phase {$phase}",
                    'progress' => round($avgProgress, 1),
                    'status' => $avgProgress >= 80 ? 'excellent' : ($avgProgress >= 50 ? 'good' : 'needs_attention'),
                ];
            }

            $heatmapData[] = $row;
        }

        return $heatmapData;
    }

    /**
     * Calculate completion velocity (trend over time)
     */
    private function calculateVelocity($employees)
    {
        $dates = [];
        $progressions = [];

        // Get data for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates[] = $date->format('M d');

            // Calculate average progress for employees as of this date
            $employees_at_date = $employees->filter(function ($emp) use ($date) {
                return $emp->vnb_period_start <= $date;
            });

            if ($employees_at_date->count() > 0) {
                $avg = VnbPlanItem::whereIn('plan_id',
                    VnbPlan::whereIn('employee_id', $employees_at_date->pluck('id'))->pluck('id')
                )->avg('completion_percentage') ?? 0;
                $progressions[] = round($avg, 1);
            } else {
                $progressions[] = 0;
            }
        }

        return [
            'dates' => $dates,
            'data' => $progressions,
        ];
    }
}
