@extends('layouts.app')

@section('title', 'Catálogo de Livros')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Catálogo de Livros</h1>
    @if(in_array(auth()->user()->role, ['librarian', 'admin']))
        <a href="{{ route('books.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Novo Livro
        </a>
    @endif
</div>

<form method="GET" action="{{ route('books.index') }}" class="mb-6 flex gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Buscar por título ou autor..."
           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <select name="media" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Todos os tipos</option>
        <option value="physical" @selected(request('media') === 'physical')>Físico</option>
        <option value="digital" @selected(request('media') === 'digital')>Digital</option>
    </select>
    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition">Buscar</button>
</form>

@if($books->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">📭</div>
        <p>Nenhum livro encontrado.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($books as $book)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                @if($book->has_cover)
                    <img src="{{ route('books.cover', $book->id) }}"
                         alt="Capa de {{ $book->title }}"
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                @endif

                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $book->media === 'physical' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $book->media === 'physical' ? 'Físico' : 'Digital' }}
                        </span>
                        @if($book->media === 'physical')
                            <span class="text-xs {{ $book->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $book->stock > 0 ? "Estoque: {$book->stock}" : 'Esgotado' }}
                            </span>
                        @else
                            <span class="text-xs {{ !$book->reserved ? 'text-green-600' : 'text-red-500' }}">
                                {{ !$book->reserved ? 'Disponível' : 'Reservado' }}
                            </span>
                        @endif
                    </div>

                    <h3 class="font-semibold text-gray-900 text-sm leading-tight mb-1">{{ $book->title }}</h3>
                    <p class="text-gray-500 text-xs mb-4">{{ $book->author }}</p>

                    <div class="mt-auto flex gap-2">
                        <a href="{{ route('books.show', $book->id) }}"
                           class="flex-1 text-center text-xs border border-indigo-300 text-indigo-700 hover:bg-indigo-50 py-1.5 rounded-lg transition">
                            Ver detalhes
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
