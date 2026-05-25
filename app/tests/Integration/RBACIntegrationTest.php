<?php

namespace Tests\Integration;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;

class RBACIntegrationTest extends TestCase
{
    public function test_guest_cannot_access_any_protected_route(): void
    {
        $this->get('/books')->assertRedirect('/login');
        $this->get('/fines')->assertRedirect('/login');
        $this->get('/reservations')->assertRedirect('/login');
        $this->get('/loans')->assertRedirect('/login');
        $this->get('/users')->assertRedirect('/login');
    }

    public function test_customer_can_only_access_read_routes(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get('/books')->assertOk();
        $this->actingAs($customer)->get('/fines')->assertOk();
        $this->actingAs($customer)->get('/reservations')->assertOk();
    }

    public function test_customer_cannot_access_librarian_routes(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get('/loans')->assertForbidden();
        $this->actingAs($customer)->get('/books/create')->assertForbidden();
        $this->actingAs($customer)->post('/books', [])->assertForbidden();
    }

    public function test_librarian_can_access_book_and_loan_management(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)->get('/loans')->assertOk();
        $this->actingAs($librarian)->get('/books/create')->assertOk();
    }

    public function test_librarian_cannot_access_user_management(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)->get('/users')->assertForbidden();
        $this->actingAs($librarian)->post('/users', [])->assertForbidden();
    }

    public function test_admin_can_access_all_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/books')->assertOk();
        $this->actingAs($admin)->get('/loans')->assertOk();
        $this->actingAs($admin)->get('/users')->assertOk();
    }
}
