@extends('layouts.app')

@section('title', 'Minhas Reservas')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Minhas Reservas</h1>

@if($reservations->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">📭</div>
        <p>Você não tem reservas ativas.</p>
        <a href="{{ route('books.index') }}" class="mt-3 inline-block text-indigo-600 hover:underline text-sm">Ver catálogo</a>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Livro</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Reservado em</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Expira em</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($reservations as $reservation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $reservation->book->title }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $reservation->reserved_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $reservation->expiration_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @if($reservation->status === 'pending')
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Pendente</span>
                            @elseif($reservation->status === 'fulfilled')
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Disponível</span>
                            @else
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Cancelada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($reservation->status === 'pending')
                                <form method="POST" action="{{ route('reservations.cancel', $reservation->id) }}"
                                      onsubmit="return confirm('Cancelar reserva?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Cancelar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
