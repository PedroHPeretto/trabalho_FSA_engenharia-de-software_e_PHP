<?php

namespace App\Controllers;

use App\DTOs\LoginDTO;
use App\DTOs\PasswordRecoveryDTO;
use App\DTOs\ResetPasswordDTO;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function showLogin(): View
    {
        Log::debug('[Auth] Exibindo página de login');

        return view('auth.login');
    }

    public function showRegister(): View
    {
        Log::debug('[Auth] Exibindo página de cadastro');

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        Log::debug('[Auth] Tentativa de cadastro de novo usuário', [
            'email' => $request->input('email'),
            'name'  => $request->input('name'),
            'ip'    => $request->ip(),
        ]);

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'cpf'                   => ['required', 'string', 'size:11', 'regex:/^\d{11}$/'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $this->authService->register(
            $validated['name'],
            $validated['cpf'],
            $validated['email'],
            $validated['password'],
        );

        $request->session()->regenerate();

        Log::debug('[Auth] Usuário cadastrado com sucesso', ['email' => $validated['email']]);

        return redirect('/books');
    }

    public function login(Request $request): RedirectResponse
    {
        Log::debug('[Auth] Tentativa de login', [
            'email' => $request->input('email'),
            'ip'    => $request->ip(),
        ]);

        $dto = LoginDTO::fromRequest($request);

        try {
            $this->authService->login($dto);
        } catch (AuthenticationException) {
            Log::debug('[Auth] Falha na autenticação — credenciais inválidas', [
                'email' => $request->input('email'),
            ]);

            return back()
                ->withErrors(['email' => 'E-mail ou senha incorretos.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        Log::debug('[Auth] Login realizado com sucesso', ['email' => $request->input('email')]);

        return redirect()->intended('/books');
    }

    public function logout(Request $request): RedirectResponse
    {
        Log::debug('[Auth] Logout solicitado', ['user_id' => auth()->id()]);

        $this->authService->logout($request);

        Log::debug('[Auth] Sessão encerrada com sucesso');

        return redirect('/login');
    }

    public function showForgotPassword(): View
    {
        Log::debug('[Auth] Exibindo página de recuperação de senha');

        return view('auth.forgot-password');
    }

    public function sendRecoveryEmail(Request $request): RedirectResponse
    {
        Log::debug('[Auth] Solicitação de envio de e-mail de recuperação de senha', [
            'email' => $request->input('email'),
            'ip'    => $request->ip(),
        ]);

        $dto = PasswordRecoveryDTO::fromRequest($request);
        $this->authService->sendPasswordRecoveryEmail($dto);

        Log::debug('[Auth] E-mail de recuperação de senha enviado', ['email' => $request->input('email')]);

        return back()->with('success', 'Password recovery email sent. Please check your inbox.');
    }

    public function showResetPassword(Request $request): View
    {
        Log::debug('[Auth] Exibindo página de redefinição de senha', [
            'email' => $request->query('email'),
        ]);

        return view('auth.reset-password', [
            'token' => $request->query('token'),
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        Log::debug('[Auth] Tentativa de redefinição de senha', [
            'email' => $request->input('email'),
            'ip'    => $request->ip(),
        ]);

        $dto = ResetPasswordDTO::fromRequest($request);
        $this->authService->resetPassword($dto);

        Log::debug('[Auth] Senha redefinida com sucesso', ['email' => $request->input('email')]);

        return redirect('/login')->with('success', 'Password reset successfully. You can now log in.');
    }
}
