<?php

namespace Tests\Integration;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;

/**
 * Comprehensive RBAC test: every route × every role.
 *
 * Expected responses:
 *   200/OK   – route is accessible and returns content
 *   302      – redirect (login redirect for guests, or success/error redirect for actions)
 *   403      – forbidden (wrong role)
 *
 * For mutating routes (POST/PUT/DELETE) we only assert access control, not
 * business-logic outcomes (those are covered by the feature integration tests).
 */
class RouteAccessControlTest extends TestCase
{
    private User $admin;
    private User $librarian;
    private User $customer;
    private Book $book;
    private Loan $loan;
    private Reservation $reservation;
    private Fine $fine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin     = User::factory()->admin()->create();
        $this->librarian = User::factory()->librarian()->create();
        $this->customer  = User::factory()->create();

        $this->book        = Book::factory()->create(['stock' => 5]);
        $this->loan        = Loan::factory()->create([
            'book_id' => $this->book->id,
            'user_id' => $this->customer->id,
        ]);
        $this->reservation = Reservation::factory()->create([
            'book_id' => $this->book->id,
            'user_id' => $this->customer->id,
            'status'  => 'pending',
        ]);
        $this->fine = Fine::factory()->create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->customer->id,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Guest-only routes: authenticated users are redirected away
    // ──────────────────────────────────────────────────────────────────────────

    public function test_login_page_accessible_to_guest(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_login_page_redirects_authenticated_users(): void
    {
        // Authenticated users are redirected away from guest-only routes
        $this->actingAs($this->customer)->get('/login')->assertRedirect();
        $this->actingAs($this->librarian)->get('/login')->assertRedirect();
        $this->actingAs($this->admin)->get('/login')->assertRedirect();
    }

    public function test_register_page_accessible_to_guest(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_forgot_password_page_accessible_to_guest(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_reset_password_page_accessible_to_guest(): void
    {
        $this->get('/reset-password')->assertOk();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Authenticated routes — all roles can access
    // ──────────────────────────────────────────────────────────────────────────

    public function test_books_index_requires_auth(): void
    {
        $this->get('/books')->assertRedirect('/login');
    }

    public function test_books_index_accessible_by_all_roles(): void
    {
        $this->actingAs($this->customer)->get('/books')->assertOk();
        $this->actingAs($this->librarian)->get('/books')->assertOk();
        $this->actingAs($this->admin)->get('/books')->assertOk();
    }

    public function test_book_show_requires_auth(): void
    {
        $this->get("/books/{$this->book->id}")->assertRedirect('/login');
    }

    public function test_book_show_accessible_by_all_roles(): void
    {
        $this->actingAs($this->customer)->get("/books/{$this->book->id}")->assertOk();
        $this->actingAs($this->librarian)->get("/books/{$this->book->id}")->assertOk();
        $this->actingAs($this->admin)->get("/books/{$this->book->id}")->assertOk();
    }

    public function test_reservations_index_requires_auth(): void
    {
        $this->get('/reservations')->assertRedirect('/login');
    }

    public function test_reservations_index_accessible_by_all_roles(): void
    {
        $this->actingAs($this->customer)->get('/reservations')->assertOk();
        $this->actingAs($this->librarian)->get('/reservations')->assertOk();
        $this->actingAs($this->admin)->get('/reservations')->assertOk();
    }

    public function test_fines_index_requires_auth(): void
    {
        $this->get('/fines')->assertRedirect('/login');
    }

    public function test_fines_index_accessible_by_all_roles(): void
    {
        $this->actingAs($this->customer)->get('/fines')->assertOk();
        $this->actingAs($this->librarian)->get('/fines')->assertOk();
        $this->actingAs($this->admin)->get('/fines')->assertOk();
    }

    public function test_logout_requires_auth(): void
    {
        $this->post('/logout')->assertRedirect('/login');
    }

    public function test_logout_works_for_all_roles(): void
    {
        // Each actingAs creates a fresh session
        $this->actingAs($this->customer)->post('/logout')->assertRedirect();
        $this->actingAs($this->librarian)->post('/logout')->assertRedirect();
        $this->actingAs($this->admin)->post('/logout')->assertRedirect();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Librarian + Admin routes: customers must receive 403
    // ──────────────────────────────────────────────────────────────────────────

    public function test_book_create_requires_librarian_or_admin(): void
    {
        $this->get('/books/create')->assertRedirect('/login');
        $this->actingAs($this->customer)->get('/books/create')->assertForbidden();
        $this->actingAs($this->librarian)->get('/books/create')->assertOk();
        $this->actingAs($this->admin)->get('/books/create')->assertOk();
    }

    public function test_book_store_requires_librarian_or_admin(): void
    {
        $payload = ['title' => 'Test Book', 'author' => 'Author', 'media' => 'physical', 'stock' => 1];

        $this->post('/books', $payload)->assertRedirect('/login');
        $this->actingAs($this->customer)->post('/books', $payload)->assertForbidden();
        $this->actingAs($this->librarian)->post('/books', $payload)->assertRedirect();
        $this->actingAs($this->admin)->post('/books', $payload)->assertRedirect();
    }

    public function test_book_edit_requires_librarian_or_admin(): void
    {
        $this->get("/books/{$this->book->id}/edit")->assertRedirect('/login');
        $this->actingAs($this->customer)->get("/books/{$this->book->id}/edit")->assertForbidden();
        $this->actingAs($this->librarian)->get("/books/{$this->book->id}/edit")->assertOk();
        $this->actingAs($this->admin)->get("/books/{$this->book->id}/edit")->assertOk();
    }

    public function test_book_update_requires_librarian_or_admin(): void
    {
        $payload = ['title' => 'Updated', 'author' => 'Author', 'media' => 'physical', 'stock' => 2];

        $this->put("/books/{$this->book->id}", $payload)->assertRedirect('/login');
        $this->actingAs($this->customer)->put("/books/{$this->book->id}", $payload)->assertForbidden();
        $this->actingAs($this->librarian)->put("/books/{$this->book->id}", $payload)->assertRedirect();
        $this->actingAs($this->admin)->put("/books/{$this->book->id}", $payload)->assertRedirect();
    }

    public function test_book_destroy_requires_librarian_or_admin(): void
    {
        $bookA = Book::factory()->create();
        $bookB = Book::factory()->create();

        $this->delete("/books/{$bookA->id}")->assertRedirect('/login');
        $this->actingAs($this->customer)->delete("/books/{$bookA->id}")->assertForbidden();
        $this->actingAs($this->librarian)->delete("/books/{$bookA->id}")->assertRedirect();
        $this->actingAs($this->admin)->delete("/books/{$bookB->id}")->assertRedirect();
    }

    public function test_loans_index_requires_librarian_or_admin(): void
    {
        $this->get('/loans')->assertRedirect('/login');
        $this->actingAs($this->customer)->get('/loans')->assertForbidden();
        $this->actingAs($this->librarian)->get('/loans')->assertOk();
        $this->actingAs($this->admin)->get('/loans')->assertOk();
    }

    public function test_loan_show_requires_librarian_or_admin(): void
    {
        $this->get("/loans/{$this->loan->id}")->assertRedirect('/login');
        $this->actingAs($this->customer)->get("/loans/{$this->loan->id}")->assertForbidden();
        $this->actingAs($this->librarian)->get("/loans/{$this->loan->id}")->assertOk();
        $this->actingAs($this->admin)->get("/loans/{$this->loan->id}")->assertOk();
    }

    public function test_loan_store_requires_librarian_or_admin(): void
    {
        $book     = Book::factory()->create(['stock' => 3]);
        $borrower = User::factory()->create();
        $payload  = [
            'book_id'  => $book->id,
            'user_id'  => $borrower->id,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ];

        $this->post('/loans', $payload)->assertRedirect('/login');
        $this->actingAs($this->customer)->post('/loans', $payload)->assertForbidden();
        $this->actingAs($this->librarian)->post('/loans', $payload)->assertRedirect();
    }

    public function test_loan_renew_requires_librarian_or_admin(): void
    {
        $loan = Loan::factory()->create(['due_date' => now()->addDays(5)]);

        $this->post("/loans/{$loan->id}/renew")->assertRedirect('/login');
        $this->actingAs($this->customer)->post("/loans/{$loan->id}/renew")->assertForbidden();
        $this->actingAs($this->librarian)->post("/loans/{$loan->id}/renew")->assertRedirect();
        $this->actingAs($this->admin)->post("/loans/{$loan->id}/renew")->assertRedirect();
    }

    public function test_loan_return_requires_librarian_or_admin(): void
    {
        $book = Book::factory()->create(['stock' => 2]);
        $loanA = Loan::factory()->create(['book_id' => $book->id, 'due_date' => now()->addDays(5)]);
        $loanB = Loan::factory()->create(['book_id' => $book->id, 'due_date' => now()->addDays(5)]);

        $this->post("/loans/{$loanA->id}/return")->assertRedirect('/login');
        $this->actingAs($this->customer)->post("/loans/{$loanA->id}/return")->assertForbidden();
        $this->actingAs($this->librarian)->post("/loans/{$loanA->id}/return")->assertRedirect();
        $this->actingAs($this->admin)->post("/loans/{$loanB->id}/return")->assertRedirect();
    }

    public function test_fine_pay_requires_librarian_or_admin(): void
    {
        $fineA = Fine::factory()->create();
        $fineB = Fine::factory()->create();

        $this->post("/fines/{$fineA->id}/pay")->assertRedirect('/login');
        $this->actingAs($this->customer)->post("/fines/{$fineA->id}/pay")->assertForbidden();
        $this->actingAs($this->librarian)->post("/fines/{$fineA->id}/pay")->assertRedirect();
        $this->actingAs($this->admin)->post("/fines/{$fineB->id}/pay")->assertRedirect();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Admin-only routes: guests, customers, and librarians must receive 403
    // ──────────────────────────────────────────────────────────────────────────

    public function test_users_index_requires_admin(): void
    {
        $this->get('/users')->assertRedirect('/login');
        $this->actingAs($this->customer)->get('/users')->assertForbidden();
        $this->actingAs($this->librarian)->get('/users')->assertForbidden();
        $this->actingAs($this->admin)->get('/users')->assertOk();
    }

    public function test_user_create_requires_admin(): void
    {
        $this->get('/users/create')->assertRedirect('/login');
        $this->actingAs($this->customer)->get('/users/create')->assertForbidden();
        $this->actingAs($this->librarian)->get('/users/create')->assertForbidden();
        $this->actingAs($this->admin)->get('/users/create')->assertOk();
    }

    public function test_user_store_requires_admin(): void
    {
        $payload = [
            'name'                  => 'New User',
            'cpf'                   => '12345678901',
            'email'                 => 'newuser@example.com',
            'role'                  => 'customer',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post('/users', $payload)->assertRedirect('/login');
        $this->actingAs($this->customer)->post('/users', $payload)->assertForbidden();
        $this->actingAs($this->librarian)->post('/users', $payload)->assertForbidden();
        $this->actingAs($this->admin)->post('/users', $payload)->assertRedirect();
    }

    public function test_user_show_requires_admin(): void
    {
        $target = User::factory()->create();

        $this->get("/users/{$target->id}")->assertRedirect('/login');
        $this->actingAs($this->customer)->get("/users/{$target->id}")->assertForbidden();
        $this->actingAs($this->librarian)->get("/users/{$target->id}")->assertForbidden();
        $this->actingAs($this->admin)->get("/users/{$target->id}")->assertOk();
    }

    public function test_user_edit_requires_admin(): void
    {
        $target = User::factory()->create();

        $this->get("/users/{$target->id}/edit")->assertRedirect('/login');
        $this->actingAs($this->customer)->get("/users/{$target->id}/edit")->assertForbidden();
        $this->actingAs($this->librarian)->get("/users/{$target->id}/edit")->assertForbidden();
        $this->actingAs($this->admin)->get("/users/{$target->id}/edit")->assertOk();
    }

    public function test_user_update_requires_admin(): void
    {
        $target  = User::factory()->create();
        $payload = ['name' => 'Updated Name', 'email' => $target->email, 'role' => 'customer'];

        $this->put("/users/{$target->id}", $payload)->assertRedirect('/login');
        $this->actingAs($this->customer)->put("/users/{$target->id}", $payload)->assertForbidden();
        $this->actingAs($this->librarian)->put("/users/{$target->id}", $payload)->assertForbidden();
        $this->actingAs($this->admin)->put("/users/{$target->id}", $payload)->assertRedirect();
    }

    public function test_user_destroy_requires_admin(): void
    {
        $targetA = User::factory()->create();
        $targetB = User::factory()->create();

        $this->delete("/users/{$targetA->id}")->assertRedirect('/login');
        $this->actingAs($this->customer)->delete("/users/{$targetA->id}")->assertForbidden();
        $this->actingAs($this->librarian)->delete("/users/{$targetA->id}")->assertForbidden();
        $this->actingAs($this->admin)->delete("/users/{$targetB->id}")->assertRedirect();
    }

    public function test_user_block_requires_admin(): void
    {
        $target = User::factory()->create();

        $this->post("/users/{$target->id}/block")->assertRedirect('/login');
        $this->actingAs($this->customer)->post("/users/{$target->id}/block")->assertForbidden();
        $this->actingAs($this->librarian)->post("/users/{$target->id}/block")->assertForbidden();
        $this->actingAs($this->admin)->post("/users/{$target->id}/block")->assertRedirect();
    }

    public function test_user_unblock_requires_admin(): void
    {
        $target = User::factory()->blocked()->create();

        $this->post("/users/{$target->id}/unblock")->assertRedirect('/login');
        $this->actingAs($this->customer)->post("/users/{$target->id}/unblock")->assertForbidden();
        $this->actingAs($this->librarian)->post("/users/{$target->id}/unblock")->assertForbidden();
        $this->actingAs($this->admin)->post("/users/{$target->id}/unblock")->assertRedirect();
    }

    public function test_logs_index_requires_admin(): void
    {
        $this->get('/logs')->assertRedirect('/login');
        $this->actingAs($this->customer)->get('/logs')->assertForbidden();
        $this->actingAs($this->librarian)->get('/logs')->assertForbidden();
        $this->actingAs($this->admin)->get('/logs')->assertOk();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Customer-specific: reservation and cancellation
    // ──────────────────────────────────────────────────────────────────────────

    public function test_reservation_cancel_accessible_by_all_authenticated_roles(): void
    {
        $resA = Reservation::factory()->create(['status' => 'pending']);
        $resB = Reservation::factory()->create(['status' => 'pending']);
        $resC = Reservation::factory()->create(['status' => 'pending']);

        $this->delete("/reservations/{$resA->id}")->assertRedirect('/login');
        $this->actingAs($this->customer)->delete("/reservations/{$resA->id}")->assertRedirect();
        $this->actingAs($this->librarian)->delete("/reservations/{$resB->id}")->assertRedirect();
        $this->actingAs($this->admin)->delete("/reservations/{$resC->id}")->assertRedirect();
    }
}
