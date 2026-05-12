<?php

// Debug helper: attempt batch HRIS/HRMS sync for pending rows and print detailed results
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $controller = app()->make(App\Http\Controllers\Api\HrisController::class);

    $buildMethod = new ReflectionMethod($controller, 'buildComparisonDataset');
    $buildMethod->setAccessible(true);
    $dataset = $buildMethod->invoke($controller);

    $pending = $dataset['pending'] ?? [];
    if (empty($pending) || (is_array($pending) && count($pending) === 0) || ($pending instanceof \Illuminate\Support\Collection && $pending->isEmpty())) {
        echo "No pending rows found.\n";
        exit(0);
    }

    $total = is_countable($pending) ? count($pending) : ($pending instanceof \Illuminate\Support\Collection ? $pending->count() : 0);
    echo "Pending rows: {$total}\n";

    $syncMethod = new ReflectionMethod($controller, 'syncRowToEmployee');
    $syncMethod->setAccessible(true);

    $synced = 0;
    $failed = 0;
    $errors = [];

    foreach ($pending as $row) {
        $result = $syncMethod->invoke($controller, (array) $row);
        if (!empty($result['success'])) {
            $synced++;
            echo "OK: {$row['employee_number']} - " . ($result['message'] ?? 'OK') . "\n";
            continue;
        }

        $failed++;
        $msg = $result['message'] ?? 'Unknown error';
        echo "FAIL: {$row['employee_number']} - {$msg}\n";
        $errors[] = [
            'id' => $row['id'] ?? null,
            'employee_number' => $row['employee_number'] ?? null,
            'message' => $msg,
        ];
    }

    echo "\nSummary: {$synced} succeeded, {$failed} failed.\n";
    if (!empty($errors)) {
        echo "Errors:\n";
        foreach ($errors as $e) {
            echo json_encode($e, JSON_UNESCAPED_UNICODE) . "\n";
        }
    }

} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

exit(0);
