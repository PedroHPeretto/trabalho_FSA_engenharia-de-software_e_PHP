<?php

namespace Tests\Integration;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;

class LoanIntegrationTest extends TestCase
{
    public function test_librarian_can_create_loan(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->create();
        $book      = Book::factory()->create(['stock' => 3]);

        $this->actingAs($librarian)
            ->post('/loans', [
                'book_id'  => $book->id,
                'user_id'  => $customer->id,
                'due_date' => now()->addDays(14)->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('loans', [
            'book_id' => $book->id,
            'user_id' => $customer->id,
        ]);

        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 2]);
    }

    public function test_customer_cannot_create_loan(): void
    {
        $customer = User::factory()->create();
        $book     = Book::factory()->create();
        $target   = User::factory()->create();

        $this->actingAs($customer)
            ->post('/loans', [
                'book_id'  => $book->id,
                'user_id'  => $target->id,
                'due_date' => now()->addDays(14)->format('Y-m-d'),
            ])
            ->assertForbidden();
    }

    public function test_blocked_user_cannot_be_loaned_to(): void
    {
        $librarian       = User::factory()->librarian()->create();
        $blockedCustomer = User::factory()->blocked()->create();
        $book            = Book::factory()->create();

        $this->actingAs($librarian)
            ->post('/loans', [
                'book_id'  => $book->id,
                'user_id'  => $blockedCustomer->id,
                'due_date' => now()->addDays(14)->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('loans', ['user_id' => $blockedCustomer->id]);
    }

    public function test_librarian_can_renew_loan(): void
    {
        $librarian = User::factory()->librarian()->create();
        $loan      = Loan::factory()->create(['due_date' => now()->addDays(3)]);

        $originalDueDate = $loan->due_date;

        $this->actingAs($librarian)
            ->post("/loans/{$loan->id}/renew")
            ->assertRedirect();

        $loan->refresh();
        $this->assertTrue($loan->due_date->gt($originalDueDate));
    }

    public function test_renewal_fails_when_book_has_pending_reservation(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->create();
        $book      = Book::factory()->create(['stock' => 0]);
        $loan      = Loan::factory()->create(['book_id' => $book->id, 'user_id' => $customer->id]);

        Reservation::factory()->create(['book_id' => $book->id, 'status' => 'pending']);

        $this->actingAs($librarian)
            ->post("/loans/{$loan->id}/renew")
            ->assertRedirect();

        $loan->refresh();
        $this->assertFalse($loan->due_date->gt(now()->addDays(14)));
    }

    public function test_librarian_can_return_loan(): void
    {
        $librarian = User::factory()->librarian()->create();
        $book      = Book::factory()->create(['stock' => 2]);
        $customer  = User::factory()->create();
        $loan      = Loan::factory()->create([
            'book_id'  => $book->id,
            'user_id'  => $customer->id,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($librarian)
            ->post("/loans/{$loan->id}/return")
            ->assertRedirect();

        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'has_fine' => false]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 3]);
    }

    public function test_late_return_creates_fine_and_blocks_user(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->create();
        $book      = Book::factory()->create(['stock' => 1]);
        $loan      = Loan::factory()->overdue()->create([
            'book_id' => $book->id,
            'user_id' => $customer->id,
        ]);

        $this->actingAs($librarian)
            ->post("/loans/{$loan->id}/return")
            ->assertRedirect();

        $this->assertDatabaseHas('fines', ['loan_id' => $loan->id, 'amount' => 100.00, 'paid' => false]);
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'has_fine' => true]);
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'blocked' => true]);
    }

    public function test_on_time_return_does_not_create_fine(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->create();
        $book      = Book::factory()->create(['stock' => 1]);
        $loan      = Loan::factory()->create([
            'book_id'  => $book->id,
            'user_id'  => $customer->id,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($librarian)
            ->post("/loans/{$loan->id}/return")
            ->assertRedirect();

        $this->assertDatabaseMissing('fines', ['loan_id' => $loan->id]);
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'has_fine' => false]);
    }
}
