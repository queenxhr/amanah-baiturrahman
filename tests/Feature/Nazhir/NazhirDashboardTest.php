<?php

namespace Tests\Feature\Nazhir;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T03ProgramWakafFactory;
use Database\Factories\T04TransaksiFactory;
use Database\Factories\T05LaporanPenyaluranFactory;
use Database\Factories\T01RoleFactory;
use Laravel\Sanctum\Sanctum;

class NazhirDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
    }

    public function test_get_counters()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        T03ProgramWakafFactory::new()->count(3)->create(['dana_terkumpul' => 1000]);
        T04TransaksiFactory::new()->count(2)->create();

        $response = $this->getJson('/api/nazhir/counter');

        $response->assertStatus(200);
    }

    public function test_get_penyebaran_program()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        T04TransaksiFactory::new()->count(2)->create(['status_pembayaran' => 1]);

        $response = $this->getJson('/api/nazhir/penyebaran-program?bulan=' . now()->month . '&tahun=' . now()->year);

        $response->assertStatus(200);
    }

    public function test_get_trend_wakaf_per_tahun()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        T04TransaksiFactory::new()->count(5)->create([
            'status_pembayaran' => 1,
            'created_at' => now()->startOfYear()
        ]);

        $response = $this->getJson('/api/nazhir/trend-wakaf?tahun=' . now()->year);

        $response->assertStatus(200);
    }

    public function test_get_trend_wakaf_per_bulan_dengan_filter_bulan()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        T04TransaksiFactory::new()->create([
            'status_pembayaran' => 1,
            'created_at' => now()->startOfMonth()
        ]);

        $response = $this->getJson('/api/nazhir/trend-wakaf?tahun=' . now()->year . '&bulan=' . now()->month);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['tanggal', 'wakaf_terkumpul']
                     ]
                 ]);
    }
}
