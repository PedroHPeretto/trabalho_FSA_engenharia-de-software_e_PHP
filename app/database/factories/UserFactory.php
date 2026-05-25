<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'cpf'      => fake()->numerify('###########'),
            'email'    => fake()->unique()->safeEmail(),
            'role'     => 'customer',
            'password' => bcrypt('password'),
            'blocked'  => false,
        ];
    }

    public function librarian(): static
    {
        return $this->state(['role' => 'librarian']);
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function blocked(): static
    {
        return $this->state(['blocked' => true]);
    }
}
