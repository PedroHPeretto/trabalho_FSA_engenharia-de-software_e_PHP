<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class UpdateUserDTO
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $password,
        public ?string $role,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'unique:users,email'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'role'     => ['sometimes', 'in:customer,librarian,admin'],
        ]);

        return new static(
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            password: $validated['password'] ?? null,
            role: $validated['role'] ?? null,
        );
    }
}
