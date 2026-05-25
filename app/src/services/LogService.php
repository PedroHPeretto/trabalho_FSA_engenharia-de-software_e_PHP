<?php

namespace App\Services;

use App\Models\Log;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class LogService
{
    public function log(User $actor, string $action, string $description): void
    {
        Log::create([
            'made_by'     => $actor->id,
            'action'      => $action,
            'description' => $description,
            'date'        => now(),
        ]);
    }

    public function listLogs(array $filters = []): Collection
    {
        $query = Log::with('actor')->orderByDesc('date');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('actor', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    public function distinctActions(): array
    {
        return Log::distinct()->orderBy('action')->pluck('action')->toArray();
    }
}
