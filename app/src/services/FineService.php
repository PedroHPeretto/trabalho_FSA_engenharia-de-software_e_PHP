<?php

namespace App\Services;

use App\DTOs\PayFineDTO;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Collection;

class FineService
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function createFine(Loan $loan): Fine
    {
        return Fine::create([
            'loan_id' => $loan->id,
            'user_id' => $loan->user_id,
            'amount'  => config('library.fine_amount', 100.00),
            'paid'    => false,
        ]);
    }

    public function payFine(PayFineDTO $dto, User $actor): Fine
    {
        $fine = Fine::findOrFail($dto->fine_id);

        $fine->update(['paid' => true]);

        $hasRemainingFines = Fine::where('user_id', $fine->user_id)
            ->where('paid', false)
            ->exists();

        if (!$hasRemainingFines) {
            $this->userService->unblockUser($fine->user_id);
        }

        return $fine->fresh();
    }

    public function listUnpaidFines(string $userId): Collection
    {
        return Fine::where('user_id', $userId)
            ->where('paid', false)
            ->with(['loan.book'])
            ->get();
    }
}
