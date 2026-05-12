<?php

// Debug helper: run one HRIS sync and print result or exception
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $controller = app()->make(App\Http\Controllers\Api\HrisController::class);

    $getMethod = new ReflectionMethod($controller, 'getSourceRowsBySystem');
    $getMethod->setAccessible(true);
    $rows = $getMethod->invoke($controller, 'HRIS');

    echo "HRIS source rows count: " . (is_countable($rows) ? count($rows) : ($rows instanceof \Illuminate\Support\Collection ? $rows->count() : 0)) . PHP_EOL;

    if (empty($rows) || ($rows instanceof \Illuminate\Support\Collection && $rows->isEmpty())) {
        echo "No HRIS rows found.\n";
        exit(0);
    }

    $row = $rows->first();

    $syncMethod = new ReflectionMethod($controller, 'syncRowToEmployee');
    $syncMethod->setAccessible(true);

    $result = $syncMethod->invoke($controller, $row);

    echo "Sync result:\n";
    var_export($result);
    echo PHP_EOL;

} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}

exit(0);
