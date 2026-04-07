<?php
// Direct SQL to check revisions without Tinker
$host = 'localhost';
$db = 'vnb_wismilak';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    echo "\n===== VNB_PLAN_REVISIONS STATUS =====\n";
    $revisions = $pdo->query('SELECT id, vnb_plan_id, revision_number, status, requested_at FROM vnb_plan_revisions')->fetchAll();
    
    if (count($revisions) === 0) {
        echo "⚠️  No revision records in database\n";
    } else {
        echo "Total revisions: " . count($revisions) . "\n\n";
        foreach ($revisions as $rev) {
            echo "  ID: {$rev['id']}, Plan: {$rev['vnb_plan_id']}, RevNum: {$rev['revision_number']}, Status: {$rev['status']}\n";
        }
    }
    
    echo "\n===== VNB_PLAN_REVISION_DETAILS STATUS =====\n";
    $details = $pdo->query('SELECT COUNT(*) as cnt FROM vnb_plan_revision_details')->fetch();
    echo "Total detail records: {$details['cnt']}\n";
    
   if ($details['cnt'] > 0) {
        $detailSample = $pdo->query('SELECT id, vnb_plan_revision_id, vnb_plan_item_id FROM vnb_plan_revision_details LIMIT 5')->fetchAll();
        echo "Sample details:\n";
        foreach ($detailSample as $d) {
            echo "  ID: {$d['id']}, Revision: {$d['vnb_plan_revision_id']}, Item: {$d['vnb_plan_item_id']}\n";
        }
    }
    
    echo "\n===== END =====\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
