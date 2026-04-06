<?php

require 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbPath = 'database/database.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);

// Check vnb_plans_new
echo "=== Cek vnb_plans_new ===\n";
$newCount = $pdo->query("SELECT COUNT(*) FROM vnb_plans_new")->fetchColumn();
echo "vnb_plans_new punya: $newCount data\n";

if ($newCount > 0) {
    echo "\n✓ Data ada! Nih datanya:\n";
    $data = $pdo->query("SELECT id, employee_id, title, status FROM vnb_plans_new LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($data as $row) {
        echo "  ID: {$row['id']}, Employee: {$row['employee_id']}, Title: {$row['title']}, Status: {$row['status']}\n";
    }
    
    // Check items
    $itemsCount = $pdo->query("SELECT COUNT(*) FROM vnb_plan_items")->fetchColumn();
    echo "\nvnb_plan_items: $itemsCount data\n";
    
    // Check revisions
    $revisionsCount = $pdo->query("SELECT COUNT(*) FROM vnb_plan_revisions")->fetchColumn();
    echo "vnb_plan_revisions: $revisionsCount data\n";
}

echo "\nCek kolom di vnb_plans_new:\n";
$cols = $pdo->query("PRAGMA table_info(vnb_plans_new)")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "  {$col['name']}\n";
}
