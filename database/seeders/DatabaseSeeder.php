<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            T01RolesSeeder::class,
            MigrationsSeeder::class,
            T03ProgramWakafSeeder::class,
        ]);

        // User::factory(10)->create();

        if (!User::where('email', 'test@example.com')->exists()) {
            User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        if (!\App\Models\T02User::where('email', 'superadmin@example.com')->exists()) {
            \App\Models\T02User::create([
                'id_role' => 3,
                'nama' => 'Superadmin Amanah',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'),
                'no_hp' => '081234567890',
                'status' => 'active'
            ]);
        }

        if (config('database.default') === 'pgsql') {
            $tables = [
                't01_roles' => 'id_role',
                't02_users' => 'id_user',
                't03_program_wakaf' => 'id_program',
                't04_transaksi' => 'id_transaksi',
                't05_laporan_penyaluran' => 'id_laporan',
                't06_pencairan_dana' => 'id_pencairan',
            ];
            foreach ($tables as $table => $pk) {
                \Illuminate\Support\Facades\DB::statement("SELECT setval(pg_get_serial_sequence('$table', '$pk'), coalesce(max($pk), 1)) FROM $table");
            }
        }
    }
}
