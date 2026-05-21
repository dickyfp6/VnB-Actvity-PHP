<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$item = \App\Models\VnbPlanItem::where('submission_status', 'completed')->first();
if ($item && $item->manager_review_snapshot) {
    echo "Snapshot:\n";
    echo json_encode($item->manager_review_snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\nChecking was_revised in each entry:\n";
    foreach ($item->manager_review_snapshot as $idx => $entry) {
        echo "Entry $idx: was_revised = " . ($entry['was_revised'] ?? 'NOT SET') . "\n";
    }
} else {
    echo "No completed item with snapshot found\n";
}
