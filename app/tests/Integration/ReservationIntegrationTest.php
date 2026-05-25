<?php

namespace Tests\Integration;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;

class ReservationIntegrationTest extends TestCase
{
    public function test_customer_can_reserve_unavailable_book(): void
    {
        $customer = User::factory()->create();
        $book     = Book::factory()->outOfStock()->create();

        $this->actingAs($customer)
            ->post('/reservations', [
                'book_id' => $book->id,
                'user_id' => $customer->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'book_id' => $book->id,
            'user_id' => $customer->id,
            'status'  => 'pending',
        ]);
    }

    public function test_customer_cannot_reserve_available_book(): void
    {
        $customer = User::factory()->create();
        $book     = Book::factory()->create(['stock' => 3]);

        $this->actingAs($customer)
            ->post('/reservations', [
                'book_id' => $book->id,
                'user_id' => $customer->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('reservations', ['book_id' => $book->id]);
    }

    public function test_blocked_user_cannot_make_reservation(): void
    {
        $blocked = User::factory()->blocked()->create();
        $book    = Book::factory()->outOfStock()->create();

        $this->actingAs($blocked)
            ->post('/reservations', [
                'book_id' => $book->id,
                'user_id' => $blocked->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('reservations', ['user_id' => $blocked->id]);
    }

    public function test_customer_can_cancel_own_reservation(): void
    {
        $customer    = User::factory()->create();
        $book        = Book::factory()->outOfStock()->create();
        $reservation = Reservation::factory()->create([
            'book_id' => $book->id,
            'user_id' => $customer->id,
        ]);

        $this->actingAs($customer)
            ->delete("/reservations/{$reservation->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'cancelled']);
    }

    public function test_oldest_reservation_fulfilled_when_book_returned(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->create();
        $book      = Book::factory()->create(['stock' => 1]);

        $loan = Loan::factory()->overdue()->create([
            'book_id' => $book->id,
            'user_id' => $customer->id,
        ]);

        $waiter1 = User::factory()->create();
        $waiter2 = User::factory()->create();

        $reservation1 = Reservation::factory()->create([
            'book_id'     => $book->id,
            'user_id'     => $waiter1->id,
            'reserved_at' => now()->subHours(2),
            'status'      => 'pending',
        ]);
        $reservation2 = Reservation::factory()->create([
            'book_id'     => $book->id,
            'user_id'     => $waiter2->id,
            'reserved_at' => now()->subHour(),
            'status'      => 'pending',
        ]);

        $this->actingAs($librarian)->post("/loans/{$loan->id}/return");

        $this->assertDatabaseHas('reservations', ['id' => $reservation1->id, 'status' => 'fulfilled']);
        $this->assertDatabaseHas('reservations', ['id' => $reservation2->id, 'status' => 'pending']);
    }
}
