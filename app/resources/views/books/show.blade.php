@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:underline">← Voltar ao catálogo</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($book->cover_image)
            <img src="{{ route('books.cover', $book->id) }}"
                 alt="Capa de {{ $book->title }}"
                 class="w-full max-h-72 object-cover object-top">
        @endif

        <div class="p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full mb-3 inline-block
                    {{ $book->media === 'physical' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                    {{ $book->media === 'physical' ? 'Físico' : 'Digital' }}
                </span>
                <h1 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h1>
                <p class="text-gray-500 mt-1">{{ $book->author }}</p>
            </div>
            @if(in_array(auth()->user()->role, ['librarian', 'admin']))
                <div class="flex gap-2">
                    <a href="{{ route('books.edit', $book->id) }}"
                       class="text-sm border border-gray-300 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">Editar</a>
                    <form method="POST" action="{{ route('books.destroy', $book->id) }}" onsubmit="return confirm('Deletar este livro?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm border border-red-300 text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">Deletar</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            @if($book->media === 'physical')
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500 text-xs mb-1">Estoque</p>
                    <p class="font-semibold {{ $book->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $book->stock }} unidade(s)
                    </p>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500 text-xs mb-1">Status</p>
                    <p class="font-semibold {{ !$book->reserved ? 'text-green-600' : 'text-red-500' }}">
                        {{ !$book->reserved ? 'Disponível' : 'Reservado' }}
                    </p>
                </div>
                @if($book->pdf)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-500 text-xs mb-1">PDF</p>
                        <a href="{{ route('books.pdf', $book->id) }}" target="_blank"
                           class="text-indigo-600 hover:underline text-xs font-medium">
                            Acessar PDF
                        </a>
                    </div>
                @elseif($book->digital_link)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-500 text-xs mb-1">Link Digital</p>
                        <a href="{{ $book->digital_link }}" target="_blank"
                           class="text-indigo-600 hover:underline text-xs break-all">
                            Acessar
                        </a>
                    </div>
                @endif
            @endif
        </div>

        @php
            $isUnavailable = ($book->media === 'physical' && $book->stock <= 0)
                          || ($book->media === 'digital' && $book->reserved);
        @endphp

        <div class="flex gap-3 items-center">
            @if(in_array(auth()->user()->role, ['librarian', 'admin']))
                <form method="POST" action="{{ route('loans.store') }}">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="due_date" value="{{ now()->addDays(14)->format('Y-m-d') }}">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition">
                        Registrar Empréstimo
                    </button>
                </form>
            @elseif(!$isUnavailable)
                <p class="text-sm text-gray-500">
                    Livro disponível — solicite o empréstimo ao bibliotecário.
                </p>
            @endif

            @if($isUnavailable)
                <form method="POST" action="{{ route('reservations.store') }}">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <button type="submit" class="border border-indigo-300 text-indigo-700 hover:bg-indigo-50 text-sm px-4 py-2 rounded-lg transition">
                        Reservar
                    </button>
                </form>
            @endif
        </div>
        </div>{{-- /p-8 --}}
    </div>
</div>
@endsection
