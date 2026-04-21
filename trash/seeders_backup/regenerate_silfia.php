<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Delete Silfia's plan items
$silfiaPlanIds = App\Models\VnbPlan::where('employee_id', 4)->pluck('id')->toArray();
$deleted = App\Models\VnbPlanItem::whereIn('plan_id', $silfiaPlanIds)->delete();
echo "Deleted $deleted items for Silfia\n";

// Now regenerate just for Silfia (run command logic for employee 4)
$silfiaEmployee = App\Models\Employee::find(4);
$silfiaPlan = App\Models\VnbPlan::where('employee_id', 4)->first();

echo "Regenerating for: {$silfiaEmployee->name}, Level: {$silfiaEmployee->level}\n";

// Map level to career stage
$level = strtolower($silfiaEmployee->level);
$primaryRole = explode('/', $level)[0];
$primaryRole = strtolower(trim($primaryRole));

if (strpos($primaryRole, 'manager') !== false || strpos($primaryRole, 'kepala') !== false) {
    $careerStage = 'manage_managers';
} elseif (strpos($primaryRole, 'staff') !== false) {
    $careerStage = 'manage_self_staff';
} elseif (strpos($primaryRole, 'supervisor') !== false || strpos($primaryRole, 'lead') !== false) {
    $careerStage = 'manage_others';
} else {
    $careerStage = 'manage_self_non_staff';
}

echo "Mapped career stage: $careerStage\n";

// Get framework items
$frameworkItems = App\Models\VnbFrameworkItem::where('career_stage', $careerStage)->get()->groupBy('phase');
$itemsToInsert = [];

foreach ($frameworkItems as $phaseNumber => $items) {
    foreach ($items as $item) {
        // Format description as pipe-separated
        $integrationParts = [];
        if ($item->integration_1) {
            $integrationParts[] = $item->integration_1;
        }
        if ($item->integration_2) {
            $integrationParts[] = $item->integration_2;
        }
        $description = !empty($integrationParts) ? implode(' | ', $integrationParts) : $item->behaviour;

        $itemsToInsert[] = [
            'plan_id' => $silfiaPlan->id,
            'framework_item_id' => $item->id,
            'activity_title' => "{$item->behaviour} - Phase {$phaseNumber}",
            'description' => $description,
            'integration_1' => $item->integration_1,
            'integration_2' => $item->integration_2,
            'implementation_date' => now()->toDateString(),
            'deliverables' => '',
            'behavior_metrics' => null,
            'status' => 'draft',
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

// Insert items one by one
$created = 0;
foreach ($itemsToInsert as $itemData) {
    App\Models\VnbPlanItem::create($itemData);
    $created++;
}

echo "Created $created new items for Silfia's plan\n";
