<?php

namespace App\Services;

use App\DTOs\CreateLoanDTO;
use App\DTOs\RenewLoanDTO;
use App\DTOs\ReturnLoanDTO;
use App\Exceptions\RenewalBlockedException;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;

class LoanService
{
    public function __construct(
        private readonly UserService $userService,
        private readonly BookService $bookService,
        private readonly FineService $fineService,
        private readonly ReservationService $reservationService,
        private readonly LogService $logService,
    ) {}

    public function createLoan(CreateLoanDTO $dto, User $actor): Loan
    {
        $user = $this->userService->findUser($dto->user_id);
        $this->userService->assertUserNotBlocked($user);
        $this->userService->assertNoPendingFines($user);

        $book = $this->bookService->findBook($dto->book_id);
        $this->bookService->assertBookAvailable($book);

        $loan = Loan::create([
            'book_id'   => $book->id,
            'user_id'   => $user->id,
            'loaned_at' => now(),
            'due_date'  => $dto->due_date,
        ]);

        if ($book->media === 'physical') {
            $this->bookService->decrementStock($book);
        }

        if ($book->media === 'digital') {
            $book->update([
                'reserved'    => true,
                'reserved_to' => $user->id,
            ]);
        }

        $this->logService->log(
            $actor,
            'loan.create',
            "Loan created for book '{$book->title}' to user '{$user->name}'"
        );

        return $loan;
    }

    public function renewLoan(RenewLoanDTO $dto, User $actor): Loan
    {
        $loan = Loan::findOrFail($dto->loan_id);

        $this->userService->assertUserNotBlocked($loan->user);

        $hasReservations = Reservation::where('book_id', $loan->book_id)
            ->where('status', 'pending')
            ->exists();

        if ($hasReservations) {
            throw new RenewalBlockedException();
        }

        $renewalDays = (int) config('library.renewal_days', 14);
        $loan->update(['due_date' => $loan->due_date->addDays($renewalDays)]);

        $this->logService->log(
            $actor,
            'loan.renew',
            "Loan {$loan->id} renewed by '{$actor->name}'"
        );

        return $loan->fresh();
    }

    public function returnLoan(ReturnLoanDTO $dto, User $actor): Loan
    {
        $loan = Loan::with(['book', 'user'])->findOrFail($dto->loan_id);

        $returnedAt = now();
        $loan->update(['returned_at' => $returnedAt]);

        $book = $loan->book;

        if ($book->media === 'physical') {
            $this->bookService->incrementStock($book);
        }

        if ($book->media === 'digital') {
            $book->update([
                'reserved'    => false,
                'reserved_to' => null,
            ]);
        }

        if ($returnedAt->gt($loan->due_date)) {
            $this->fineService->createFine($loan);
            $loan->update(['has_fine' => true]);
            $this->userService->blockUser($loan->user_id);
        }

        $this->reservationService->fulfillNext($book->fresh());

        $this->logService->log(
            $actor,
            'loan.return',
            "Book '{$book->title}' returned by user '{$loan->user->name}'"
        );

        return $loan->fresh();
    }
}
