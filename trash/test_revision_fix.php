<?php
// Direct SQL to test the fix
$host = 'localhost';
$db = 'vnb_wismilak';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    echo "\n===== TESTING REVISION SUBMISSION FIX =====\n\n";
    
    // 1. Check if we have any plans that can be used for testing
    $plans = $pdo->query('SELECT id, employee_id, status FROM vnb_plans LIMIT 5')->fetchAll();
    echo "📋 Available Plans:\n";
    foreach ($plans as $plan) {
        echo "  Plan ID: {$plan['id']}, Employee: {$plan['employee_id']}, Status: {$plan['status']}\n";
    }
    
    if (empty($plans)) {
        echo "❌ No plans found\n";
        exit;
    }
    
    $plan = $plans[0];
    
    // 2. Create a test revision
    echo "\n📝 Creating test revision for Plan ID: {$plan['id']}...\n";
    // Just use the first manager (ID=1 is typically a manager)
    $managerId = 1;
    
    $insertRevision = $pdo->prepare(
        'INSERT INTO vnb_plan_revisions (vnb_plan_id, revision_number, requested_by, revision_notes, status, requested_at, created_at, updated_at) 
         VALUES (?, 1, ?, ?, ?, NOW(), NOW(), NOW())'
    );
    $insertRevision->execute([
        $plan['id'],
        $managerId,
        'Test revision notes',
        'pending'
    ]);
    
    $revisionId = $pdo->lastInsertId();
    echo "✅ Revision created: ID={$revisionId}\n";
    
    // 3. Verify revision exists
    echo "\n🔍 Verifying revision...\n";
    $revision = $pdo->query("SELECT id, vnb_plan_id, status FROM vnb_plan_revisions WHERE id={$revisionId}")->fetch();
    if ($revision) {
        echo "✅ Revision verified: ID={$revision['id']}, Plan={$revision['vnb_plan_id']}, Status={$revision['status']}\n";
        echo "   This revision can now be used with submitRevisionChanges()\n";
        echo "   Route: POST /api/vnb-plans/{$plan['id']}/submit-revision/{$revisionId}\n";
    } else {
        echo "❌ Revision not found\n";
    }
    
    // 4. Check what items exist for this plan
    echo "\n📊 Plan Items available for revision:\n";
    $items = $pdo->query("SELECT id, plan_id, employee_id FROM vnb_plan_items WHERE plan_id={$plan['id']} LIMIT 3")->fetchAll();
    foreach ($items as $item) {
        echo "  Item ID: {$item['id']}, Plan: {$item['plan_id']}, Employee: {$item['employee_id']}\n";
    }
    
    echo "\n✅ SETUP COMPLETE - Ready to test revision submission\n";
    echo "\n===== END =====\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
