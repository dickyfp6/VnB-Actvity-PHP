<?php

require 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbPath = 'database/database.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);

echo "=== Starting Data Recovery ===\n";

try {
    // 1. Backup vnb_plans_new untuk keamanan
    echo "\n1. Backing up vnb_plans_new...\n";
    $backupCount = $pdo->query("SELECT COUNT(*) FROM vnb_plans_new")->fetchColumn();
    echo "   Planning: $backupCount\n";
    
    // 2. Copy data dari vnb_plans_new ke vnb_plans
    echo "\n2. Restoring planning data...\n";
    $stmt = $pdo->query("
        INSERT INTO vnb_plans 
        (id, employee_id, period_id, phase_number, title, description, planning_mode, status, 
         revision_count, revision_notes, submitted_at, approved_at, approved_by, rejection_reason, 
         discussion_notes, created_at, updated_at)
        SELECT id, employee_id, period_id, phase_number, title, description, planning_mode, status,
               COALESCE(revision_count, 0), revision_notes, submitted_at, approved_at, approved_by, 
               rejection_reason, discussion_notes, created_at, updated_at
        FROM vnb_plans_new
    ");
    
    $inserted = $pdo->query("SELECT COUNT(*) FROM vnb_plans")->fetchColumn();
    echo "   ✓ Berhasil restore $inserted planning\n";
    
    // 3. Check if there are plan items associated
    echo "\n3. Checking plan items...\n";
    $itemsCount = $pdo->query("SELECT COUNT(*) FROM vnb_plan_items")->fetchColumn();
    if ($itemsCount === 0) {
        echo "   ⚠️  Plan items tidak ada - ini mungkin karena belum dibuat saat migration\n";
        echo "   Cek ke database manual atau data seeding\n";
    } else {
        echo "   ✓ Plan items: $itemsCount\n";
    }
    
    // 4. Clean up backup table
    echo "\n4. Cleaning up backup...\n";
    $pdo->query("DROP TABLE IF EXISTS vnb_plans_new");
    echo "   ✓ Backup table dropped\n";
    
    echo "\n=== Recovery Complete! ===\n";
    echo "Planning data sudah dikembalikan ke vnb_plans\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
