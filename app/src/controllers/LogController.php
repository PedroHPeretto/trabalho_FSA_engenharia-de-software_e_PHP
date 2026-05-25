<?php

namespace App\Controllers;

use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LogController
{
    public function __construct(
        private readonly LogService $logService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'action', 'date_from', 'date_to']);

        Log::debug('[Logs] Listando logs do sistema', [
            'admin_id' => auth()->id(),
            'filters'  => $filters,
        ]);

        $logs    = $this->logService->listLogs($filters);
        $actions = $this->logService->distinctActions();

        Log::debug('[Logs] Listagem concluída', ['total' => $logs->count()]);

        return view('logs.index', compact('logs', 'actions', 'filters'));
    }
}
