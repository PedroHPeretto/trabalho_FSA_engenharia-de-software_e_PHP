<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookMediaSeeder extends Seeder
{
    /**
     * Maps each book UUID to its cover image filename (relative to /images/).
     * Maps digital book UUIDs to their PDF filename (relative to /pdfs/).
     */
    public function run(): void
    {
        $imagesPath = base_path('../images');
        $pdfsPath   = base_path('../pdfs');

        $coverMap = [
            'b0000000-0000-0000-0000-000000000001' => 'dom-quixote.jpg',
            'b0000000-0000-0000-0000-000000000002' => 'o-senhor-dos-aneis.jpg',
            'b0000000-0000-0000-0000-000000000003' => '1984.jpg',
            'b0000000-0000-0000-0000-000000000004' => 'o-pequeno-principe.png',
            'b0000000-0000-0000-0000-000000000005' => 'crime-e-castigo.webp',
            'b0000000-0000-0000-0000-000000000006' => 'clean-code.jpg',
            'b0000000-0000-0000-0000-000000000007' => 'design-patterns.jpg',
            'b0000000-0000-0000-0000-000000000008' => 'pragmatic-programmer.png',
        ];

        $pdfMap = [
            'b0000000-0000-0000-0000-000000000006' => 'clean-code.pdf',
            'b0000000-0000-0000-0000-000000000007' => 'design-patterns.pdf',
            'b0000000-0000-0000-0000-000000000008' => 'pragmatic-programmer.pdf',
        ];

        foreach ($coverMap as $bookId => $filename) {
            $filePath = "{$imagesPath}/{$filename}";

            if (!file_exists($filePath)) {
                $this->command->warn("Cover image not found: {$filePath}");
                continue;
            }

            DB::table('books')
                ->where('id', $bookId)
                ->update(['cover_image' => file_get_contents($filePath)]);
        }

        foreach ($pdfMap as $bookId => $filename) {
            $filePath = "{$pdfsPath}/{$filename}";

            if (!file_exists($filePath)) {
                $this->command->warn("PDF not found: {$filePath}");
                continue;
            }

            DB::table('books')
                ->where('id', $bookId)
                ->update(['pdf' => file_get_contents($filePath)]);
        }

        $this->command->info('Book media (covers + PDFs) seeded successfully.');
    }
}
