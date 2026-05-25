<?php

namespace App\Services;

use App\DTOs\CancelReservationDTO;
use App\DTOs\CreateReservationDTO;
use App\Exceptions\BookAvailableException;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ReservationService
{
    public function __construct(
        private readonly UserService $userService,
        private readonly LogService $logService,
    ) {}

    public function createReservation(CreateReservationDTO $dto, User $actor): Reservation
    {
        $user = $this->userService->findUser($dto->user_id);
        $this->userService->assertUserNotBlocked($user);
        $this->userService->assertNoPendingFines($user);

        $book = Book::findOrFail($dto->book_id);

        if ($book->media === 'physical' && $book->stock > 0) {
            throw new BookAvailableException();
        }

        if ($book->media === 'digital' && !$book->reserved) {
            throw new BookAvailableException();
        }

        $expiryDays = (int) config('library.reservation_expiry_days', 3);

        $reservation = Reservation::create([
            'book_id'         => $book->id,
            'user_id'         => $user->id,
            'reserved_at'     => now(),
            'expiration_date' => now()->addDays($expiryDays),
            'status'          => 'pending',
        ]);

        $this->logService->log(
            $actor,
            'reservation.create',
            "Reservation created for book '{$book->title}' by user '{$user->name}'"
        );

        return $reservation;
    }

    public function cancelReservation(CancelReservationDTO $dto, User $actor): Reservation
    {
        $reservation = Reservation::findOrFail($dto->reservation_id);

        $reservation->update(['status' => 'cancelled']);

        $this->logService->log(
            $actor,
            'reservation.cancel',
            "Reservation {$reservation->id} cancelled by '{$actor->name}'"
        );

        return $reservation->fresh();
    }

    public function fulfillNext(Book $book): void
    {
        $reservation = Reservation::where('book_id', $book->id)
            ->where('status', 'pending')
            ->orderBy('reserved_at')
            ->first();

        if (!$reservation) {
            return;
        }

        $reservation->update(['status' => 'fulfilled']);

        // Notify the user that their reservation is ready
        try {
            $user = $reservation->user;
            Mail::to($user->email)->queue(
                new \App\Mail\ReservationFulfilledMail($reservation)
            );
        } catch (\Throwable) {
            // Notification failure should not break the return flow
        }
    }
}
