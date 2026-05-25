@extends('layouts.app')

@section('title', 'Empréstimo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('loans.index') }}" class="text-sm text-indigo-600 hover:underline">← Voltar aos empréstimos</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-start justify-between mb-6">
            <h1 class="text-xl font-bold text-gray-900">Detalhe do Empréstimo</h1>
            @if($loan->returned_at)
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Devolvido</span>
            @elseif($loan->due_date->isPast())
                <span class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-full">Atrasado</span>
            @else
                <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full">Ativo</span>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 text-xs mb-1">Livro</p>
                <p class="font-semibold text-gray-900">{{ $loan->book->title }}</p>
                <p class="text-gray-500 text-xs">{{ $loan->book->author }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 text-xs mb-1">Usuário</p>
                <p class="font-semibold text-gray-900">{{ $loan->user->name }}</p>
                <p class="text-gray-500 text-xs">{{ $loan->user->email }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 text-xs mb-1">Emprestado em</p>
                <p class="font-semibold">{{ $loan->loaned_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-500 text-xs mb-1">Prazo de devolução</p>
                <p class="font-semibold {{ $loan->due_date->isPast() && !$loan->returned_at ? 'text-red-600' : '' }}">
                    {{ $loan->due_date->format('d/m/Y') }}
                </p>
            </div>
            @if($loan->returned_at)
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500 text-xs mb-1">Devolvido em</p>
                    <p class="font-semibold">{{ $loan->returned_at->format('d/m/Y H:i') }}</p>
                </div>
            @endif
            @if($loan->has_fine && $loan->fine)
                <div class="bg-orange-50 rounded-lg p-3">
                    <p class="text-orange-500 text-xs mb-1">Multa</p>
                    <p class="font-semibold text-orange-700">R$ {{ number_format($loan->fine->amount, 2, ',', '.') }}</p>
                    <p class="text-xs text-orange-500">{{ $loan->fine->paid ? 'Paga' : 'Pendente' }}</p>
                </div>
            @endif
        </div>

        @if(!$loan->returned_at)
            <div class="flex gap-3">
                <form method="POST" action="{{ route('loans.renew', $loan->id) }}">
                    @csrf
                    <button type="submit" class="border border-indigo-300 text-indigo-700 hover:bg-indigo-50 text-sm px-4 py-2 rounded-lg transition">
                        Renovar (+{{ config('library.renewal_days', 14) }} dias)
                    </button>
                </form>
                <form method="POST" action="{{ route('loans.return', $loan->id) }}" onsubmit="return confirm('Confirmar devolução?')">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition">
                        Registrar Devolução
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
