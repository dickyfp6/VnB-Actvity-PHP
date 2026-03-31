<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;
use App\Models\VnbFrameworkItem;

// Helper function (dari controller)
function mapLevelToCareerStage($level): string {
    if (!$level) return 'manage_self_non_staff';
    $level = strtolower($level);
    if (str_contains($level, 'manager') || str_contains($level, 'kepala')) {
        return 'manage_managers';
    }
    if (str_contains($level, 'supervisor') || str_contains($level, 'lead')) {
        return 'manage_others';
    }
    if (str_contains($level, 'staff')) {
        return 'manage_self_staff';
    }
    return 'manage_self_non_staff';
}

$emp = Employee::find(2);
if ($emp) {
    echo "Employee: {$emp->name}\n";
    echo "Level: {$emp->level}\n";
    $mapped = mapLevelToCareerStage($emp->level);
    echo "Mapped Career Stage: {$mapped}\n";
    
    $count = VnbFrameworkItem::where('career_stage', $mapped)->count();
    echo "Framework items found: {$count}\n";
    
    // Show sample
    $sample = VnbFrameworkItem::where('career_stage', $mapped)->first();
    if ($sample) {
        echo "Sample: {$sample->behaviour} - Phase {$sample->phase}\n";
    }
} else {
    echo "Employee not found\n";
}
