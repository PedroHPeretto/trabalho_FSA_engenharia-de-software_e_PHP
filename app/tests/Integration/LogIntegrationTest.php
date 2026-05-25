<?php

namespace Tests\Integration;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;

class LogIntegrationTest extends TestCase
{
    public function test_loan_creation_creates_log_entry(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->create();
        $book      = Book::factory()->create(['stock' => 3]);

        $this->actingAs($librarian)->post('/loans', [
            'book_id'  => $book->id,
            'user_id'  => $customer->id,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('logs', [
            'made_by' => $librarian->id,
            'action'  => 'loan.create',
        ]);
    }

    public function test_loan_return_creates_log_entry(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->create();
        $book      = Book::factory()->create(['stock' => 1]);
        $loan      = Loan::factory()->create([
            'book_id'  => $book->id,
            'user_id'  => $customer->id,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($librarian)->post("/loans/{$loan->id}/return");

        $this->assertDatabaseHas('logs', [
            'made_by' => $librarian->id,
            'action'  => 'loan.return',
        ]);
    }

    public function test_book_creation_creates_log_entry_via_middleware(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)->post('/books', [
            'title'  => 'Test Book',
            'author' => 'Author',
            'media'  => 'physical',
            'stock'  => 2,
        ]);

        $this->assertDatabaseHas('logs', [
            'made_by' => $librarian->id,
            'action'  => 'post.books.store',
        ]);
    }

    public function test_reservation_creation_creates_log_entry(): void
    {
        $customer = User::factory()->create();
        $book     = Book::factory()->outOfStock()->create();

        $this->actingAs($customer)->post('/reservations', [
            'book_id' => $book->id,
            'user_id' => $customer->id,
        ]);

        $this->assertDatabaseHas('logs', [
            'made_by' => $customer->id,
            'action'  => 'reservation.create',
        ]);
    }

    public function test_loan_renewal_creates_log_entry(): void
    {
        $librarian = User::factory()->librarian()->create();
        $loan      = Loan::factory()->create(['due_date' => now()->addDays(3)]);

        $this->actingAs($librarian)->post("/loans/{$loan->id}/renew");

        $this->assertDatabaseHas('logs', [
            'made_by' => $librarian->id,
            'action'  => 'loan.renew',
        ]);
    }
}
