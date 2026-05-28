<?php

namespace Tests\Feature\Nazhir;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T01RoleFactory;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class NazhirAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create Nazhir role
        T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
    }

    public function test_nazhir_can_login()
    {
        $nazhir = T02UserFactory::new()->create([
            'id_role' => 1,
            'email'   => 'nazhir@example.com',
            'password'=> Hash::make('nazhirpass')
        ]);

        $response = $this->postJson('/api/nazhir/login', [
            'email'    => 'nazhir@example.com',
            'password' => 'nazhirpass'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'token',
                         'user'
                     ]
                 ]);
    }

    public function test_nazhir_can_logout()
    {
        $nazhir = T02UserFactory::new()->create(['id_role' => 1]);
        Sanctum::actingAs($nazhir, ['*']);

        $response = $this->postJson('/api/nazhir/logout');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}
