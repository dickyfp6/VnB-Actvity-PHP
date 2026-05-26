<?php
/**
 * Script untuk menambahkan 'was_revised' flag ke semua existing snapshots
 * Menggunakan Tinker environment
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VnbPlanItem;

$updatedCount = 0;
$items = VnbPlanItem::whereNotNull('manager_review_snapshot')
    ->get();

foreach ($items as $item) {
    $snapshot = $item->manager_review_snapshot;
    if (!is_array($snapshot)) {
        continue;
    }

    $wasModified = false;
    foreach ($snapshot as $idx => &$entry) {
        if (!isset($entry['was_revised'])) {
            // Jika flag belum ada, set ke false (asumsi approval tanpa revisi)
            // Kalau ada yang perlu revisi, user bisa approve lagi
            $entry['was_revised'] = false;
            $wasModified = true;
        }
    }

    if ($wasModified) {
        $item->manager_review_snapshot = $snapshot;
        $item->save();
        $updatedCount++;
        echo "Updated item {$item->id}\n";
    }
}

echo "\nTotal items updated: $updatedCount\n";
