<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$program = App\Models\T03ProgramWakaf::first();
if ($program) {
    echo "Model attributes: " . json_encode($program) . "\n";
} else {
    echo "No program found.\n";
}
