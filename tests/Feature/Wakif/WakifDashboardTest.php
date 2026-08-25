<?php

namespace Tests\Feature\Wakif;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T03ProgramWakafFactory;
use Database\Factories\T04TransaksiFactory;
use Database\Factories\T05LaporanPenyaluranFactory;
use Database\Factories\T01RoleFactory;

class WakifDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup initial role
        T01RoleFactory::new()->create(['id_role' => 2, 'nama_role' => 'Wakif']);
    }

    public function test_get_counter()
    {
        // Add 2 valid program
        T03ProgramWakafFactory::new()->count(2)->create(['dana_terkumpul' => 500000]);
        // Add 3 transaction -> 3 donaturs
        T04TransaksiFactory::new()->count(3)->create(['nominal' => 100000]);

        $response = $this->getJson('/api/wakif/counter');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'program',
                         'wakaf_terkumpul',
                         'donatur',
                         'penerima_manfaat'
                     ]
                 ]);
    }

    public function test_get_program_wakaf_list()
    {
        T03ProgramWakafFactory::new()->count(5)->create();

        $response = $this->getJson('/api/wakif/program-wakaf');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'nama_program',
                             'deskripsi',
                             'target_dana',
                             'dana_terkumpul',
                             'due_date'
                         ]
                     ]
                 ]);
    }

    public function test_get_program_deskripsi()
    {
        $program = T03ProgramWakafFactory::new()->create([
            'nama_program' => 'Program A',
            'deskripsi' => 'Deskripsi panjang lebar.'
        ]);

        $response = $this->getJson('/api/wakif/program-wakaf/' . $program->id_program . '/deskripsi');

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'nama_program' => 'Program A',
                         'deskripsi' => 'Deskripsi panjang lebar.'
                     ]
                 ]);
    }

    public function test_get_counter_donatur()
    {
        $response = $this->getJson('/api/wakif/counter-donatur');

        // Based on endpoint implementation, structure can be specific, usually just the number
        $response->assertStatus(200);
    }

    public function test_get_donatur_by_program()
    {
        $program = T03ProgramWakafFactory::new()->create();
        T04TransaksiFactory::new()->count(3)->create([
            'id_program' => $program->id_program
        ]);

        $response = $this->getJson('/api/wakif/program-wakaf/' . $program->id_program . '/donatur');

        $response->assertStatus(200);
    }

    public function test_get_berita_laporan()
    {
        $program = T03ProgramWakafFactory::new()->create();
        T05LaporanPenyaluranFactory::new()->count(2)->create([
            'id_program' => $program->id_program
        ]);

        $response = $this->getJson('/api/wakif/berita-laporan');

        $response->assertStatus(200);
    }

    public function test_get_penyebaran_program()
    {
        $response = $this->getJson('/api/wakif/penyebaran-program');
        $response->assertStatus(200);
    }

    public function test_get_trend_wakaf_per_tahun()
    {
        T04TransaksiFactory::new()->create(['status_pembayaran' => 1, 'created_at' => now()->subMonths(1)]);
        $response = $this->getJson('/api/wakif/trend-wakaf?tahun=' . now()->year);
        $response->assertStatus(200);
    }

    public function test_get_trend_wakaf_per_bulan_dengan_filter_bulan()
    {
        T04TransaksiFactory::new()->create([
            'status_pembayaran' => 1,
            'created_at' => now()->startOfMonth()
        ]);
        $response = $this->getJson('/api/wakif/trend-wakaf?tahun=' . now()->year . '&bulan=' . now()->month);
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['tanggal', 'wakaf_terkumpul']
                     ]
                 ]);
    }
}
