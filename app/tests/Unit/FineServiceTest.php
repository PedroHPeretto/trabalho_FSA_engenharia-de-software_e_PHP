<?php

namespace Tests\Unit;

use App\DTOs\PayFineDTO;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use App\Services\FineService;
use App\Services\UserService;
use Mockery;

class FineServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_create_fine_is_unpaid_by_default(): void
    {
        $fineMock = Mockery::mock('alias:App\Models\Fine');
        $fineMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $data) => $data['paid'] === false))
            ->andReturn($fineMock);

        $loan = Mockery::mock(Loan::class);
        $loan->shouldReceive('getAttribute')->with('id')->andReturn('loan-uuid');
        $loan->shouldReceive('getAttribute')->with('user_id')->andReturn('user-uuid');

        $userService = Mockery::mock(UserService::class);
        $service     = new FineService($userService);

        $service->createFine($loan);

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_create_fine_uses_config_fine_amount(): void
    {
        $fineMock = Mockery::mock('alias:App\Models\Fine');
        $fineMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $data) => $data['amount'] == 100.00))
            ->andReturn($fineMock);

        $loan = Mockery::mock(Loan::class);
        $loan->shouldReceive('getAttribute')->with('id')->andReturn('loan-uuid');
        $loan->shouldReceive('getAttribute')->with('user_id')->andReturn('user-uuid');

        $userService = Mockery::mock(UserService::class);
        $service     = new FineService($userService);

        $service->createFine($loan);

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_pay_fine_unblocks_user_when_no_remaining_unpaid_fines(): void
    {
        $builderMock = Mockery::mock('stdClass');
        $builderMock->shouldReceive('where')->andReturnSelf();
        $builderMock->shouldReceive('exists')->andReturn(false);

        $fineMock = Mockery::mock('alias:App\Models\Fine');
        $fineMock->shouldReceive('findOrFail')->andReturn($fineMock);
        $fineMock->shouldReceive('update')->with(['paid' => true])->once();
        $fineMock->user_id = 'user-uuid';
        $fineMock->shouldReceive('where')->andReturn($builderMock);
        $fineMock->shouldReceive('fresh')->andReturnSelf();

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('unblockUser')->with('user-uuid')->once();

        $service = new FineService($userService);
        $dto     = new PayFineDTO('fine-uuid');
        $actor   = Mockery::mock(User::class);

        $service->payFine($dto, $actor);

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_pay_fine_does_not_unblock_user_when_remaining_unpaid_fines_exist(): void
    {
        $builderMock = Mockery::mock('stdClass');
        $builderMock->shouldReceive('where')->andReturnSelf();
        $builderMock->shouldReceive('exists')->andReturn(true);

        $fineMock = Mockery::mock('alias:App\Models\Fine');
        $fineMock->shouldReceive('findOrFail')->andReturn($fineMock);
        $fineMock->shouldReceive('update')->with(['paid' => true])->once();
        $fineMock->user_id = 'user-uuid';
        $fineMock->shouldReceive('where')->andReturn($builderMock);
        $fineMock->shouldReceive('fresh')->andReturnSelf();

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('unblockUser')->never();

        $service = new FineService($userService);
        $dto     = new PayFineDTO('fine-uuid');
        $actor   = Mockery::mock(User::class);

        $service->payFine($dto, $actor);

        $this->assertTrue(true);
    }
}
