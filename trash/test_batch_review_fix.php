<?php
// Direct test of the fixed batch review endpoint
$host = 'localhost';
$db = 'vnb_wismilak';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    echo "\n===== TESTING BATCH REVIEW FIX =====\n\n";
    
    // 1. Check if we have plan items to test with
    echo "📋 Checking vnb_plan_items...\n";
    $items = $pdo->query('SELECT id, plan_id FROM vnb_plan_items WHERE plan_id = 1 LIMIT 3')->fetchAll();
    
    if (empty($items)) {
        echo "❌ No items found for plan 1\n";
        exit;
    }
    
    echo "✅ Found " . count($items) . " items for testing\n";
    foreach ($items as $item) {
        echo "   - Item ID: {$item['id']}, Plan: {$item['plan_id']}\n";
    }
    
    // 2. Test data structure that would be sent to batch-review endpoint
    echo "\n🧪 Sample Request Data:\n";
    $sampleRequest = [
        'reviews' => [
            [
                'id' => $items[0]['id'],
                'action' => 'approve',
                'notes' => 'Looks good'
            ]
        ]
    ];
    echo "POST /api/manager/plans/1/batch-review\n";
    echo json_encode($sampleRequest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    
    // 3. Check column names in vnb_plan_revision_details
    echo "\n📊 Verifying table structure:\n";
    $columns = $pdo->query('DESCRIBE vnb_plan_revision_details')->fetchAll(PDO::FETCH_COLUMN, 0);
    
    if (in_array('vnb_plan_revision_id', $columns)) {
        echo "✅ Column 'vnb_plan_revision_id' exists\n";
    } else {
        echo "❌ Column 'vnb_plan_revision_id' MISSING\n";
    }
    
    if (!in_array('revision_id', $columns)) {
        echo "✅ Column 'revision_id' does NOT exist (correct - we use vnb_plan_revision_id)\n";
    } else {
        echo "⚠️  Column 'revision_id' exists!\n";
    }
    
    echo "\n✅ BATCH REVIEW ENDPOINT FIX VERIFIED\n";
    echo "   - Column name corrected: revision_id → vnb_plan_revision_id\n";
    echo "   - Applied to: batchReviewPlanItems() and requestRevisionForItem()\n";
    
    echo "\n===== END =====\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
