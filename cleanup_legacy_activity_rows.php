<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apply = in_array('--apply', $argv, true);
$limit = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--limit=')) {
        $limit = max(1, (int) substr($arg, 8));
    }
}

$query = App\Models\VnbPlanItem::query()
    ->with('evidences')
    ->where('submission_status', 'completed')
    ->orderBy('id');

if ($limit !== null) {
    $query->limit($limit);
}

$candidates = $query->get();
$targets = [];

foreach ($candidates as $item) {
    $activityRows = is_array($item->activity_rows) ? $item->activity_rows : [];

    $hasMeaningfulActivity = false;

    $activityDescription = trim((string) ($item->activity_description ?? ''));
    $activityDate = trim((string) ($item->activity_date ?? ''));

    if ($activityDescription !== '' && $activityDescription !== '-') {
        $hasMeaningfulActivity = true;
    }

    if ($activityDate !== '' && $activityDate !== '-') {
        $hasMeaningfulActivity = true;
    }

    if (($item->evidences ?? collect())->isNotEmpty()) {
        $hasMeaningfulActivity = true;
    }

    foreach ($activityRows as $row) {
        $rowDescription = trim((string) ($row['activity_description'] ?? ''));
        $rowDate = trim((string) ($row['activity_date'] ?? ''));
        $rowStatus = strtolower((string) ($row['submission_status'] ?? 'draft'));

        if ($rowDescription !== '' && $rowDescription !== '-') {
            $hasMeaningfulActivity = true;
            break;
        }

        if ($rowDate !== '' && $rowDate !== '-') {
            $hasMeaningfulActivity = true;
            break;
        }

        if (in_array($rowStatus, ['waiting_approval', 'submitted', 'revision_required'], true)) {
            $hasMeaningfulActivity = true;
            break;
        }
    }

    if (!$hasMeaningfulActivity) {
        $targets[] = $item;
    }
}

echo "Legacy activity cleanup\n";
echo "Mode: " . ($apply ? 'APPLY' : 'PREVIEW') . "\n";
echo "Candidates checked: " . $candidates->count() . "\n";
echo "Rows to clean: " . count($targets) . "\n\n";

foreach (array_slice($targets, 0, 20) as $item) {
    echo sprintf(
        "- Item #%d | Plan #%d | %s\n",
        $item->id,
        $item->plan_id,
        $item->activity_title
    );
}

if (!$apply) {
    echo "\nRun again with --apply to persist the cleanup.\n";
    exit(0);
}

$updated = 0;

foreach ($targets as $item) {
    $activityRows = is_array($item->activity_rows) ? $item->activity_rows : [];
    $normalizedRows = [];

    foreach ($activityRows as $row) {
        $row['submission_status'] = 'draft';
        $row['submitted_at'] = null;
        $row['approved_functional_by'] = null;
        $row['approved_functional_at'] = null;
        $row['approved_operational_by'] = null;
        $row['approved_operational_at'] = null;
        $normalizedRows[] = $row;
    }

    $item->fill([
        'submission_status' => 'draft',
        'status' => 'draft',
        'submitted_at' => null,
        'completion_percentage' => 0,
        'activity_description' => null,
        'activity_date' => null,
        'activity_rows' => $normalizedRows ?: null,
    ]);
    $item->save();
    $updated++;
}

echo "\nUpdated rows: {$updated}\n";