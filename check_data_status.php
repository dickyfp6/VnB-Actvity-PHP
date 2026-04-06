<?php

require 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbPath = 'database/database.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);

// List all tables to see if backup exists
echo "=== Tables di database ===\n";
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "- $table\n";
}

// Check if vnb_plans_old exists
echo "\n=== Cek vnb_plans_old ===\n";
$oldExists = in_array('vnb_plans_old', $tables);
if ($oldExists) {
    $count = $pdo->query("SELECT COUNT(*) FROM vnb_plans_old")->fetchColumn();
    echo "✓ vnb_plans_old ada dengan $count data\n";
    
    // Get columns
    $cols = $pdo->query("PRAGMA table_info(vnb_plans_old)")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nKolom di vnb_plans_old:\n";
    foreach ($cols as $col) {
        echo "  - {$col['name']} ({$col['type']})\n";
    }
} else {
    echo "✗ vnb_plans_old tidak ada\n";
}

// Check vnb_plans
echo "\n=== Cek vnb_plans ===\n";
$newCount = $pdo->query("SELECT COUNT(*) FROM vnb_plans")->fetchColumn();
echo "vnb_plans punya $newCount data\n";

// Check vnb_plan_items
echo "\n=== Cek vnb_plan_items ===\n";
$itemsCount = $pdo->query("SELECT COUNT(*) FROM vnb_plan_items")->fetchColumn();
echo "vnb_plan_items punya $itemsCount data\n";

if ($oldExists && $newCount === 0 && $itemsCount === 0) {
    echo "\n⚠️  MAP: Data ada di vnb_plans_old tapi vnb_plans & vnb_plan_items kosong!\n";
    echo "Kita perlu restore...\n";
}
