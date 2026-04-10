<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;

$total = Employee::count();
$withCareerStage = Employee::whereNotNull('career_stage')
    ->where('career_stage', '!=', '')
    ->count();
$nullCareerStage = $total - $withCareerStage;

echo "=== Career Stage Stats ===\n\n";
echo "Total Employees: {$total}\n";
echo "With Career Stage: {$withCareerStage} (" . round(($withCareerStage/$total)*100, 1) . "%)\n";
echo "Without Career Stage: {$nullCareerStage} (" . round(($nullCareerStage/$total)*100, 1) . "%)\n\n";

// Show distribution
$distribution = Employee::selectRaw('career_stage, COUNT(*) as count')
    ->groupBy('career_stage')
    ->orderByRaw("FIELD(career_stage, 'Manage Self (Non-Staff)', 'Manage Self (Staff)', 'Manage Others', 'Manage Managers', 'Manage Function', NULL)")
    ->get();

echo "=== Distribution ===\n";
foreach ($distribution as $row) {
    $stage = $row->career_stage ?? 'NULL';
    echo "{$stage}: {$row->count}\n";
}
