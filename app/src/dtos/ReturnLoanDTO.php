<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ReturnLoanDTO
{
    public function __construct(
        public string $loan_id,
    ) {}

    public static function fromRequest(Request $request, string $loanId): static
    {
        $request->validate([
            'loan_id' => ['sometimes', 'uuid', 'exists:loans,id'],
        ]);

        return new static(loan_id: $loanId);
    }
}
