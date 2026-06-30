<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\RepositoryInterfaces\Wakif\WakifDashboardRepositoryInterface;

$repo = app(WakifDashboardRepositoryInterface::class);

echo "Starting benchmark...\n";

$start = microtime(true);
$list = $repo->getProgramWakafList();
$end = microtime(true);

echo "Fetched " . count($list) . " programs.\n";
echo "Time taken: " . number_format(($end - $start) * 1000, 4) . " ms\n";
