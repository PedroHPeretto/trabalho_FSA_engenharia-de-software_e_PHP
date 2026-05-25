<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class PasswordRecoveryDTO
{
    public function __construct(
        public string $email,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        return new static(email: $validated['email']);
    }
}
