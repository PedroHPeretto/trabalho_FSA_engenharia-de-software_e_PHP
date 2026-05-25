<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\LogService;
use Mockery;

class LogServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_log_accepts_actor_action_and_description(): void
    {
        $logMock = Mockery::mock('alias:App\Models\Log');
        $logMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) {
                return $data['made_by'] === 'actor-uuid'
                    && $data['action'] === 'test.action'
                    && $data['description'] === 'Test description';
            }));

        $service = new LogService();

        $actor = Mockery::mock(User::class);
        $actor->shouldReceive('getAttribute')->with('id')->andReturn('actor-uuid');

        $service->log($actor, 'test.action', 'Test description');

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_log_includes_current_timestamp(): void
    {
        $logMock = Mockery::mock('alias:App\Models\Log');
        $logMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) {
                return array_key_exists('date', $data) && $data['date'] !== null;
            }));

        $service = new LogService();

        $actor = Mockery::mock(User::class);
        $actor->shouldReceive('getAttribute')->with('id')->andReturn('actor-uuid');

        $service->log($actor, 'action', 'desc');

        $this->assertTrue(true);
    }
}
