<?php

namespace App\Controllers;

use App\DTOs\CreateLoanDTO;
use App\DTOs\RenewLoanDTO;
use App\DTOs\ReturnLoanDTO;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoanController
{
    public function __construct(
        private readonly LoanService $loanService,
    ) {}

    public function index(): View
    {
        Log::debug('[Loan] Listando todos os empréstimos', ['user_id' => auth()->id()]);

        $loans = Loan::with(['book', 'user'])->latest()->get();

        Log::debug('[Loan] Listagem concluída', ['total' => $loans->count()]);

        return view('loans.index', compact('loans'));
    }

    public function show(string $id): View
    {
        Log::debug('[Loan] Exibindo detalhes do empréstimo', [
            'loan_id' => $id,
            'user_id' => auth()->id(),
        ]);

        $loan = Loan::with(['book', 'user', 'fine'])->findOrFail($id);

        Log::debug('[Loan] Empréstimo encontrado', [
            'loan_id' => $loan->id,
            'book_id' => $loan->book?->id,
            'status'  => $loan->status,
        ]);

        return view('loans.show', compact('loan'));
    }

    public function store(Request $request): RedirectResponse
    {
        Log::debug('[Loan] Tentativa de criação de empréstimo', [
            'user_id'     => auth()->id(),
            'book_id'     => $request->input('book_id'),
            'borrower_id' => $request->input('user_id'),
        ]);

        $dto  = CreateLoanDTO::fromRequest($request);
        $loan = $this->loanService->createLoan($dto, auth()->user());

        Log::debug('[Loan] Empréstimo criado com sucesso', [
            'loan_id'      => $loan->id,
            'book_id'      => $loan->book_id,
            'borrower_id'  => $loan->user_id,
            'due_date'     => $loan->due_date,
        ]);

        return redirect()->route('loans.show', $loan->id)
            ->with('success', 'Loan created successfully.');
    }

    public function renew(Request $request, string $id): RedirectResponse
    {
        Log::debug('[Loan] Tentativa de renovação de empréstimo', [
            'loan_id' => $id,
            'user_id' => auth()->id(),
        ]);

        $dto = RenewLoanDTO::fromRequest($request, $id);
        $this->loanService->renewLoan($dto, auth()->user());

        Log::debug('[Loan] Empréstimo renovado com sucesso', ['loan_id' => $id]);

        return redirect()->route('loans.show', $id)
            ->with('success', 'Loan renewed successfully.');
    }

    public function return(Request $request, string $id): RedirectResponse
    {
        Log::debug('[Loan] Tentativa de devolução de livro', [
            'loan_id' => $id,
            'user_id' => auth()->id(),
        ]);

        $dto = ReturnLoanDTO::fromRequest($request, $id);
        $this->loanService->returnLoan($dto, auth()->user());

        Log::debug('[Loan] Livro devolvido com sucesso', ['loan_id' => $id]);

        return redirect()->route('loans.show', $id)
            ->with('success', 'Book returned successfully.');
    }
}
