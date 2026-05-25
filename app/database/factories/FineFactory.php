<?php

namespace Database\Factories;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FineFactory extends Factory
{
    protected $model = Fine::class;

    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'user_id' => User::factory(),
            'amount'  => 100.00,
            'paid'    => false,
        ];
    }

    public function paid(): static
    {
        return $this->state(['paid' => true]);
    }
}
