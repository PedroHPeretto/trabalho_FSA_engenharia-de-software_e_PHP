@extends('layouts.app')

@section('title', 'Logs do Sistema')

@section('content')

@php
$actionColors = [
    'USER_CREATED'    => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'dot' => 'bg-green-500'],
    'USER_UPDATED'    => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500'],
    'USER_DELETED'    => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'dot' => 'bg-red-500'],
    'USER_BLOCKED'    => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'dot' => 'bg-red-500'],
    'USER_UNBLOCKED'  => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'dot' => 'bg-green-500'],
    'LOAN_CREATED'    => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'dot' => 'bg-indigo-500'],
    'LOAN_RENEWED'    => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'dot' => 'bg-indigo-500'],
    'LOAN_RETURNED'   => ['bg' => 'bg-teal-100',   'text' => 'text-teal-700',   'dot' => 'bg-teal-500'],
    'FINE_GENERATED'  => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'dot' => 'bg-orange-500'],
    'FINE_PAID'       => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'dot' => 'bg-green-500'],
    'BOOK_CREATED'    => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500'],
    'BOOK_UPDATED'    => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500'],
    'BOOK_DELETED'    => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'dot' => 'bg-red-500'],
];
$defaultColor = ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'];

$hasFilters = collect($filters)->filter()->isNotEmpty();
@endphp

{{-- Page header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Logs do Sistema</h1>
        <p class="text-sm text-gray-500 mt-0.5">Auditoria completa de ações realizadas na plataforma</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 font-semibold px-3 py-1.5 rounded-full">
            {{ $logs->count() }} {{ $logs->count() === 1 ? 'registro' : 'registros' }}
        </span>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('logs.index') }}"
      class="mb-6 bg-white border border-gray-100 rounded-2xl shadow-sm p-4 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
               placeholder="Descrição ou nome do usuário..."
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="min-w-44">
        <label class="block text-xs font-medium text-gray-500 mb-1">Ação</label>
        <select name="action"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todas as ações</option>
            @foreach($actions as $action)
                <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>
                    {{ $action }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="min-w-36">
        <label class="block text-xs font-medium text-gray-500 mb-1">Data início</label>
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="min-w-36">
        <label class="block text-xs font-medium text-gray-500 mb-1">Data fim</label>
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="flex gap-2">
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            Filtrar
        </button>
        @if($hasFilters)
            <a href="{{ route('logs.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                Limpar
            </a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium w-40">Data / Hora</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium w-44">Usuário</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium w-44">Ação</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Descrição</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($logs as $log)
                @php
                    $color = $actionColors[$log->action] ?? $defaultColor;
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                        <div class="font-medium text-gray-700">{{ $log->date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-400">{{ $log->date->format('H:i:s') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if($log->actor)
                            <div class="font-medium text-gray-800">{{ $log->actor->name }}</div>
                            <div class="text-xs text-gray-400">{{ $log->actor->email }}</div>
                        @else
                            <span class="text-gray-400 italic text-xs">Usuário removido</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $color['bg'] }} {{ $color['text'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $color['dot'] }}"></span>
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs">
                        <span class="line-clamp-2" title="{{ $log->description }}">{{ $log->description }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-2 text-gray-400">
                            <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                            </svg>
                            <p class="font-medium text-sm">Nenhum log encontrado</p>
                            @if($hasFilters)
                                <p class="text-xs">Tente ajustar os filtros aplicados.</p>
                            @else
                                <p class="text-xs">Ainda não há registros no sistema.</p>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
