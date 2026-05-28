<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('migrations')->updateOrInsert(
            ['id' => 1],
            ['migration' => '0001_01_01_000000_create_users_table', 'batch' => 1]
        );
        DB::table('migrations')->updateOrInsert(
            ['id' => 2],
            ['migration' => '0001_01_01_000001_create_cache_table', 'batch' => 1]
        );
        DB::table('migrations')->updateOrInsert(
            ['id' => 3],
            ['migration' => '0001_01_01_000002_create_jobs_table', 'batch' => 1]
        );
        DB::table('migrations')->updateOrInsert(
            ['id' => 4],
            ['migration' => '2025_08_14_170933_add_two_factor_columns_to_users_table', 'batch' => 1]
        );
    }
}