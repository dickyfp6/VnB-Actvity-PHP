<?php

namespace App\Console\Commands;

use App\Models\VnbPlan;
use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;
use Illuminate\Console\Command;

class GeneratePlanItemsFromFramework extends Command
{
    protected $signature = 'vnb:generate-plan-items';
    protected $description = 'Generate plan items from framework for existing plans that have no items';

    public function handle()
    {
        $this->info('Starting to generate plan items from framework...');

        // Get all plans that have no items
        $plansWithoutItems = VnbPlan::doesntHave('items')->get();

        if ($plansWithoutItems->isEmpty()) {
            $this->info('✅ All plans already have items!');
            return 0;
        }

        $totalGenerated = 0;

        foreach ($plansWithoutItems as $plan) {
            // Map employee level to career stage
            $careerStage = $this->mapLevelToCareerStage($plan->employee->level);

            // Get framework items grouped by phase
            $frameworkItems = VnbFrameworkItem::where('career_stage', $careerStage)
                ->get()
                ->groupBy('phase');  // Group by full phase value: "1-3", "4-6", "6+"

            if ($frameworkItems->isEmpty()) {
                $this->warn("❌ No framework items found for career stage '{$careerStage}' (Plan ID: {$plan->id}, Employee: {$plan->employee->name})");
                continue;
            }

            // Prepare items for bulk insert
            $itemsToInsert = [];
            foreach ($frameworkItems as $phaseNumber => $items) {
                foreach ($items as $item) {
                    // Format description as "integration_1 | integration_2" (pipe-separated for view parsing)
                    $integrationParts = [];
                    if ($item->integration_1) {
                        $integrationParts[] = $item->integration_1;
                    }
                    if ($item->integration_2) {
                        $integrationParts[] = $item->integration_2;
                    }
                    $description = !empty($integrationParts) ? implode(' | ', $integrationParts) : $item->behaviour;

                    $itemsToInsert[] = [
                        'plan_id' => $plan->id,
                        'framework_item_id' => $item->id,
                        'activity_title' => "{$item->behaviour} - Phase {$phaseNumber}",
                        'description' => $description,  // Format: "int1 | int2" for view parsing
                        'integration_1' => $item->integration_1,
                        'integration_2' => $item->integration_2,
                        'implementation_date' => now()->toDateString(),
                        'deliverables' => '',  // Empty by default, user fills in later
                        'behavior_metrics' => null,
                        'status' => 'draft',  // Enum values: draft, submitted, approved, revision, completed, rejected
                        'completion_percentage' => 0,
                        'activity_description' => null,
                        'activity_date' => null,
                        'submission_status' => 'draft',
                        'revision_notes' => null,
                        'submitted_at' => null,
                        'due_date' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Bulk insert
            if (!empty($itemsToInsert)) {
                // Use create() method per item to avoid bulk insert enum issues
                $created = 0;
                foreach ($itemsToInsert as $itemData) {
                    try {
                        VnbPlanItem::create($itemData);
                        $created++;
                    } catch (\Exception $e) {
                        $this->error("Failed to create item: " . $e->getMessage());
                        $this->error("Data: " . json_encode($itemData));
                        throw $e;
                    }
                }
                $totalGenerated += $created;
                $this->line("✓ Generated " . $created . " items for plan '{$plan->title}' (Employee: {$plan->employee->name})");
            }
        }

        $this->info("\n📊 Generation completed!");
        $this->info("✅ Total items generated: $totalGenerated");

        return 0;
    }

    /**
     * Map employee level to career stage
     */
    private function mapLevelToCareerStage($level): string
    {
        if (!$level) {
            return 'manage_self_non_staff';
        }

        $level = strtolower($level);

        // Extract primary role (before "/" if compound role)
        $primaryRole = explode('/', $level)[0];
        $primaryRole = strtolower(trim($primaryRole));

        // Check Non-Staff FIRST (before checking Staff) since "non-staff" contains "staff"
        if (str_contains($primaryRole, 'non-staff') || str_contains($primaryRole, 'non staff')) {
            return 'manage_self_non_staff';
        }

        // Check Manager/Kepala
        if (str_contains($primaryRole, 'manager') || str_contains($primaryRole, 'kepala')) {
            return 'manage_managers';
        }

        // Check Staff (after checking non-staff)
        if (str_contains($primaryRole, 'staff')) {
            return 'manage_self_staff';
        }

        // Check Supervisor/Lead (primary role) 
        if (str_contains($primaryRole, 'supervisor') || str_contains($primaryRole, 'lead')) {
            return 'manage_others';
        }

        // Default
        return 'manage_self_non_staff';
    }
}
