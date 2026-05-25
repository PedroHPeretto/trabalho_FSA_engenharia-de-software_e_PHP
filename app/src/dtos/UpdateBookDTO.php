<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class UpdateBookDTO
{
    public function __construct(
        public ?string $title,
        public ?string $author,
        public ?string $media,
        public ?int $stock,
        public ?string $digital_link,
        public ?string $cover_image,
        public ?string $pdf,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'title'        => ['sometimes', 'string', 'max:255'],
            'author'       => ['sometimes', 'string', 'max:255'],
            'media'        => ['sometimes', 'in:physical,digital'],
            'stock'        => ['sometimes', 'nullable', 'integer', 'min:0'],
            'digital_link' => ['sometimes', 'nullable', 'url'],
            'cover_image'  => ['nullable', 'image', 'max:5120'],
            'pdf'          => ['nullable', 'mimes:pdf', 'max:20480'],
        ]);

        $coverImageBytes = $request->hasFile('cover_image')
            ? file_get_contents($request->file('cover_image')->getRealPath())
            : null;

        $pdfBytes = $request->hasFile('pdf')
            ? file_get_contents($request->file('pdf')->getRealPath())
            : null;

        return new static(
            title: $validated['title'] ?? null,
            author: $validated['author'] ?? null,
            media: $validated['media'] ?? null,
            stock: $validated['stock'] ?? null,
            digital_link: $validated['digital_link'] ?? null,
            cover_image: $coverImageBytes,
            pdf: $pdfBytes,
        );
    }
}
