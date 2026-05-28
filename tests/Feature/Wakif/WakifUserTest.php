<?php

namespace Tests\Feature\Wakif;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T01RoleFactory;
use Laravel\Sanctum\Sanctum;

class WakifUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        T01RoleFactory::new()->create(['id_role' => 2, 'nama_role' => 'Wakif']);
    }

    public function test_get_profile()
    {
        $user = T02UserFactory::new()->create(['id_role' => 2]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/wakif/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'nama',
                         'email',
                         'no_hp',
                         'jenis_kelamin',
                         'alamat',
                         'tanggal_lahir'
                     ]
                 ]);
    }

    public function test_update_profile()
    {
        $user = T02UserFactory::new()->create(['id_role' => 2]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->putJson('/api/wakif/user', [
            'nama' => 'Update Name',
            'email' => 'update@example.com',
            'no_hp' => '0899999999',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat Baru',
            'tanggal_lahir' => '1990-01-01'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('t02_users', [
            'id_user' => $user->id_user,
            'nama' => 'Update Name'
        ]);
    }
}
