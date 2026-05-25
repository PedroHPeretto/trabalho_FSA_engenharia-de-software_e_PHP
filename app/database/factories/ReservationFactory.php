<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'book_id'         => Book::factory()->outOfStock(),
            'user_id'         => User::factory(),
            'reserved_at'     => now(),
            'expiration_date' => now()->addDays(3),
            'status'          => 'pending',
        ];
    }

    public function fulfilled(): static
    {
        return $this->state(['status' => 'fulfilled']);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }
}
