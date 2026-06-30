<?php

namespace Tests\Feature\Superadmin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\T02UserFactory;
use Database\Factories\T01RoleFactory;
use Illuminate\Support\Facades\Mail;
use App\Mail\NazhirApprovedMail;

class SuperadminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
        T01RoleFactory::new()->create(['id_role' => 3, 'nama_role' => 'Superadmin']);
    }

    public function test_superadmin_can_approve_nazhir_and_email_is_sent()
    {
        Mail::fake();

        // Create Superadmin user
        $superadmin = T02UserFactory::new()->create([
            'id_role' => 3,
            'status' => 'active'
        ]);

        // Create pending Nazhir user
        $nazhir = T02UserFactory::new()->create([
            'id_role' => 1,
            'status' => 'pending',
            'email' => 'nazhir_pending@example.com'
        ]);

        $this->actingAs($superadmin);

        $response = $this->putJson("/api/superadmin/users/{$nazhir->id_user}/approve");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertEquals('active', $nazhir->fresh()->status);

        // Assert that the NazhirApprovedMail was sent to the Nazhir
        Mail::assertSent(NazhirApprovedMail::class, function ($mail) use ($nazhir) {
            return $mail->hasTo($nazhir->email) && (int)$mail->user->id_user === (int)$nazhir->id_user;
        });
    }
}
