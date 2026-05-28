<?php

namespace Tests\Feature\Nazhir;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T03ProgramWakafFactory;
use Database\Factories\T01RoleFactory;
use Laravel\Sanctum\Sanctum;

class NazhirProgramTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
    }

    public function test_get_list_program()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        T03ProgramWakafFactory::new()->count(3)->create();

        $response = $this->getJson('/api/nazhir/program');

        $response->assertStatus(200);
    }

    public function test_create_program()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $response = $this->postJson('/api/nazhir/program', [
            'nama_program' => 'Program Coba',
            'deskripsi' => 'Deskripsi Program Coba',
            'target_dana' => 100000000,
            'due_date' => '2026-12-31'
            // status default 1, terkumpul 0
        ]);

        $response->assertStatus(201); // or 200
        $this->assertDatabaseHas('t03_program_wakaf', [
            'nama_program' => 'Program Coba'
        ]);
    }

    public function test_update_program()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $program = T03ProgramWakafFactory::new()->create();

        $response = $this->putJson('/api/nazhir/program/' . $program->id_program, [
            'nama_program' => 'Program Update',
            'target_dana' => 50000000,
            'due_date' => '2027-01-01',
            'status_program' => 0
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('t03_program_wakaf', [
            'id_program' => $program->id_program,
            'nama_program' => 'Program Update'
        ]);
    }
}
