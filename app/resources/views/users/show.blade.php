@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-sm text-indigo-600 hover:underline">← Voltar aos usuários</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($user->blocked)
                    <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Bloqueado</span>
                @else
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Ativo</span>
                @endif
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $user->role }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 text-xs mb-1">CPF</p>
                <p class="font-semibold">{{ $user->cpf }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 text-xs mb-1">Membro desde</p>
                <p class="font-semibold">{{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('users.edit', $user->id) }}"
               class="border border-gray-300 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition">
                Editar
            </a>

            @if($user->blocked)
                <form method="POST" action="{{ route('users.unblock', $user->id) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition">
                        Desbloquear
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('users.block', $user->id) }}">
                    @csrf
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white text-sm px-4 py-2 rounded-lg transition">
                        Bloquear
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Deletar este usuário?')">
                @csrf @method('DELETE')
                <button type="submit" class="border border-red-300 text-red-600 hover:bg-red-50 text-sm px-4 py-2 rounded-lg transition">
                    Deletar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
