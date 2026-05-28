<?php

namespace Tests\Feature\Nazhir;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T03ProgramWakafFactory;
use Database\Factories\T05LaporanPenyaluranFactory;
use Database\Factories\T01RoleFactory;
use Laravel\Sanctum\Sanctum;

class NazhirLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
    }

    public function test_get_status_laporan_program()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $program = T03ProgramWakafFactory::new()->create();
        T05LaporanPenyaluranFactory::new()->create(['id_program' => $program->id_program]);

        $response = $this->getJson('/api/nazhir/laporan/status/' . $program->id_program);

        $response->assertStatus(200);
    }

    public function test_create_laporan()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $program = T03ProgramWakafFactory::new()->create();

        $response = $this->postJson('/api/nazhir/laporan', [
            'id_program'       => $program->id_program,
            'judul_laporan'    => 'Laporan Coba',
            'keterangan'       => 'Keterangan laporan ini',
            'dana_disalurkan'  => 5000000,
            'penerima_manfaat' => 100
        ]);

        $response->assertStatus(201); // or 200
        $this->assertDatabaseHas('t05_laporan_penyaluran', [
            'id_program'    => $program->id_program,
            'judul_laporan' => 'Laporan Coba'
        ]);
    }

    public function test_update_laporan()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $laporan = T05LaporanPenyaluranFactory::new()->create();

        $response = $this->putJson('/api/nazhir/laporan/' . $laporan->id_laporan, [
            'id_program'       => $laporan->id_program,
            'judul_laporan'    => 'Laporan Update',
            'keterangan'       => 'Keterangan update',
            'dana_disalurkan'  => 10000000,
            'penerima_manfaat' => 200
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('t05_laporan_penyaluran', [
            'id_laporan'    => $laporan->id_laporan,
            'judul_laporan' => 'Laporan Update'
        ]);
    }

    public function test_get_laporan_by_program()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $program = T03ProgramWakafFactory::new()->create();
        T05LaporanPenyaluranFactory::new()->count(2)->create(['id_program' => $program->id_program]);

        $response = $this->getJson('/api/nazhir/laporan/' . $program->id_program);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id_laporan',
                             'id_program',
                             'judul_laporan',
                             'dana_disalurkan',
                             'penerima_manfaat',
                             'created_at'
                         ]
                     ]
                 ]);
    }
}
