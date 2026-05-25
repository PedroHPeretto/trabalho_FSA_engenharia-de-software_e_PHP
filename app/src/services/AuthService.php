<?php

namespace App\Services;

use App\DTOs\LoginDTO;
use App\DTOs\PasswordRecoveryDTO;
use App\DTOs\ResetPasswordDTO;
use App\Mail\PasswordRecoveryMail;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(string $name, string $cpf, string $email, string $password): User
    {
        $user = User::create([
            'name'     => $name,
            'cpf'      => $cpf,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'customer',
        ]);

        Auth::login($user);

        return $user;
    }

    public function login(LoginDTO $dto): User
    {
        if (!Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return Auth::user();
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function sendPasswordRecoveryEmail(PasswordRecoveryDTO $dto): void
    {
        $user = User::where('email', $dto->email)->firstOrFail();

        $token = Password::createToken($user);

        Mail::to($dto->email)->queue(new PasswordRecoveryMail($token, $dto->email));
    }

    public function resetPassword(ResetPasswordDTO $dto): void
    {
        $status = Password::reset(
            [
                'email'    => $dto->email,
                'password' => $dto->password,
                'token'    => $dto->token,
            ],
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            throw new \RuntimeException('Invalid or expired password reset token.');
        }
    }
}
