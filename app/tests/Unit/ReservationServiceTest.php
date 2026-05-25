<?php

namespace Tests\Unit;

use App\DTOs\CancelReservationDTO;
use App\DTOs\CreateReservationDTO;
use App\Exceptions\BookAvailableException;
use App\Exceptions\PendingFineException;
use App\Exceptions\UserBlockedException;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use App\Services\LogService;
use App\Services\ReservationService;
use App\Services\UserService;
use Mockery;

class ReservationServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_reservation_throws_when_user_is_blocked(): void
    {
        $userService = Mockery::mock(UserService::class);
        $logService  = Mockery::mock(LogService::class);
        $service     = new ReservationService($userService, $logService);

        $user  = Mockery::mock(User::class);
        $actor = Mockery::mock(User::class);
        $dto   = new CreateReservationDTO('book-uuid', 'user-uuid');

        $userService->shouldReceive('findUser')->andReturn($user);
        $userService->shouldReceive('assertUserNotBlocked')->andThrow(new UserBlockedException());

        $this->expectException(UserBlockedException::class);
        $service->createReservation($dto, $actor);
    }

    public function test_create_reservation_throws_when_user_has_pending_fines(): void
    {
        $userService = Mockery::mock(UserService::class);
        $logService  = Mockery::mock(LogService::class);
        $service     = new ReservationService($userService, $logService);

        $user  = Mockery::mock(User::class);
        $actor = Mockery::mock(User::class);
        $dto   = new CreateReservationDTO('book-uuid', 'user-uuid');

        $userService->shouldReceive('findUser')->andReturn($user);
        $userService->shouldReceive('assertUserNotBlocked')->once();
        $userService->shouldReceive('assertNoPendingFines')->andThrow(new PendingFineException());

        $this->expectException(PendingFineException::class);
        $service->createReservation($dto, $actor);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_create_reservation_throws_when_physical_book_is_available(): void
    {
        $bookMock = Mockery::mock('alias:App\Models\Book');
        $bookMock->shouldReceive('findOrFail')->andReturn($bookMock);
        $bookMock->media = 'physical';
        $bookMock->stock = 3;
        $bookMock->title = 'Test Book';

        $userService = Mockery::mock(UserService::class);
        $logService  = Mockery::mock(LogService::class);
        $service     = new ReservationService($userService, $logService);

        $user  = Mockery::mock(User::class);
        $actor = Mockery::mock(User::class);
        $dto   = new CreateReservationDTO('book-uuid', 'user-uuid');

        $userService->shouldReceive('findUser')->andReturn($user);
        $userService->shouldReceive('assertUserNotBlocked')->once();
        $userService->shouldReceive('assertNoPendingFines')->once();

        $this->expectException(BookAvailableException::class);
        $service->createReservation($dto, $actor);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_create_reservation_throws_when_digital_book_is_not_reserved(): void
    {
        $bookMock = Mockery::mock('alias:App\Models\Book');
        $bookMock->shouldReceive('findOrFail')->andReturn($bookMock);
        $bookMock->media    = 'digital';
        $bookMock->reserved = false;
        $bookMock->title    = 'Digital Book';

        $userService = Mockery::mock(UserService::class);
        $logService  = Mockery::mock(LogService::class);
        $service     = new ReservationService($userService, $logService);

        $user  = Mockery::mock(User::class);
        $actor = Mockery::mock(User::class);
        $dto   = new CreateReservationDTO('book-uuid', 'user-uuid');

        $userService->shouldReceive('findUser')->andReturn($user);
        $userService->shouldReceive('assertUserNotBlocked')->once();
        $userService->shouldReceive('assertNoPendingFines')->once();

        $this->expectException(BookAvailableException::class);
        $service->createReservation($dto, $actor);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_cancel_reservation_sets_status_to_cancelled(): void
    {
        $reservationMock = Mockery::mock('alias:App\Models\Reservation');
        $reservationMock->shouldReceive('findOrFail')->andReturn($reservationMock);
        $reservationMock->shouldReceive('update')->with(['status' => 'cancelled'])->once()->andReturn(true);
        $reservationMock->id = 'reservation-uuid';
        $reservationMock->shouldReceive('fresh')->andReturnSelf();

        $logMock = Mockery::mock('alias:App\Models\Log');
        $logMock->shouldReceive('create')->andReturn(true);

        $actor = Mockery::mock(User::class);
        $actor->shouldReceive('getAttribute')->with('id')->andReturn('actor-uuid');
        $actor->shouldReceive('getAttribute')->with('name')->andReturn('Admin');

        $userService = Mockery::mock(UserService::class);
        $logService  = new \App\Services\LogService();
        $service     = new ReservationService($userService, $logService);

        $dto = new CancelReservationDTO('reservation-uuid');

        $result = $service->cancelReservation($dto, $actor);

        $this->assertInstanceOf(Reservation::class, $result);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_fulfill_next_picks_oldest_pending_reservation(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn('user@example.com');

        $reservationMock = Mockery::mock('alias:App\Models\Reservation');
        $reservationMock->shouldReceive('where->where->orderBy->first')->andReturn($reservationMock);
        $reservationMock->shouldReceive('update')->with(['status' => 'fulfilled'])->once();
        $reservationMock->shouldReceive('getAttribute')->with('user')->andReturn($user);

        $mailMock = Mockery::mock('alias:Illuminate\Support\Facades\Mail');
        $mailMock->shouldReceive('to->queue')->andReturn(true);

        $book = Mockery::mock(Book::class);
        $book->shouldReceive('getAttribute')->with('id')->andReturn('book-uuid');

        $userService = Mockery::mock(UserService::class);
        $logService  = Mockery::mock(LogService::class);
        $service     = new ReservationService($userService, $logService);

        $service->fulfillNext($book);

        $this->assertTrue(true);
    }
}
