<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DueDateReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Loan $loan,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete: Devolução de Livro Amanhã',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.due-date-reminder',
        );
    }
}
