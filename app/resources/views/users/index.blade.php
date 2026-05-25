@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Usuários</h1>
    <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        + Novo Usuário
    </a>
</div>

<form method="GET" action="{{ route('users.index') }}" class="mb-6 flex gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Buscar por nome ou email..."
           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Todos os papéis</option>
        <option value="customer" @selected(request('role') === 'customer')>Cliente</option>
        <option value="librarian" @selected(request('role') === 'librarian')>Bibliotecário</option>
        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
    </select>
    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition">Buscar</button>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Nome</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">E-mail</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">CPF</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Papel</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->cpf }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($user->role === 'admin') bg-purple-100 text-purple-700
                            @elseif($user->role === 'librarian') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($user->blocked)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Bloqueado</span>
                        @else
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Ativo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover:underline">Ver</a>
                        <a href="{{ route('users.edit', $user->id) }}" class="text-gray-600 hover:underline">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Nenhum usuário encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
