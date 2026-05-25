<?php

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthIntegrationTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'test@example.com', 'password' => 'password'])
            ->assertRedirect('/books');
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'test@example.com', 'password' => 'wrong-password'])
            ->assertSessionHasErrors();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $this->get('/books')->assertRedirect('/login');
        $this->get('/fines')->assertRedirect('/login');
        $this->get('/reservations')->assertRedirect('/login');
    }

    public function test_forgot_password_queues_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);

        $this->post('/forgot-password', ['email' => 'test@example.com'])
            ->assertSessionHasNoErrors();

        Mail::assertQueued(\App\Mail\PasswordRecoveryMail::class);
    }
}
