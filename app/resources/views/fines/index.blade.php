@extends('layouts.app')

@section('title', 'Multas')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Multas</h1>

@if($fines->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">✅</div>
        <p>Nenhuma multa encontrada.</p>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    @if(in_array(auth()->user()->role, ['librarian', 'admin']))
                        <th class="text-left px-4 py-3 text-gray-600 font-medium">Usuário</th>
                    @endif
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Livro</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Valor</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                    @if(in_array(auth()->user()->role, ['librarian', 'admin']))
                        <th class="text-left px-4 py-3 text-gray-600 font-medium">Ações</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($fines as $fine)
                    <tr class="hover:bg-gray-50">
                        @if(in_array(auth()->user()->role, ['librarian', 'admin']))
                            <td class="px-4 py-3 text-gray-600">{{ $fine->user->name }}</td>
                        @endif
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $fine->loan->book->title ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700 font-semibold">R$ {{ number_format($fine->amount, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if($fine->paid)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Paga</span>
                            @else
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Pendente</span>
                            @endif
                        </td>
                        @if(in_array(auth()->user()->role, ['librarian', 'admin']))
                            <td class="px-4 py-3">
                                @if(!$fine->paid)
                                    <form method="POST" action="{{ route('fines.pay', $fine->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg transition">
                                            Registrar Pagamento
                                        </button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
