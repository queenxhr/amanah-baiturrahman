<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class T01RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('t01_roles')->updateOrInsert(
            ['id_role' => 1],
            ['nama_role' => 'Nazhir', 'created_at' => '2026-04-28 14:28:07.483366', 'edited_at' => '2026-04-28 14:28:07.483366']
        );
        DB::table('t01_roles')->updateOrInsert(
            ['id_role' => 2],
            ['nama_role' => 'Wakif', 'created_at' => '2026-04-28 14:28:07.483366', 'edited_at' => '2026-04-28 14:28:07.483366']
        );
        DB::table('t01_roles')->updateOrInsert(
            ['id_role' => 3],
            ['nama_role' => 'Superadmin', 'created_at' => '2026-04-28 14:28:07.483366', 'edited_at' => '2026-04-28 14:28:07.483366']
        );
    }
}