<?php

namespace App\Services;

use App\DTOs\CreateBookDTO;
use App\DTOs\UpdateBookDTO;
use App\Exceptions\OutOfStockException;
use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function createBook(CreateBookDTO $dto): Book
    {
        return Book::create([
            'title'        => $dto->title,
            'author'       => $dto->author,
            'media'        => $dto->media,
            'stock'        => $dto->stock,
            'digital_link' => $dto->digital_link,
            'cover_image'  => $dto->cover_image,
            'pdf'          => $dto->pdf,
        ]);
    }

    public function updateBook(string $id, UpdateBookDTO $dto): Book
    {
        $book = $this->findBook($id);

        $data = array_filter([
            'title'        => $dto->title,
            'author'       => $dto->author,
            'media'        => $dto->media,
            'stock'        => $dto->stock,
            'digital_link' => $dto->digital_link,
        ], fn ($value) => $value !== null);

        if ($dto->cover_image !== null) {
            $data['cover_image'] = $dto->cover_image;
        }

        if ($dto->pdf !== null) {
            $data['pdf'] = $dto->pdf;
        }

        $book->update($data);

        return $book->fresh();
    }

    public function deleteBook(string $id): void
    {
        $this->findBook($id)->delete();
    }

    public function listBooks(array $filters = []): Collection
    {
        $query = Book::query()->select([
            'id', 'title', 'author', 'media', 'stock', 'digital_link',
            'reserved', 'reserve_expiration', 'reserved_to', 'fine',
            'created_at', 'updated_at', 'deleted_at',
            DB::raw('(cover_image IS NOT NULL) as has_cover'),
            DB::raw('(pdf IS NOT NULL) as has_pdf'),
        ]);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('author', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['media'])) {
            $query->where('media', $filters['media']);
        }

        return $query->get();
    }

    public function findBook(string $id): Book
    {
        return Book::findOrFail($id);
    }

    public function decrementStock(Book $book): void
    {
        if ($book->stock <= 0) {
            throw new OutOfStockException();
        }

        $book->decrement('stock');
    }

    public function incrementStock(Book $book): void
    {
        $book->increment('stock');
    }

    public function assertBookAvailable(Book $book): void
    {
        if ($book->media === 'physical' && $book->stock <= 0) {
            throw new OutOfStockException();
        }

        if ($book->media === 'digital' && $book->reserved) {
            throw new OutOfStockException('This digital book is currently reserved by another user.');
        }
    }
}
