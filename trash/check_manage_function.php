<?php
// Quick check of manage_function framework items
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\VnbFrameworkItem;

$stages_count = VnbFrameworkItem::selectRaw('career_stage, COUNT(id) as count')
    ->groupBy('career_stage')
    ->orderBy('career_stage')
    ->get();

echo "VnB Framework Items Distribution:\n";
$total = 0;
foreach ($stages_count as $stage) {
    echo "  - {$stage->career_stage}: {$stage->count} items\n";
    $total += $stage->count;
}
echo "  TOTAL: $total items\n\n";

// Specifically check manage_function
$manage_function_count = VnbFrameworkItem::where('career_stage', 'manage_function')->count();
echo "✓ manage_function stage has $manage_function_count items\n";

if ($manage_function_count > 0) {
    echo "\nmanage_function Behaviours:\n";
    $manage_function_behaviours = VnbFrameworkItem::where('career_stage', 'manage_function')
        ->selectRaw('behaviour, COUNT(DISTINCT phase) as phase_count')
        ->groupBy('behaviour')
        ->get();
    
    foreach ($manage_function_behaviours as $behaviour) {
        echo "  - {$behaviour->behaviour}: {$behaviour->phase_count} phases\n";
    }
}
