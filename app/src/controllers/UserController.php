<?php

namespace App\Controllers;

use App\DTOs\RegisterUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'role']);

        Log::debug('[User] Listando usuários', [
            'admin_id' => auth()->id(),
            'filters'  => $filters,
        ]);

        $users = $this->userService->listUsers($filters);

        Log::debug('[User] Listagem concluída', ['total' => $users->count()]);

        return view('users.index', compact('users'));
    }

    public function show(string $id): View
    {
        Log::debug('[User] Exibindo detalhes do usuário', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        $user = $this->userService->findUser($id);

        Log::debug('[User] Usuário encontrado', ['name' => $user->name, 'role' => $user->role]);

        return view('users.show', compact('user'));
    }

    public function create(): View
    {
        Log::debug('[User] Exibindo formulário de criação de usuário', ['admin_id' => auth()->id()]);

        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Log::debug('[User] Tentativa de criação de usuário', [
            'admin_id' => auth()->id(),
            'email'    => $request->input('email'),
            'role'     => $request->input('role'),
        ]);

        $dto  = RegisterUserDTO::fromRequest($request);
        $user = $this->userService->createUser($dto);

        Log::debug('[User] Usuário criado com sucesso', [
            'new_user_id' => $user->id,
            'email'       => $user->email,
            'admin_id'    => auth()->id(),
        ]);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'User created successfully.');
    }

    public function edit(string $id): View
    {
        Log::debug('[User] Exibindo formulário de edição de usuário', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        $user = $this->userService->findUser($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Log::debug('[User] Tentativa de atualização de usuário', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
            'input'          => $request->except(['_token', '_method', 'password', 'password_confirmation']),
        ]);

        $dto = UpdateUserDTO::fromRequest($request);
        $this->userService->updateUser($id, $dto);

        Log::debug('[User] Usuário atualizado com sucesso', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        return redirect()->route('users.show', $id)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        Log::debug('[User] Tentativa de exclusão de usuário', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        $this->userService->deleteUser($id);

        Log::debug('[User] Usuário excluído com sucesso', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function block(string $id): RedirectResponse
    {
        Log::debug('[User] Tentativa de bloqueio de usuário', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        $this->userService->blockUser($id);

        Log::debug('[User] Usuário bloqueado com sucesso', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        return redirect()->route('users.show', $id)
            ->with('success', 'User blocked.');
    }

    public function unblock(string $id): RedirectResponse
    {
        Log::debug('[User] Tentativa de desbloqueio de usuário', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        $this->userService->unblockUser($id);

        Log::debug('[User] Usuário desbloqueado com sucesso', [
            'target_user_id' => $id,
            'admin_id'       => auth()->id(),
        ]);

        return redirect()->route('users.show', $id)
            ->with('success', 'User unblocked.');
    }
}
