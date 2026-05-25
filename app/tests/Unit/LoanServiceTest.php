<?php

namespace Tests\Unit;

use App\DTOs\CreateLoanDTO;
use App\DTOs\RenewLoanDTO;
use App\DTOs\ReturnLoanDTO;
use App\Exceptions\OutOfStockException;
use App\Exceptions\PendingFineException;
use App\Exceptions\UserBlockedException;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\BookService;
use App\Services\FineService;
use App\Services\LoanService;
use App\Services\LogService;
use App\Services\ReservationService;
use App\Services\UserService;
use Mockery;

class LoanServiceTest extends TestCase
{
    private UserService $userService;
    private BookService $bookService;
    private FineService $fineService;
    private ReservationService $reservationService;
    private LogService $logService;
    private LoanService $loanService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService        = Mockery::mock(UserService::class);
        $this->bookService        = Mockery::mock(BookService::class);
        $this->fineService        = Mockery::mock(FineService::class);
        $this->reservationService = Mockery::mock(ReservationService::class);
        $this->logService         = Mockery::mock(LogService::class);

        $this->loanService = new LoanService(
            $this->userService,
            $this->bookService,
            $this->fineService,
            $this->reservationService,
            $this->logService,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_loan_throws_when_user_is_blocked(): void
    {
        $user  = Mockery::mock(User::class);
        $actor = Mockery::mock(User::class);
        $dto   = new CreateLoanDTO('book-uuid', 'user-uuid', '2026-12-01');

        $this->userService->shouldReceive('findUser')->with('user-uuid')->andReturn($user);
        $this->userService->shouldReceive('assertUserNotBlocked')->with($user)->andThrow(new UserBlockedException());

        $this->expectException(UserBlockedException::class);
        $this->loanService->createLoan($dto, $actor);
    }

    public function test_create_loan_throws_when_user_has_pending_fines(): void
    {
        $user  = Mockery::mock(User::class);
        $actor = Mockery::mock(User::class);
        $dto   = new CreateLoanDTO('book-uuid', 'user-uuid', '2026-12-01');

        $this->userService->shouldReceive('findUser')->with('user-uuid')->andReturn($user);
        $this->userService->shouldReceive('assertUserNotBlocked')->with($user)->once();
        $this->userService->shouldReceive('assertNoPendingFines')->with($user)->andThrow(new PendingFineException());

        $this->expectException(PendingFineException::class);
        $this->loanService->createLoan($dto, $actor);
    }

    public function test_create_loan_throws_when_book_out_of_stock(): void
    {
        $user  = Mockery::mock(User::class);
        $book  = Mockery::mock(Book::class);
        $actor = Mockery::mock(User::class);
        $dto   = new CreateLoanDTO('book-uuid', 'user-uuid', '2026-12-01');

        $this->userService->shouldReceive('findUser')->andReturn($user);
        $this->userService->shouldReceive('assertUserNotBlocked')->once();
        $this->userService->shouldReceive('assertNoPendingFines')->once();
        $this->bookService->shouldReceive('findBook')->with('book-uuid')->andReturn($book);
        $this->bookService->shouldReceive('assertBookAvailable')->with($book)->andThrow(new OutOfStockException());

        $this->expectException(OutOfStockException::class);
        $this->loanService->createLoan($dto, $actor);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_renew_loan_throws_when_user_is_blocked(): void
    {
        $user = Mockery::mock(User::class);

        $loanMock = Mockery::mock('alias:App\Models\Loan');
        $loanMock->shouldReceive('findOrFail')->andReturn($loanMock);
        $loanMock->user = $user;

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('assertUserNotBlocked')->with($user)->andThrow(new UserBlockedException());

        $logService         = Mockery::mock(LogService::class);
        $bookService        = Mockery::mock(BookService::class);
        $fineService        = Mockery::mock(FineService::class);
        $reservationService = Mockery::mock(ReservationService::class);

        $loanService = new LoanService($userService, $bookService, $fineService, $reservationService, $logService);

        $dto   = new RenewLoanDTO('loan-uuid');
        $actor = Mockery::mock(User::class);

        $this->expectException(UserBlockedException::class);
        $loanService->renewLoan($dto, $actor);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_renew_loan_throws_when_book_has_pending_reservations(): void
    {
        $reservationBuilder = Mockery::mock('stdClass');
        $reservationBuilder->shouldReceive('where')->andReturnSelf();
        $reservationBuilder->shouldReceive('exists')->andReturn(true);

        $user = Mockery::mock(User::class);

        $reservationAlias = Mockery::mock('alias:App\Models\Reservation');
        $reservationAlias->shouldReceive('where')->andReturn($reservationBuilder);

        $loanMock = Mockery::mock('alias:App\Models\Loan');
        $loanMock->shouldReceive('findOrFail')->andReturn($loanMock);
        $loanMock->user    = $user;
        $loanMock->book_id = 'book-uuid';

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('assertUserNotBlocked')->once();

        $logService         = Mockery::mock(LogService::class);
        $bookService        = Mockery::mock(BookService::class);
        $fineService        = Mockery::mock(FineService::class);
        $reservationService = Mockery::mock(ReservationService::class);

        $loanService = new LoanService($userService, $bookService, $fineService, $reservationService, $logService);

        $dto   = new RenewLoanDTO('loan-uuid');
        $actor = Mockery::mock(User::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot renew: book has pending reservations.');
        $loanService->renewLoan($dto, $actor);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_return_loan_creates_fine_when_returned_late(): void
    {
        $book = Mockery::mock(Book::class);
        $book->shouldReceive('getAttribute')->with('media')->andReturn('physical');
        $book->shouldReceive('getAttribute')->with('title')->andReturn('Test Book');
        $book->shouldReceive('update')->andReturn(true);
        $book->shouldReceive('fresh')->andReturnSelf();

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('John');

        $pastDue = now()->subDays(3);

        $loanMock = Mockery::mock('alias:App\Models\Loan');
        $loanMock->shouldReceive('with')->andReturn($loanMock);
        $loanMock->shouldReceive('findOrFail')->andReturn($loanMock);
        $loanMock->book     = $book;
        $loanMock->user     = $user;
        $loanMock->user_id  = 'user-uuid';
        $loanMock->due_date = $pastDue;
        $loanMock->shouldReceive('update')->andReturn(true);
        $loanMock->shouldReceive('fresh')->andReturnSelf();

        $userService = Mockery::mock(UserService::class);
        $bookService = Mockery::mock(BookService::class);
        $bookService->shouldReceive('incrementStock')->with($book)->once();

        $fineService = Mockery::mock(FineService::class);
        $fineService->shouldReceive('createFine')->with($loanMock)->once();

        $userService->shouldReceive('blockUser')->with('user-uuid')->once();

        $reservationService = Mockery::mock(ReservationService::class);
        $reservationService->shouldReceive('fulfillNext')->with($book)->once();

        $logService = Mockery::mock(LogService::class);
        $logService->shouldReceive('log')->once();

        $loanService = new LoanService($userService, $bookService, $fineService, $reservationService, $logService);

        $dto   = new ReturnLoanDTO('loan-uuid');
        $actor = Mockery::mock(User::class);

        $loanService->returnLoan($dto, $actor);

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_return_loan_does_not_create_fine_when_returned_on_time(): void
    {
        $book = Mockery::mock(Book::class);
        $book->shouldReceive('getAttribute')->with('media')->andReturn('physical');
        $book->shouldReceive('getAttribute')->with('title')->andReturn('Test Book');
        $book->shouldReceive('update')->andReturn(true);
        $book->shouldReceive('fresh')->andReturnSelf();

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('John');

        $futureDue = now()->addDays(5);

        $loanMock = Mockery::mock('alias:App\Models\Loan');
        $loanMock->shouldReceive('with')->andReturn($loanMock);
        $loanMock->shouldReceive('findOrFail')->andReturn($loanMock);
        $loanMock->book     = $book;
        $loanMock->user     = $user;
        $loanMock->user_id  = 'user-uuid';
        $loanMock->due_date = $futureDue;
        $loanMock->shouldReceive('update')->andReturn(true);
        $loanMock->shouldReceive('fresh')->andReturnSelf();

        $userService = Mockery::mock(UserService::class);
        $bookService = Mockery::mock(BookService::class);
        $bookService->shouldReceive('incrementStock')->with($book)->once();

        $fineService = Mockery::mock(FineService::class);
        $fineService->shouldReceive('createFine')->never();

        $userService->shouldReceive('blockUser')->never();

        $reservationService = Mockery::mock(ReservationService::class);
        $reservationService->shouldReceive('fulfillNext')->with($book)->once();

        $logService = Mockery::mock(LogService::class);
        $logService->shouldReceive('log')->once();

        $loanService = new LoanService($userService, $bookService, $fineService, $reservationService, $logService);

        $dto   = new ReturnLoanDTO('loan-uuid');
        $actor = Mockery::mock(User::class);

        $loanService->returnLoan($dto, $actor);

        $this->assertTrue(true);
    }
}
