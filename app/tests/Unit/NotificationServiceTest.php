<?php

namespace Tests\Unit;

use App\Mail\DueDateReminderMail;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Mockery;

class NotificationServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_send_due_date_reminders_queues_mail_for_each_qualifying_loan(): void
    {
        $user = Mockery::mock('stdClass');
        $user->email = 'user@example.com';

        $loanMock = Mockery::mock('alias:App\Models\Loan');
        $loanMock->user = $user;
        $loanMock->shouldReceive('with->whereNull->whereDate->get')
            ->andReturn(new Collection([$loanMock, $loanMock]));

        $mailMock = Mockery::mock('alias:Illuminate\Support\Facades\Mail');
        $mailMock->shouldReceive('to->queue')->twice();

        $service = new NotificationService();
        $service->sendDueDateReminders();

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_send_due_date_reminders_queues_no_mail_when_no_loans_due(): void
    {
        $loanMock = Mockery::mock('alias:App\Models\Loan');
        $loanMock->shouldReceive('with->whereNull->whereDate->get')
            ->andReturn(new Collection([]));

        $mailMock = Mockery::mock('alias:Illuminate\Support\Facades\Mail');
        $mailMock->shouldReceive('to->queue')->never();

        $service = new NotificationService();
        $service->sendDueDateReminders();

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_send_due_date_reminders_queues_correct_mail_class(): void
    {
        $user = Mockery::mock('stdClass');
        $user->email = 'reader@example.com';

        $loanMock = Mockery::mock('alias:App\Models\Loan');
        $loanMock->user = $user;
        $loanMock->shouldReceive('with->whereNull->whereDate->get')
            ->andReturn(new Collection([$loanMock]));

        $mailMock = Mockery::mock('alias:Illuminate\Support\Facades\Mail');
        $mailMock->shouldReceive('to')
            ->with('reader@example.com')
            ->andReturnSelf();
        $mailMock->shouldReceive('queue')
            ->once()
            ->with(Mockery::type(DueDateReminderMail::class));

        $service = new NotificationService();
        $service->sendDueDateReminders();

        $this->assertTrue(true);
    }
}
