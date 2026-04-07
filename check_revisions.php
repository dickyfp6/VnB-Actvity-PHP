<?php
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\VnbPlan;
use App\Models\VnbPlanRevision;

echo "\n===== VNB PLANS AND REVISIONS STATUS =====\n\n";

// Check all plans
$plans = VnbPlan::with(['employee', 'revisions'])->get();
foreach ($plans as $plan) {
    $empName = $plan->employee ? $plan->employee->name : 'N/A';
    echo "📋 Plan ID: {$plan->id}\n";
    echo "   Employee: {$empName}\n";
    echo "   Status: {$plan->status}\n";
    echo "   Revisions: " . $plan->revisions->count() . "\n";
    
    if ($plan->revisions->count() > 0) {
        foreach ($plan->revisions as $rev) {
            echo "      - Revision #{$rev->id}: status={$rev->status}, revision_number={$rev->revision_number}\n";
            $detailCount = DB::table('vnb_plan_revision_details')
                ->where('vnb_plan_revision_id', $rev->id)
                ->count();
            echo "        Details records: {$detailCount}\n";
        }
    }
    echo "\n";
}

// Check if there are orphaned revisions in database
echo "\n===== ORPHANED REVISIONS (NOT LINKED TO PLANS) =====\n";
$orphans = DB::table('vnb_plan_revisions')
    ->leftJoin('vnb_plans', 'vnb_plan_revisions.vnb_plan_id', '=', 'vnb_plans.id')
    ->whereNull('vnb_plans.id')
    ->select('vnb_plan_revisions.*')
    ->get();

if (count($orphans) > 0) {
    foreach ($orphans as $orphan) {
        echo "❌ Orphaned Revision: {$orphan->id}\n";
    }
} else {
    echo "✅ No orphaned revisions found\n";
}

// Show raw DB query to manually check data
echo "\n===== VNB_PLAN_REVISIONS TABLE DATA =====\n";
$revisions = DB::table('vnb_plan_revisions')->get();
if (count($revisions) === 0) {
    echo "⚠️  No revision records in database\n";
} else {
    foreach ($revisions as $rev) {
        echo "ID: {$rev->id}, Plan: {$rev->vnb_plan_id}, Status: {$rev->status}, Revision#: {$rev->revision_number}\n";
    }
}

echo "\n===== END =====\n";
