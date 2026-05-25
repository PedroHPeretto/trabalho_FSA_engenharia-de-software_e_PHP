<?php

namespace Tests\Integration;

use App\Mail\DueDateReminderMail;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationIntegrationTest extends TestCase
{
    public function test_send_due_date_reminders_queues_mail_for_loans_due_tomorrow(): void
    {
        Mail::fake();

        $customer = User::factory()->create();
        $book     = Book::factory()->create(['stock' => 1]);

        Loan::factory()->create([
            'book_id'  => $book->id,
            'user_id'  => $customer->id,
            'due_date' => now()->addDay()->startOfDay(),
        ]);

        $this->artisan('loans:notify-due');

        Mail::assertQueued(DueDateReminderMail::class, 1);
    }

    public function test_send_due_date_reminders_does_not_queue_mail_when_no_loans_due_tomorrow(): void
    {
        Mail::fake();

        $customer = User::factory()->create();
        $book     = Book::factory()->create(['stock' => 1]);

        Loan::factory()->create([
            'book_id'  => $book->id,
            'user_id'  => $customer->id,
            'due_date' => now()->addDays(3)->startOfDay(),
        ]);

        $this->artisan('loans:notify-due');

        Mail::assertNothingQueued();
    }

    public function test_send_due_date_reminders_skips_already_returned_loans(): void
    {
        Mail::fake();

        $customer = User::factory()->create();
        $book     = Book::factory()->create(['stock' => 1]);

        Loan::factory()->create([
            'book_id'     => $book->id,
            'user_id'     => $customer->id,
            'due_date'    => now()->addDay()->startOfDay(),
            'returned_at' => now()->subDay(),
        ]);

        $this->artisan('loans:notify-due');

        Mail::assertNothingQueued();
    }

    public function test_artisan_command_exits_successfully(): void
    {
        Mail::fake();

        $this->artisan('loans:notify-due')->assertExitCode(0);
    }
}
