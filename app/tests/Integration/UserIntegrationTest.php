<?php

namespace Tests\Integration;

use App\Models\User;

class UserIntegrationTest extends TestCase
{
    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/users', [
                'name'                  => 'New Customer',
                'cpf'                   => '12345678901',
                'email'                 => 'new@example.com',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'role'                  => 'customer',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => 'customer']);
    }

    public function test_admin_can_update_user(): void
    {
        $admin    = User::factory()->admin()->create();
        $customer = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->put("/users/{$customer->id}", [
                'name' => 'New Name',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'name' => 'New Name']);
    }

    public function test_admin_can_soft_delete_user(): void
    {
        $admin    = User::factory()->admin()->create();
        $customer = User::factory()->create();

        $this->actingAs($admin)
            ->delete("/users/{$customer->id}")
            ->assertRedirect();

        $this->assertSoftDeleted('users', ['id' => $customer->id]);
    }

    public function test_admin_can_block_user(): void
    {
        $admin    = User::factory()->admin()->create();
        $customer = User::factory()->create(['blocked' => false]);

        $this->actingAs($admin)
            ->post("/users/{$customer->id}/block")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'blocked' => true]);
    }

    public function test_admin_can_unblock_user(): void
    {
        $admin    = User::factory()->admin()->create();
        $customer = User::factory()->blocked()->create();

        $this->actingAs($admin)
            ->post("/users/{$customer->id}/unblock")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'blocked' => false]);
    }

    public function test_librarian_cannot_manage_users(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)
            ->post('/users', [
                'name'                  => 'Test',
                'cpf'                   => '11122233344',
                'email'                 => 'test@test.com',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'role'                  => 'customer',
            ])
            ->assertForbidden();
    }

    public function test_customer_cannot_manage_users(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/users')
            ->assertForbidden();
    }
}
