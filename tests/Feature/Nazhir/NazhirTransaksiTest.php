<?php

namespace Tests\Feature\Nazhir;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T03ProgramWakafFactory;
use Database\Factories\T04TransaksiFactory;
use Database\Factories\T01RoleFactory;
use Laravel\Sanctum\Sanctum;

class NazhirTransaksiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
    }

    public function test_get_list_transaksi()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        T04TransaksiFactory::new()->count(3)->create();

        $response = $this->getJson('/api/nazhir/transaksi');

        $response->assertStatus(200);
    }

    public function test_get_bukti_pembayaran()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $transaksi = T04TransaksiFactory::new()->create(['bukti_pembayaran' => 'testbukti.jpg']);

        $response = $this->getJson('/api/nazhir/transaksi/' . $transaksi->id_transaksi . '/bukti');

        $response->assertStatus(200)
                 ->assertJsonPath('data.bukti_pembayaran', 'testbukti.jpg');
    }

    public function test_approve_transaksi()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $transaksi = T04TransaksiFactory::new()->create(['status_pembayaran' => 0]);

        $response = $this->putJson('/api/nazhir/transaksi/' . $transaksi->id_transaksi . '/approve', [
            'status_pembayaran' => 1
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('t04_transaksi', [
            'id_transaksi'      => $transaksi->id_transaksi,
            'status_pembayaran' => 1
        ]);
    }

    public function test_export_csv_transaksi()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        T04TransaksiFactory::new()->count(5)->create();

        $response = $this->getJson('/api/nazhir/transaksi/export-csv');

        // CSV endpoints return 200 with streamed content
        $response->assertStatus(200);
    }
}
