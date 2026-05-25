<?php

namespace App\Services;

use App\Mail\DueDateReminderMail;
use App\Models\Loan;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendDueDateReminders(): void
    {
        $loans = Loan::with(['user', 'book'])
            ->whereNull('returned_at')
            ->whereDate('due_date', today()->addDay())
            ->get();

        foreach ($loans as $loan) {
            Mail::to($loan->user->email)->queue(new DueDateReminderMail($loan));
        }
    }
}
