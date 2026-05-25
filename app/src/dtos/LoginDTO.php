<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        return new static(
            email: $validated['email'],
            password: $validated['password'],
        );
    }
}
