<?php

namespace Tests\Unit\Services;

use App\Helpers\Helper;
use App\Interfaces\AuthInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Helpers\TestDoubles;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&AuthInterface */
    protected AuthInterface $auth;

    protected ProfileService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->auth = $this->mockAuthInterface();

        $this->service = new ProfileService(
            $this->base,
            $this->fetch,
            $this->auth
        );
    }

    #[Test]
    public function it_stores_a_profile_successfully()
    {
        $profile = new Profile(['id' => 1, 'first_name' => 'John']);

        $this->auth->shouldReceive('getProfileId')->once()->andReturn(99);
        $this->base->shouldReceive('store')->once()->andReturn($profile);

        $request = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_numbers' => ['123456'],
            'user_id' => 1
        ];

        $response = $this->service->storeProfile($request);

        $this->assertSame(201, $response['code']);
        $this->assertSame(Helper::SUCCESS, $response['status']);
        $this->assertSame('Profile created successfully!', $response['message']);
        $this->assertEquals($profile, $response['data']);
    }

    #[Test]
    public function it_updates_a_profile_successfully()
    {
        $profileId = 1;
        $mockProfile = new Profile(['id' => $profileId, 'first_name' => 'Old']);

        $this->auth->shouldReceive('getProfileId')->once()->andReturn(99);

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive('firstOrFail')->once()->andReturn($mockProfile);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(Profile::class, $profileId)
            ->andReturn($builderMock);

        $this->base->shouldReceive('update')->once()->andReturn($mockProfile);

        $request = [
            'first_name' => 'New',
        ];

        $response = $this->service->updateProfile($request, $profileId);

        $this->assertSame(200, $response['code']);
        $this->assertSame(Helper::SUCCESS, $response['status']);
        $this->assertSame('Profile updated successfully!', $response['message']);
        $this->assertEquals($mockProfile, $response['data']);
    }

    #[Test]
    public function it_deletes_a_profile_successfully()
    {
        $profileId = 1;
        $mockProfile = new Profile(['id' => $profileId]);

        $this->auth->shouldReceive('getProfileId')->once()->andReturn(99);

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive('firstOrFail')->once()->andReturn($mockProfile);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(Profile::class, $profileId)
            ->andReturn($builderMock);

        $this->base->shouldReceive('update')->once()->with($mockProfile, Mockery::type('array'))->andReturn($mockProfile);
        $this->base->shouldReceive('delete')->once()->with($mockProfile);

        $response = $this->service->deleteProfile($profileId);

        $this->assertSame(204, $response['code']);
        $this->assertSame(Helper::SUCCESS, $response['status']);
        $this->assertSame('Profile deleted successfully!', $response['message']);
        $this->assertNull($response['data']);
    }
}
