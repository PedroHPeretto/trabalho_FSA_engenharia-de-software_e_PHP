<?php

namespace App\Controllers;

use App\DTOs\PayFineDTO;
use App\Models\Fine;
use App\Services\FineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FineController
{
    public function __construct(
        private readonly FineService $fineService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        Log::debug('[Fine] Listando multas', [
            'user_id' => $user->id,
            'role'    => $user->role,
        ]);

        $fines = in_array($user->role, ['librarian', 'admin'])
            ? Fine::with(['user', 'loan.book'])->latest()->get()
            : Fine::where('user_id', $user->id)->with(['loan.book'])->latest()->get();

        Log::debug('[Fine] Listagem concluída', [
            'user_id' => $user->id,
            'total'   => $fines->count(),
        ]);

        return view('fines.index', compact('fines'));
    }

    public function pay(Request $request, string $id): RedirectResponse
    {
        Log::debug('[Fine] Tentativa de pagamento de multa', [
            'fine_id' => $id,
            'user_id' => auth()->id(),
        ]);

        $dto = PayFineDTO::fromRequest($request, $id);
        $this->fineService->payFine($dto, auth()->user());

        Log::debug('[Fine] Multa paga com sucesso', [
            'fine_id' => $id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('fines.index')
            ->with('success', 'Fine paid successfully. User unblocked if no remaining fines.');
    }
}
