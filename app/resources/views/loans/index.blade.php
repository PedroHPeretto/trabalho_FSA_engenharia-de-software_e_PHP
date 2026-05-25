@extends('layouts.app')

@section('title', 'Empréstimos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Empréstimos</h1>
    <a href="{{ route('books.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        + Novo Empréstimo
    </a>
</div>

@if($loans->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">📋</div>
        <p>Nenhum empréstimo registrado.</p>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Livro</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Usuário</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Emprestado em</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Prazo</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($loans as $loan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $loan->book->title }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $loan->user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $loan->loaned_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @if($loan->returned_at)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Devolvido</span>
                            @elseif($loan->due_date->isPast())
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Atrasado</span>
                            @else
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Ativo</span>
                            @endif
                            @if($loan->has_fine)
                                <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full ml-1">Multa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('loans.show', $loan->id) }}" class="text-indigo-600 hover:underline">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
