<?php
// Check users table structure and employees linked to them
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

// Check users table schema
$usersColumns = DB::select("DESCRIBE users");
echo "Users table columns:\n";
foreach ($usersColumns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}

echo "\n\nTotal Users: " . User::count() . "\n";
echo "Sample users:\n";

User::with('employee')->limit(5)->get()->each(function($user, $i) {
    echo ($i+1) . ". {$user->name} ({$user->email})\n";
    if ($user->employee) {
        echo "   Employee: ID {$user->employee->id}, Career Stage: " . $user->employee->getCareerStage() . "\n";
    } else {
        echo "   No associated employee\n";
    }
});

// Check if there are employees at all
echo "\n\nTotal Employees: " . Employee::count() . "\n";
echo "Career Stage Distribution:\n";

$careerStages = Employee::selectRaw('getCareerStage() as stage, COUNT(*) as count')
    ->groupBy('position_id')
    ->get();

// Actually use raw SQL for groupby career stage computation
$stageDistribution = DB::select("
    SELECT 
        CASE 
            WHEN mp.name IN ('Intern', 'Harian', 'Contract') THEN 'Manage Self (Non-Staff)'
            WHEN mp.name IN ('Staff', 'Supervisor') THEN 'Manage Self (Staff)'
            WHEN mp.name IN ('Manager', 'Tim Leader') THEN 'Manage Others'
            WHEN mp.name = 'General Manager' THEN 'Manage Managers'
            WHEN mp.name IN ('Director', 'Kepala Divisi') THEN 'Manage Function'
            ELSE 'Unknown'
        END as career_stage,
        COUNT(*) as count
    FROM employees e
    LEFT JOIN master_positions mp ON e.position_id = mp.id
    GROUP BY career_stage
");

foreach ($stageDistribution as $stage) {
    echo "  - {$stage->career_stage}: {$stage->count}\n";
}
