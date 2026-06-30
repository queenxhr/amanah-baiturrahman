<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = [
    't01_roles' => 'id_role',
    't02_users' => 'id_user',
    't03_program_wakaf' => 'id_program',
    't04_transaksi' => 'id_transaksi',
    't05_laporan_penyaluran' => 'id_laporan',
    't06_pencairan_dana' => 'id_pencairan',
];

if (config('database.default') === 'pgsql') {
    echo "Running sequence reset for PostgreSQL...\n";
    foreach ($tables as $table => $pk) {
        try {
            DB::statement("SELECT setval(pg_get_serial_sequence('$table', '$pk'), coalesce(max($pk), 1)) FROM $table");
            echo "Successfully reset sequence for table: $table\n";
        } catch (\Exception $e) {
            echo "Failed for table $table: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "Current database is not PostgreSQL (" . config('database.default') . "). No sequences to reset.\n";
}
