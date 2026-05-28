<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\T03ProgramWakaf;

try {
    $program = T03ProgramWakaf::create([
        'nama_program' => 'Test Program Baru',
        'deskripsi' => 'Deskripsi program',
        'target_dana' => 10000000,
        'status_program' => 1,
        'due_date' => '2026-12-31',
    ]);
    echo "Successfully created program with ID: " . $program->id_program . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
