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

    public function test_nazhir_can_register()
    {
        $response = $this->postJson('/api/nazhir/signup', [
            'nama' => 'Nazhir Baru',
            'email' => 'nazhirbaru@example.com',
            'no_hp' => '081234567801',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'password' => 'P@ssword123',
            'password_confirmation' => 'P@ssword123'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('t02_users', [
            'email' => 'nazhirbaru@example.com',
            'id_role' => 1,
            'status' => 'pending'
        ]);
    }

    public function test_nazhir_cannot_register_with_duplicate_email()
    {
        T02UserFactory::new()->create([
            'email' => 'nazhir_dup@example.com',
            'no_hp' => '081234567802'
        ]);

        $response = $this->postJson('/api/nazhir/signup', [
            'nama' => 'Nazhir Baru',
            'email' => 'nazhir_dup@example.com',
            'no_hp' => '081234567803',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'password' => 'P@ssword123',
            'password_confirmation' => 'P@ssword123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }

    public function test_nazhir_cannot_register_with_duplicate_no_hp()
    {
        T02UserFactory::new()->create([
            'email' => 'nazhir_other@example.com',
            'no_hp' => '081234567804'
        ]);

        $response = $this->postJson('/api/nazhir/signup', [
            'nama' => 'Nazhir Baru',
            'email' => 'nazhir_new@example.com',
            'no_hp' => '081234567804',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'password' => 'P@ssword123',
            'password_confirmation' => 'P@ssword123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('no_hp');
    }
}
