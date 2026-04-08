<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\VnbPeriod;
use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use App\Models\MasterEmployeeStatus;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates realistic dummy data for PCX/Manager dashboards (50-100 new hires)
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $managers = Manager::take(10)->get();
        $departments = MasterDepartment::take(8)->get();
        $positions = MasterPosition::take(6)->get();
        
        if ($managers->isEmpty() || $departments->isEmpty() || $positions->isEmpty()) {
            $this->command->warn('⚠️  Insufficient master data. Run MasterDataSeeder, RolePermissionSeeder first.');
            return;
        }

        $this->command->info('🔄 Generating 75 dummy new hire employees...');

        // Generate 75 employees with varied data
        for ($i = 0; $i < 75; $i++) {
            $firstName = $faker->firstName();
            $lastEmployeeNumber = Employee::max('id') ?? 1000;
            $employeeNumber = 'EMP' . str_pad($lastEmployeeNumber + $i + 1, 5, '0', STR_PAD_LEFT);

            $dateJoined = now()->subDays(rand(30, 270));
            $inductionDate = $dateJoined->clone()->addDays(rand(1, 7));
            $vnbStartDate = $inductionDate->clone();
            $vnbEndDate = $vnbStartDate->clone()->addDays(90);

            $employee = Employee::create([
                'employee_number' => $employeeNumber,
                'name' => $firstName . ' ' . $faker->lastName(),
                'email' => strtolower($firstName) . '.dummy' . $i . '@wismilak.com',
                'whatsapp' => '62' . str_pad(rand(800000000, 899999999), 9, '0', STR_PAD_LEFT),
                'date_joined' => $dateJoined,
                'induction_date' => $inductionDate,
                'company' => 'PT. Wismilak Indonesia',
                'division_id' => 1, // Adjust based on your data
                'department_id' => $departments->random()->id,
                'position_id' => $positions->random()->id,
                'employee_status' => 'ACTIVE',
                'manager_functional_id' => $managers->random()->id,
                'manager_operational_id' => $managers->random()->id,
                'placement' => 'Jakarta',
                'level' => rand(1, 3),
                'vnb_period_start' => $vnbStartDate,
                'vnb_period_end' => $vnbEndDate,
                'vnb_status' => 'active',
                'employment_state' => 'active',
                'notes' => 'Dummy data for dashboard testing',
            ]);

            // Create VnbPeriod
            $period = VnbPeriod::create([
                'employee_id' => $employee->id,
                'phase_number' => 1,
                'start_date' => $vnbStartDate,
                'end_date' => $vnbEndDate,
                'cutoff_date' => $vnbEndDate->clone()->addDays(7),
                'status' => 'active',
            ]);

            // Determine phase based on days passed
            $daysElapsed = now()->diffInDays($vnbStartDate);
            $currentPhase = min(3, max(1, intval($daysElapsed / 30) + 1));

            // Create VnbPlan entries (1-3 depending on phase)
            for ($phase = 1; $phase <= $currentPhase; $phase++) {
                $planStatus = $phase < $currentPhase ? 'approved' : 'in_progress';
                $submittedAt = $phase < $currentPhase ? now()->subDays(rand(5, 30)) : null;
                $approvedAt = $phase < $currentPhase ? $submittedAt->clone()->addDays(rand(1, 7)) : null;

                $plan = VnbPlan::create([
                    'employee_id' => $employee->id,
                    'period_id' => $period->id,
                    'phase_number' => $phase,
                    'title' => "V&B Plan - Phase {$phase}",
                    'description' => "Development plan for phase {$phase}",
                    'planning_mode' => 'manual',
                    'status' => $planStatus,
                    'submitted_at' => $submittedAt,
                    'approved_at' => $approvedAt,
                    'revision_count' => rand(0, 2),
                ]);

                // Create PlanItems with varied completion
                $frameworkItems = VnbFrameworkItem::where('phase_number', $phase)->take(7)->get();
                
                foreach ($frameworkItems as $frameworkItem) {
                    // Vary completion: 0%, 25%, 50%, 75%, 100%
                    $completionOptions = [0, 25, 50, 75, 100];
                    $completionPercentage = $completionOptions[array_rand($completionOptions)];
                    
                    $itemStatus = match($completionPercentage) {
                        0 => 'not_started',
                        100 => $phase < $currentPhase ? 'completed' : 'completed',
                        default => 'in_progress',
                    };

                    $submittedAt = $completionPercentage > 0 ? now()->subDays(rand(0, 20)) : null;
                    $approvedAt = $completionPercentage === 100 ? $submittedAt?->clone()->addDays(rand(1, 5)) : null;

                    VnbPlanItem::create([
                        'plan_id' => $plan->id,
                        'framework_item_id' => $frameworkItem->id,
                        'activity_title' => $frameworkItem->name,
                        'description' => "Activity for {$frameworkItem->name}",
                        'status' => $itemStatus,
                        'completion_percentage' => $completionPercentage,
                        'submission_status' => $completionPercentage > 0 ? 'submitted' : 'draft',
                        'submitted_at' => $submittedAt,
                        'approved_functional_by' => $completionPercentage === 100 ? $managers->random()->id : null,
                        'approved_functional_at' => $approvedAt,
                        'due_date' => now()->addDays(rand(5, 45)),
                    ]);
                }
            }

            if (($i + 1) % 15 === 0) {
                $this->command->line('✓ Generated ' . ($i + 1) . ' employees...');
            }
        }

        $this->command->info('✅ Dummy dashboard data created successfully!');
        $this->command->info('📊 75 new hire employees with 3 phases and varied completion rates');
    }
}
