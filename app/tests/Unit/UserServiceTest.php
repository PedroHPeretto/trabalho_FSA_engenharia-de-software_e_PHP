<?php

namespace Tests\Unit;

use App\Exceptions\PendingFineException;
use App\Exceptions\UserBlockedException;
use App\Models\User;
use App\Services\UserService;
use Mockery;

class UserServiceTest extends TestCase
{
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_assert_user_not_blocked_throws_when_blocked(): void
    {
        $user          = new User();
        $user->blocked = true;

        $this->expectException(UserBlockedException::class);
        $this->service->assertUserNotBlocked($user);
    }

    public function test_assert_user_not_blocked_passes_when_not_blocked(): void
    {
        $user          = new User();
        $user->blocked = false;

        $this->service->assertUserNotBlocked($user);

        $this->assertTrue(true);
    }

    public function test_assert_user_not_blocked_passes_when_blocked_is_null(): void
    {
        $user          = new User();
        $user->blocked = null;

        $this->service->assertUserNotBlocked($user);

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_assert_no_pending_fines_throws_when_user_has_unpaid_fines(): void
    {
        $builder = Mockery::mock('stdClass');
        $builder->shouldReceive('where')->andReturnSelf();
        $builder->shouldReceive('exists')->andReturn(true);

        $fineMock = Mockery::mock('alias:App\Models\Fine');
        $fineMock->shouldReceive('where')->andReturn($builder);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn('user-uuid');

        $service = new UserService();

        $this->expectException(PendingFineException::class);
        $service->assertNoPendingFines($user);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_assert_no_pending_fines_passes_when_no_pending_fines(): void
    {
        $builder = Mockery::mock('stdClass');
        $builder->shouldReceive('where')->andReturnSelf();
        $builder->shouldReceive('exists')->andReturn(false);

        $fineMock = Mockery::mock('alias:App\Models\Fine');
        $fineMock->shouldReceive('where')->andReturn($builder);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn('user-uuid');

        $service = new UserService();
        $service->assertNoPendingFines($user);

        $this->assertTrue(true);
    }
}
