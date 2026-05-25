<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ResetPasswordDTO
{
    public function __construct(
        public string $token,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        return new static(
            token: $validated['token'],
            email: $validated['email'],
            password: $validated['password'],
        );
    }
}
