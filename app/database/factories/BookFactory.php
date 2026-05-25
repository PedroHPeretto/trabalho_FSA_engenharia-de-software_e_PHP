<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title'        => fake()->sentence(3),
            'author'       => fake()->name(),
            'media'        => 'physical',
            'stock'        => 3,
            'digital_link' => null,
            'reserved'     => false,
        ];
    }

    public function digital(): static
    {
        return $this->state([
            'media'        => 'digital',
            'stock'        => null,
            'digital_link' => fake()->url(),
            'reserved'     => false,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    public function reserved(): static
    {
        return $this->state(['reserved' => true, 'stock' => 0]);
    }
}
