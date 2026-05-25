<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    protected $signature = 'loans:notify-due';

    protected $description = 'Send due date reminder emails 24h in advance';

    public function handle(NotificationService $service): void
    {
        $service->sendDueDateReminders();
        $this->info('Due date reminders sent.');
    }
}
