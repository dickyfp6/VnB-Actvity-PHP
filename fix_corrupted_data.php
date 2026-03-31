<?php
/**
 * Fix corrupted vnb_plan_items data from failed save attempts
 * Run this from artisan tinker: include('fix_corrupted_data.php')
 */

use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;

// Get all behaviours from framework
$behaviours = VnbFrameworkItem::$behaviours;

// Map of corrupted values to correct values
$corrections = [
    'bagaimana' => 'Empathy',
];

// Track corrections
$count = 0;

foreach ($corrections as $corrupted => $correct) {
    // Find items with corrupted activity_title
    $items = VnbPlanItem::whereRaw('LOWER(activity_title) = ?', [strtolower($corrupted)])->get();
    
    foreach ($items as $item) {
        // Extract phase from description if exists, or use default
        $phase = '1-3'; // default
        if (preg_match('/Phase\s+(1-3|4-6|6\+)/', $item->activity_title, $matches)) {
            $phase = $matches[1];
        }
        
        // Reconstruct proper activity_title with phase
        $correctTitle = $correct . ' - Phase ' . $phase;
        
        $item->update(['activity_title' => $correctTitle]);
        $count++;
        echo "Fixed: '$corrupted' → '$correctTitle' (ID: {$item->id})\n";
    }
}

echo "\n✓ Total corrections: $count\n";
