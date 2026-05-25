<?php

namespace App\Controllers;

use App\DTOs\CreateBookDTO;
use App\DTOs\UpdateBookDTO;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BookController
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'media']);

        Log::debug('[Book] Listando livros', [
            'filters'  => $filters,
            'user_id'  => auth()->id(),
        ]);

        $books = $this->bookService->listBooks($filters);

        Log::debug('[Book] Listagem concluída', ['total' => $books->count()]);

        return view('books.index', compact('books'));
    }

    public function show(string $id): View
    {
        Log::debug('[Book] Exibindo detalhes do livro', [
            'book_id' => $id,
            'user_id' => auth()->id(),
        ]);

        $book = $this->bookService->findBook($id);

        Log::debug('[Book] Livro encontrado', ['title' => $book->title]);

        return view('books.show', compact('book'));
    }

    public function create(): View
    {
        Log::debug('[Book] Exibindo formulário de criação de livro', ['user_id' => auth()->id()]);

        return view('books.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Log::debug('[Book] Tentativa de criação de livro', [
            'user_id' => auth()->id(),
            'input'   => $request->except(['_token']),
        ]);

        $dto  = CreateBookDTO::fromRequest($request);
        $book = $this->bookService->createBook($dto);

        Log::debug('[Book] Livro criado com sucesso', ['book_id' => $book->id, 'title' => $book->title]);

        return redirect()->route('books.show', $book->id)
            ->with('success', 'Book created successfully.');
    }

    public function edit(string $id): View
    {
        Log::debug('[Book] Exibindo formulário de edição de livro', [
            'book_id' => $id,
            'user_id' => auth()->id(),
        ]);

        $book = $this->bookService->findBook($id);

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Log::debug('[Book] Tentativa de atualização de livro', [
            'book_id' => $id,
            'user_id' => auth()->id(),
            'input'   => $request->except(['_token', '_method']),
        ]);

        $dto = UpdateBookDTO::fromRequest($request);
        $this->bookService->updateBook($id, $dto);

        Log::debug('[Book] Livro atualizado com sucesso', ['book_id' => $id]);

        return redirect()->route('books.show', $id)
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        Log::debug('[Book] Tentativa de exclusão de livro', [
            'book_id' => $id,
            'user_id' => auth()->id(),
        ]);

        $this->bookService->deleteBook($id);

        Log::debug('[Book] Livro excluído com sucesso', ['book_id' => $id]);

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }

    public function cover(string $id): Response
    {
        $book = $this->bookService->findBook($id);

        abort_if(empty($book->cover_image), 404);

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($book->cover_image) ?: 'image/jpeg';

        return response($book->cover_image, 200, [
            'Content-Type'  => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function pdf(string $id): Response
    {
        $book = $this->bookService->findBook($id);

        abort_if(empty($book->pdf), 404);

        $filename = str($book->title)->slug()->append('.pdf')->toString();

        return response($book->pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
            'Cache-Control'       => 'public, max-age=86400',
        ]);
    }
}
