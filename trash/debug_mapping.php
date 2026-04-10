<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$level = "Non-Staff";
echo "Original level: '$level'\n";

$level = strtolower($level);
echo "Lowercased: '$level'\n";

$primaryRole = explode('/', $level)[0];
echo "After explode/trim: '$primaryRole'\n";

$primaryRole = strtolower(trim($primaryRole));
echo "Final primaryRole: '$primaryRole'\n";

// Check conditions
echo "\nCondition checks:\n";
echo "Contains 'manager'? " . (strpos($primaryRole, 'manager') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'kepala'? " . (strpos($primaryRole, 'kepala') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'staff'? " . (strpos($primaryRole, 'staff') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'supervisor'? " . (strpos($primaryRole, 'supervisor') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'lead'? " . (strpos($primaryRole, 'lead') !== false ? 'YES' : 'NO') . "\n";

// Map
if (strpos($primaryRole, 'manager') !== false || strpos($primaryRole, 'kepala') !== false) {
    $result = 'manage_managers';
} elseif (strpos($primaryRole, 'staff') !== false) {
    $result = 'manage_self_staff';
} elseif (strpos($primaryRole, 'supervisor') !== false || strpos($primaryRole, 'lead') !== false) {
    $result = 'manage_others';
} else {
    $result = 'manage_self_non_staff';
}

echo "\nFinal mapping: $result\n";
