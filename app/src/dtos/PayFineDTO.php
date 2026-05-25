<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class PayFineDTO
{
    public function __construct(
        public string $fine_id,
    ) {}

    public static function fromRequest(Request $request, string $fineId): static
    {
        $request->validate([
            'fine_id' => ['sometimes', 'uuid', 'exists:fines,id'],
        ]);

        return new static(fine_id: $fineId);
    }
}
