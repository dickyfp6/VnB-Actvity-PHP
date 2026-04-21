<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Ambil semua vnb_plan_items
$items = DB::table('vnb_plan_items')
    ->select('id', 'activity_title', 'description', 'integration_1', 'integration_2', 'deliverables')
    ->get();

$stats = [
    'nyleneh' => 0,
    'sesuai' => 0,
    'updated' => 0
];

$nylenehItems = [];
$sesuaiItems = [];

foreach ($items as $item) {
    $currentDeliverable = trim($item->deliverables ?? '');
    $isAnomaly = false;
    
    // Cek apakah deliverables kosong atau aneh
    $lowerDeliverable = strtolower(trim($currentDeliverable));
    $emptyOrAnomaly = empty($currentDeliverable) 
        || $lowerDeliverable === '-'
        || $lowerDeliverable === 'apakah'
        || strpos($lowerDeliverable, 'apakah') === 0
        || $lowerDeliverable === 'aa'
        || $lowerDeliverable === 'hehe'
        || $lowerDeliverable === 'huhuu'
        || $lowerDeliverable === 'hmm'
        || $lowerDeliverable === 'boleh'
        || $lowerDeliverable === 'ba wismilak forever stay still'
        || $lowerDeliverable === 'collab'
        || strpos($lowerDeliverable, 'speak speak') !== false
        || strlen($currentDeliverable) <= 5;
    
    if ($emptyOrAnomaly) {
        $isAnomaly = true;
        
        // Generate deliverable yang masuk akal
        $newDeliverable = generateDeliverable(
            $item->activity_title,
            $item->description,
            $item->integration_1,
            $item->integration_2
        );
        
        // Update deliverables
        DB::table('vnb_plan_items')
            ->where('id', $item->id)
            ->update(['deliverables' => $newDeliverable]);
        
        $stats['updated']++;
        $stats['nyleneh']++;
        
        $nylenehItems[] = [
            'id' => $item->id,
            'title' => $item->activity_title,
            'old' => $currentDeliverable,
            'new' => $newDeliverable
        ];
    } else {
        $stats['sesuai']++;
        $sesuaiItems[] = [
            'id' => $item->id,
            'title' => $item->activity_title,
            'deliverable' => $currentDeliverable
        ];
    }
}

// Output Report
echo "\n";
echo "============================================================\n";
echo "           VNB DELIVERABLES SEEDER REPORT                  \n";
echo "============================================================\n\n";

echo "[RINGKASAN]\n";
echo "Items Sesuai    : {$stats['sesuai']} items\n";
echo "Items Nyleneh   : {$stats['nyleneh']} items (telah diperbarui)\n";
echo "Total Updated   : {$stats['updated']} records\n";

echo "\n------------------------------------------------------------\n\n";

if ($stats['nyleneh'] > 0) {
    echo "[ITEMS YANG DIPERBARUI - Nyleneh]\n\n";
    foreach ($nylenehItems as $idx => $item) {
        echo ($idx + 1) . ". [ID: {$item['id']}] {$item['title']}\n";
        echo "   Old: \"" . substr($item['old'], 0, 60) . (strlen($item['old']) > 60 ? '...' : '') . "\"\n";
        echo "   New: \"" . substr($item['new'], 0, 60) . (strlen($item['new']) > 60 ? '...' : '') . "\"\n\n";
    }
} else {
    echo "[ITEMS YANG DIPERBARUI]\n";
    echo "Tidak ada item yang terdeteksi sebagai nyleneh.\n\n";
}

echo "============================================================\n";
echo "Seeding selesai! Database telah diperbarui.\n";
echo "============================================================\n\n";

/**
 * Generate deliverable yang masuk akal
 */
function generateDeliverable($title, $description, $integration1, $integration2) {
    // Pisah description jika ada pemisah "|" atau "/"
    $descParts = array_map('trim', preg_split('/[\|\/]/', $description));
    
    // Jika ada integration_1 dan integration_2
    if (!empty($integration1) && !empty($integration2)) {
        return $integration1 . "\n---\n" . $integration2;
    }
    
    // Jika hanya integration_1
    if (!empty($integration1)) {
        return $integration1;
    }
    
    // Jika hanya integration_2
    if (!empty($integration2)) {
        return $integration2;
    }
    
    // Fallback ke description parts
    if (count($descParts) > 1) {
        return implode("\n---\n", $descParts);
    }
    
    // Fallback ke description aja
    if (!empty($description)) {
        return $description;
    }
    
    // Last resort
    return "Aktivitas: " . $title;
}
