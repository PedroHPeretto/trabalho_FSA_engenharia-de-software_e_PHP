<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $cpf,
        public string $email,
        public string $password,
        public string $role,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'cpf'                   => ['required', 'string', 'size:11'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'role'                  => ['required', 'in:customer,librarian,admin'],
        ]);

        return new static(
            name: $validated['name'],
            cpf: $validated['cpf'],
            email: $validated['email'],
            password: $validated['password'],
            role: $validated['role'],
        );
    }
}
