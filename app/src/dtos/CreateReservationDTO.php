<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CreateReservationDTO
{
    public function __construct(
        public string $book_id,
        public string $user_id,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'book_id' => ['required', 'uuid', 'exists:books,id'],
            'user_id' => ['required', 'uuid', 'exists:users,id'],
        ]);

        return new static(
            book_id: $validated['book_id'],
            user_id: $validated['user_id'],
        );
    }
}
