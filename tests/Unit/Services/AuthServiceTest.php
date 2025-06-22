<?php

namespace Tests\Unit\Services;

use App\Helpers\Helper;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    protected AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AuthService;

        $this->beforeApplicationDestroyed(function () {
            Mockery::close();
        });
    }

    #[Test]
    public function it_logs_in_with_username_successfully()
    {
        $user = User::factory()->make(['username' => 'testuser', 'status' => Helper::ACCOUNT_STATUS_ACTIVE]);

        Auth::shouldReceive('attempt')
            ->once()
            ->with(['username' => 'testuser', 'password' => 'secret'])
            ->andReturnTrue();

        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('getSession')->andReturnSelf();
        Auth::shouldReceive('regenerate')->once();

        $response = $this->service->login([
            'username' => 'testuser',
            'password' => 'secret',
        ]);

        $this->assertEquals(200, $response['code']);
        $this->assertEquals('Logged in successfully!', $response['message']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
    }

    #[Test]
    public function it_logs_in_with_email_successfully()
    {
        $user = User::factory()->make(['email' => 'testuser@mail.com', 'status' => Helper::ACCOUNT_STATUS_ACTIVE]);

        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'testuser@mail.com', 'password' => 'secret'])
            ->andReturnTrue();

        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('getSession')->andReturnSelf();
        Auth::shouldReceive('regenerate')->once();

        $response = $this->service->login([
            'username' => 'testuser@mail.com',
            'password' => 'secret',
        ]);

        $this->assertEquals(200, $response['code']);
        $this->assertEquals('Logged in successfully!', $response['message']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
    }

    #[Test]
    public function it_returns_error_for_inactive_account()
    {
        $user = User::factory()->make(['username' => 'testuser', 'status' => 'INACTIVE']);

        Auth::shouldReceive('attempt')->once()->andReturnTrue();
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->once();

        $response = $this->service->login([
            'username' => 'testuser',
            'password' => 'secret',
        ]);

        $this->assertEquals(403, $response['code']);
        $this->assertEquals('Account is inactive.', $response['message']);
    }

    #[Test]
    public function it_returns_error_on_login_failure()
    {
        Auth::shouldReceive('attempt')->once()->andReturnFalse();

        $response = $this->service->login([
            'username' => 'invalid',
            'password' => 'wrong',
        ]);

        $this->assertEquals(422, $response['code']);
        $this->assertEquals('The provided credentials do not match our records.', $response['message']);
    }

    #[Test]
    public function it_logs_out_successfully(): void
    {
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('logout')->once();

        $sessionMock = Session::partialMock();
        $sessionMock->shouldReceive('invalidate')->once();
        $sessionMock->shouldReceive('regenerateToken')->once();


        $response = $this->service->logout();

        $this->assertEquals(200, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Logged out successfully!', $response['message']);
    }

    #[Test]
    public function it_returns_error_when_logging_out_without_user()
    {
        Auth::shouldReceive('check')->once()->andReturnFalse();

        $response = $this->service->logout();

        $this->assertEquals(401, $response['code']);
        $this->assertEquals('No authenticated user to log out.', $response['message']);
    }
}
