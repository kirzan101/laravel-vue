<?php

namespace Tests\Unit\Services;

use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
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

    /** @var \Mockery\MockInterface&CurrentUserInterface */
    protected CurrentUserInterface $currentUser;

    /** @var \Mockery\MockInterface|ProfileService */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->currentUser = $this->mockCurrentUserInterface();

        // 👇 Use partial mock to allow mocking of trait methods
        $this->service = Mockery::mock(ProfileService::class, [
            $this->base,
            $this->fetch,
            $this->currentUser
        ])->makePartial();

        $this->beforeApplicationDestroyed(function () {
            Mockery::close();
        });
    }

    #[Test]
    public function it_stores_a_profile_successfully()
    {
        $profile = new Profile(['id' => 1, 'first_name' => 'John']);

        $this->currentUser->shouldReceive('getProfileId')->once()->andReturn(99);
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

        $this->currentUser->shouldReceive('getProfileId')->once()->andReturn(99);

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
    public function it_deletes_a_profile_successfully(): void
    {
        $profileId = 1;
        $currentUserId = 99;

        $mockProfile = Mockery::mock(Profile::class)->makePartial();
        $mockProfile->shouldReceive('getAttribute')->with('id')->andReturn($profileId);

        // Simulate modelUsesSoftDeletes() by making it a SoftDeletes model
        $mockProfile->shouldReceive('getMorphClass')->andReturn(Profile::class); // dummy for type safety

        // showQuery returns a Builder mock that returns the user group
        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->once()->andReturn($mockProfile);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Profile::class, $profileId)
            ->andReturn($mockQuery);

        // Mock modelUsesSoftDeletes() and modelHasColumn() directly on the service mock
        $this->service
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('modelUsesSoftDeletes')
            ->andReturn(true);

        $this->service
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('modelHasColumn')
            ->withAnyArgs()
            ->andReturnUsing(function ($model, $column) {
                return $column === 'updated_by';
            });

        $this->currentUser
            ->shouldReceive('getProfileId')
            ->once()
            ->andReturn($currentUserId);

        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($mockProfile, ['updated_by' => $currentUserId])
            ->andReturn($mockProfile);

        $this->base
            ->shouldReceive('delete')
            ->once()
            ->with($mockProfile);

        $response = $this->service->deleteProfile($profileId);

        $this->assertSame(204, $response['code']);
        $this->assertSame('success', $response['status']);
        $this->assertSame('Profile deleted successfully!', $response['message']);
        $this->assertNull($response['data']);
    }
}
