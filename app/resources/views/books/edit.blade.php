@extends('layouts.app')

@section('title', 'Editar Livro')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('books.show', $book->id) }}" class="text-sm text-indigo-600 hover:underline">← Voltar</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-xl font-bold text-gray-900 mb-6">Editar Livro</h1>

        <form method="POST" action="{{ route('books.update', $book->id) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Autor</label>
                <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('author') border-red-500 @enderror">
                @error('author')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="media" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select id="media" name="media" onchange="toggleMediaFields(this.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('media') border-red-500 @enderror">
                    <option value="physical" @selected(old('media', $book->media) === 'physical')>Físico</option>
                    <option value="digital" @selected(old('media', $book->media) === 'digital')>Digital</option>
                </select>
                @error('media')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="field-stock" class="{{ old('media', $book->media) === 'physical' ? '' : 'hidden' }}">
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Estoque</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', $book->stock) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('stock') border-red-500 @enderror">
                @error('stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="field-digital-link" class="{{ old('media', $book->media) === 'digital' ? '' : 'hidden' }}">
                <label for="digital_link" class="block text-sm font-medium text-gray-700 mb-1">Link Digital</label>
                <input type="url" id="digital_link" name="digital_link" value="{{ old('digital_link', $book->digital_link) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('digital_link') border-red-500 @enderror">
                @error('digital_link')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="field-pdf" class="{{ old('media', $book->media) === 'digital' ? '' : 'hidden' }}">
                <label for="pdf" class="block text-sm font-medium text-gray-700 mb-1">PDF do Livro</label>
                @if($book->pdf)
                    <p class="text-xs text-green-600 mb-1">PDF atual disponível. Envie um novo para substituir.</p>
                @endif
                <input type="file" id="pdf" name="pdf" accept="application/pdf"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('pdf') border-red-500 @enderror">
                @error('pdf')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">Imagem de Capa</label>
                @if($book->cover_image)
                    <div class="mb-2">
                        <img src="{{ route('books.cover', $book->id) }}" alt="Capa atual" class="h-24 rounded-lg object-cover border border-gray-200">
                        <p class="text-xs text-gray-500 mt-1">Envie uma nova imagem para substituir.</p>
                    </div>
                @endif
                <input type="file" id="cover_image" name="cover_image" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('cover_image') border-red-500 @enderror">
                @error('cover_image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-lg transition text-sm">
                    Salvar Alterações
                </button>
                <a href="{{ route('books.show', $book->id) }}" class="border border-gray-300 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMediaFields(value) {
    document.getElementById('field-stock').classList.toggle('hidden', value !== 'physical');
    document.getElementById('field-digital-link').classList.toggle('hidden', value !== 'digital');
    document.getElementById('field-pdf').classList.toggle('hidden', value !== 'digital');
}
</script>
@endsection
