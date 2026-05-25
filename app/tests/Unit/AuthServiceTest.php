<?php

namespace Tests\Unit;

use App\DTOs\LoginDTO;
use App\DTOs\ResetPasswordDTO;
use App\Services\AuthService;
use Illuminate\Auth\Passwords\PasswordBroker;
use Mockery;

class AuthServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_auth_service_can_be_instantiated(): void
    {
        $service = new AuthService();

        $this->assertInstanceOf(AuthService::class, $service);
    }

    public function test_login_dto_holds_credentials(): void
    {
        $dto = new LoginDTO('user@example.com', 'secret');

        $this->assertSame('user@example.com', $dto->email);
        $this->assertSame('secret', $dto->password);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_reset_password_throws_runtime_exception_when_token_is_invalid(): void
    {
        $passwordFacade = Mockery::mock('alias:Illuminate\Support\Facades\Password');
        $passwordFacade->shouldReceive('reset')->andReturn(PasswordBroker::INVALID_TOKEN);

        $service = new AuthService();
        $dto     = new ResetPasswordDTO('invalid-token', 'user@example.com', 'newpassword');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid or expired password reset token.');

        $service->resetPassword($dto);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_login_throws_authentication_exception_on_invalid_credentials(): void
    {
        $authFacade = Mockery::mock('alias:Illuminate\Support\Facades\Auth');
        $authFacade->shouldReceive('attempt')->andReturn(false);

        $service = new AuthService();
        $dto     = new LoginDTO('wrong@example.com', 'wrong-password');

        $this->expectException(\Illuminate\Auth\AuthenticationException::class);

        $service->login($dto);
    }
}
