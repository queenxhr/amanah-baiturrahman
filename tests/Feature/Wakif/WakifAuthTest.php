<?php

namespace Tests\Feature\Wakif;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T01RoleFactory;
use Database\Factories\T02UserFactory;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class WakifAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create standard roles
        T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
        T01RoleFactory::new()->create(['id_role' => 2, 'nama_role' => 'Wakif']);
    }

    public function test_wakif_can_login_with_valid_credentials()
    {
        $user = T02UserFactory::new()->create([
            'id_role' => 2,
            'email' => 'wakif@example.com',
            'password' => Hash::make('P@ssword123')
        ]);

        $response = $this->postJson('/api/wakif/login', [
            'email' => 'wakif@example.com',
            'password' => 'P@ssword123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'token',
                         'user'
                     ]
                 ]);
    }

    public function test_wakif_cannot_login_with_invalid_credentials()
    {
        $user = T02UserFactory::new()->create([
            'id_role' => 2,
            'email' => 'wakif@example.com',
            'password' => Hash::make('P@ssword123')
        ]);

        $response = $this->postJson('/api/wakif/login', [
            'email' => 'wakif@example.com',
            'password' => 'wrongpassword'
        ]);

        // Assuming standard validation error or specific message, check status 401/422
        $response->assertStatus(401);
    }

    public function test_wakif_can_register()
    {
        $response = $this->postJson('/api/wakif/signup', [
            'nama' => 'John Doe',
            'email' => 'johndoe@example.com',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1995-05-15',
            'password' => 'P@ssword123',
            'password_confirmation' => 'P@ssword123'
        ]);

        $response->assertStatus(201) // Or 200 depending on API implementation
                 ->assertJsonPath('data.user.email', 'johndoe@example.com');
        
        $this->assertDatabaseHas('t02_users', [
            'email' => 'johndoe@example.com',
            'id_role' => 2
        ]);
    }

    public function test_wakif_can_ubah_password()
    {
        $user = T02UserFactory::new()->create([
            'id_role' => 2,
            'password' => Hash::make('P@ssword123')
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->putJson('/api/wakif/ubah-password', [
            'old_password' => 'P@ssword123',
            'new_password' => 'N3wP@ssword!'
        ]);

        $response->assertStatus(200);

        $this->assertTrue(Hash::check('N3wP@ssword!', $user->fresh()->password));
    }

    public function test_wakif_can_logout()
    {
        $user = T02UserFactory::new()->create(['id_role' => 2]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/wakif/logout');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_wakif_cannot_register_with_invalid_email_format()
    {
        $response = $this->postJson('/api/wakif/signup', [
            'nama' => 'John Doe',
            'email' => 'invalid-email-format',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1995-05-15',
            'password' => 'P@ssword123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }

    public function test_wakif_cannot_register_with_duplicate_email()
    {
        T02UserFactory::new()->create([
            'email' => 'duplicate@example.com',
            'no_hp' => '081234567800'
        ]);

        $response = $this->postJson('/api/wakif/signup', [
            'nama' => 'John Doe',
            'email' => 'duplicate@example.com',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1995-05-15',
            'password' => 'P@ssword123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }

    public function test_wakif_cannot_register_with_duplicate_no_hp()
    {
        T02UserFactory::new()->create([
            'email' => 'other@example.com',
            'no_hp' => '081234567890'
        ]);

        $response = $this->postJson('/api/wakif/signup', [
            'nama' => 'John Doe',
            'email' => 'johndoe@example.com',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1995-05-15',
            'password' => 'P@ssword123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('no_hp');
    }
}
