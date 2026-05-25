@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('users.show', $user->id) }}" class="text-sm text-indigo-600 hover:underline">← Voltar</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-xl font-bold text-gray-900 mb-6">Editar Usuário</h1>

        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Papel</label>
                <select id="role" name="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('role') border-red-500 @enderror">
                    <option value="customer" @selected(old('role', $user->role) === 'customer')>Cliente</option>
                    <option value="librarian" @selected(old('role', $user->role) === 'librarian')>Bibliotecário</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                </select>
                @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nova Senha (deixar em branco para manter)</label>
                <input type="password" id="password" name="password"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password_confirmation') border-red-500 @enderror">
                @error('password_confirmation')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-lg transition text-sm">
                    Salvar Alterações
                </button>
                <a href="{{ route('users.show', $user->id) }}" class="border border-gray-300 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
