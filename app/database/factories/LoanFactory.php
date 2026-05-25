<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        return [
            'book_id'     => Book::factory(),
            'user_id'     => User::factory(),
            'loaned_at'   => now(),
            'due_date'    => now()->addDays(14),
            'returned_at' => null,
            'has_fine'    => false,
            'fine_paid'   => false,
        ];
    }

    public function overdue(): static
    {
        return $this->state([
            'loaned_at' => now()->subDays(20),
            'due_date'  => now()->subDays(5),
        ]);
    }

    public function returned(): static
    {
        return $this->state(['returned_at' => now()]);
    }
}
