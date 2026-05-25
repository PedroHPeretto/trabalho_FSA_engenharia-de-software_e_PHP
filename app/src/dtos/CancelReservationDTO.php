<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CancelReservationDTO
{
    public function __construct(
        public string $reservation_id,
    ) {}

    public static function fromRequest(Request $request, string $reservationId): static
    {
        $request->validate([
            'reservation_id' => ['sometimes', 'uuid', 'exists:reservations,id'],
        ]);

        return new static(reservation_id: $reservationId);
    }
}
