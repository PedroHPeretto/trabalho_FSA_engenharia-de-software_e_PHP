<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CreateBookDTO
{
    public function __construct(
        public string $title,
        public string $author,
        public string $media,
        public ?int $stock,
        public ?string $digital_link,
        public ?string $cover_image,
        public ?string $pdf,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'author'       => ['required', 'string', 'max:255'],
            'media'        => ['required', 'in:physical,digital'],
            'stock'        => ['required_if:media,physical', 'nullable', 'integer', 'min:0'],
            'digital_link' => ['nullable', 'url'],
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
            title: $validated['title'],
            author: $validated['author'],
            media: $validated['media'],
            stock: $validated['stock'] ?? null,
            digital_link: $validated['digital_link'] ?? null,
            cover_image: $coverImageBytes,
            pdf: $pdfBytes,
        );
    }
}
