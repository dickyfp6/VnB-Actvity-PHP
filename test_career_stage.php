<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;

$employees = Employee::where('id', '<=', 5)
    ->with('position')
    ->get();

echo "=== Career Stage Test ===\n\n";
foreach ($employees as $emp) {
    echo "ID {$emp->id}: {$emp->name}\n";
    echo "  Position: " . ($emp->position?->name ?? 'NULL') . "\n";
    echo "  Career Stage DB: {$emp->career_stage}\n";
    echo "  Career Stage (method): " . ($emp->getCareerStage() ?? 'NULL') . "\n";
    echo "  Career Stage Code: " . $emp->getCareerStageCode() . "\n\n";
}
