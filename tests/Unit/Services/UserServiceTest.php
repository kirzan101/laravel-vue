<?php

namespace Tests\Unit\Services;

use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;

class UserServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the interfaces
        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->service = new UserService($this->base, $this->fetch);

        // Ensure Mockery is closed after each test to prevent memory leaks
        $this->beforeApplicationDestroyed(function () {
            \Mockery::close();
        });
    }

    #[Test]
    public function it_stores_a_user_successfully()
    {
        $request = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'is_admin' => true,
            'is_first_login' => true,
        ];

        $mockUser = new User([
            'id' => 1,
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        $this->base->shouldReceive('store')
            ->once()
            ->with(User::class, Mockery::on(function ($data) {
                return $data['username'] === 'testuser';
            }))
            ->andReturn($mockUser);

        $response = $this->service->storeUser($request);

        $this->assertEquals(201, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('User created successfully!', $response['message']);
        $this->assertEquals($mockUser, $response['data']);
    }

    #[Test]
    public function it_updates_a_user_successfully()
    {
        $userId = 1;

        $mockUser = new User([
            'id' => $userId,
            'username' => 'olduser',
            'email' => 'old@example.com',
            'password' => bcrypt('oldpassword'),
            'is_admin' => false,
            'status' => 'inactive',
            'is_first_login' => true,
        ]);

        $mockBuilder = Mockery::mock(Builder::class);
        $mockBuilder->shouldReceive('firstOrFail')->once()->andReturn($mockUser);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(User::class, $userId)
            ->andReturn($mockBuilder);

        $this->base->shouldReceive('update')
            ->once()
            ->with($mockUser, Mockery::type('array'))
            ->andReturn($mockUser);

        $request = [
            'username' => 'newuser',
            'email' => 'new@example.com',
            'is_admin' => true,
            'status' => 'active',
            'is_first_login' => false,
            'password' => 'newpassword'
        ];

        $response = $this->service->updateUser($request, $userId);

        $this->assertEquals(200, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('User updated successfully!', $response['message']);
        $this->assertEquals($mockUser, $response['data']);
    }

    #[Test]
    public function it_deletes_a_user_successfully()
    {
        $userId = 1;

        $mockUser = new User([
            'id' => $userId,
            'username' => 'deletethis',
            'email' => 'delete@example.com',
        ]);

        // Mock the Builder and its firstOrFail call
        $mockBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockBuilder->shouldReceive('firstOrFail')->once()->andReturn($mockUser);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(User::class, $userId)
            ->andReturn($mockBuilder);

        $this->base->shouldReceive('delete')
            ->once()
            ->with($mockUser);

        $response = $this->service->deleteUser($userId);

        $this->assertEquals(204, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('User deleted successfully!', $response['message']);
        $this->assertNull($response['data']);
    }
}
