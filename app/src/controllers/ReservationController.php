<?php

namespace App\Controllers;

use App\DTOs\CancelReservationDTO;
use App\DTOs\CreateReservationDTO;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReservationController
{
    public function __construct(
        private readonly ReservationService $reservationService,
    ) {}

    public function index(): View
    {
        Log::debug('[Reservation] Listando reservas do usuário', ['user_id' => auth()->id()]);

        $reservations = Reservation::where('user_id', auth()->id())
            ->with('book')
            ->latest()
            ->get();

        Log::debug('[Reservation] Listagem concluída', [
            'user_id' => auth()->id(),
            'total'   => $reservations->count(),
        ]);

        return view('reservations.index', compact('reservations'));
    }

    public function store(Request $request): RedirectResponse
    {
        Log::debug('[Reservation] Tentativa de criação de reserva', [
            'user_id' => auth()->id(),
            'book_id' => $request->input('book_id'),
        ]);

        $dto         = CreateReservationDTO::fromRequest($request);
        $reservation = $this->reservationService->createReservation($dto, auth()->user());

        Log::debug('[Reservation] Reserva criada com sucesso', [
            'reservation_id' => $reservation->id,
            'user_id'        => auth()->id(),
            'book_id'        => $reservation->book_id,
        ]);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function cancel(Request $request, string $id): RedirectResponse
    {
        Log::debug('[Reservation] Tentativa de cancelamento de reserva', [
            'reservation_id' => $id,
            'user_id'        => auth()->id(),
        ]);

        $dto = CancelReservationDTO::fromRequest($request, $id);
        $this->reservationService->cancelReservation($dto, auth()->user());

        Log::debug('[Reservation] Reserva cancelada com sucesso', [
            'reservation_id' => $id,
            'user_id'        => auth()->id(),
        ]);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation cancelled.');
    }
}
