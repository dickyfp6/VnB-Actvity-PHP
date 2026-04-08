<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\VnbFrameworkItem;

echo "=== Framework Items Count ===\n\n";

$stages = VnbFrameworkItem::selectRaw('career_stage, COUNT(*) as count')
    ->groupBy('career_stage')
    ->orderBy('career_stage')
    ->get();

foreach ($stages as $stage) {
    echo "{$stage->career_stage}: {$stage->count} items\n";
}

echo "\n=== Check manage_function specifically ===\n";
$count = VnbFrameworkItem::where('career_stage', 'manage_function')->count();
echo "manage_function items: {$count}\n";

// List first few
echo "\nFirst 3 manage_function items:\n";
VnbFrameworkItem::where('career_stage', 'manage_function')
    ->select('id', 'career_stage', 'behaviour', 'phase')
    ->limit(3)
    ->get()
    ->each(function($item) {
        echo "- Phase {$item->phase}: {$item->behaviour}\n";
    });
