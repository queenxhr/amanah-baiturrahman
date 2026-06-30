<?php

namespace Tests\Feature\Wakif;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T03ProgramWakafFactory;
use Database\Factories\T04TransaksiFactory;
use Database\Factories\T01RoleFactory;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WakifTransaksiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        T01RoleFactory::new()->create(['id_role' => 2, 'nama_role' => 'Wakif']);
        Storage::fake('public');
    }

    public function test_create_transaksi_guest()
    {
        $program = T03ProgramWakafFactory::new()->create();
        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->postJson('/api/wakif/transaksi/guest', [
            'nama'             => 'Guest Donatur',
            'no_hp'            => '081234567890',
            'email'            => 'donatur@example.com',
            'id_program'       => $program->id_program,
            'nominal'          => 50000,
            'bukti_pembayaran' => $file,
            'pesan_doa'        => 'Semoga berkah'
        ]);

        $response->assertStatus(201); // Or 200, depending on created status
    }

    public function test_create_transaksi_user()
    {
        $user = T02UserFactory::new()->create(['id_role' => 2]);
        Sanctum::actingAs($user, ['*']);

        $program = T03ProgramWakafFactory::new()->create();
        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->postJson('/api/wakif/transaksi/user', [
            // nama auto-filled from user account
            'email'            => 'wakif@example.com',
            'id_program'       => $program->id_program,
            'nominal'          => 100000,
            'bukti_pembayaran' => $file,
            'pesan_doa'        => 'Semoga amanah'
        ]);

        $response->assertStatus(201); // or 200
    }

    public function test_get_detail_pembayaran()
    {
        $user = T02UserFactory::new()->create(['id_role' => 2]);
        Sanctum::actingAs($user, ['*']);

        $transaksi = T04TransaksiFactory::new()->create(['id_user' => $user->id_user]);

        $response = $this->getJson('/api/wakif/detail-pembayaran/' . $transaksi->id_transaksi);
        $response->assertStatus(200);
    }

    public function test_get_riwayat_transaksi()
    {
        $user = T02UserFactory::new()->create(['id_role' => 2]);
        Sanctum::actingAs($user, ['*']);

        T04TransaksiFactory::new()->create(['id_user' => $user->id_user]);
        T04TransaksiFactory::new()->create(['id_user' => $user->id_user]);

        $response = $this->getJson('/api/wakif/transaksi/riwayat');
        $response->assertStatus(200);
    }

    public function test_get_transaksi_by_id()
    {
        $user = T02UserFactory::new()->create(['id_role' => 2]);
        Sanctum::actingAs($user, ['*']);

        $transaksi = T04TransaksiFactory::new()->create(['id_user' => $user->id_user]);

        $response = $this->getJson('/api/wakif/transaksi/' . $transaksi->id_transaksi);
        $response->assertStatus(200);
    }
}
