<?php

namespace Tests\Unit\Services;

use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\PermissionInterface;
use App\Interfaces\UserGroupPermissionInterface;
use App\Models\UserGroup;
use App\Services\UserGroupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Helpers\TestDoubles;
use Tests\TestCase;

class UserGroupServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&CurrentUserInterface */
    protected CurrentUserInterface $currentUser;

    /** @var \Mockery\MockInterface&PermissionInterface */
    protected PermissionInterface $permission;

    /** @var \Mockery\MockInterface&UserGroupPermissionInterface */
    protected UserGroupPermissionInterface $userGroupPermission;

    /** @var \Mockery\MockInterface|UserGroupService */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->currentUser = $this->mockCurrentUserInterface();
        $this->permission = Mockery::mock(PermissionInterface::class);
        $this->userGroupPermission = Mockery::mock(UserGroupPermissionInterface::class);

        // 👇 Use partial mock to allow mocking of trait methods
        $this->service = Mockery::mock(UserGroupService::class, [
            $this->base,
            $this->fetch,
            $this->currentUser,
            $this->permission,
            $this->userGroupPermission,
        ])->makePartial();

        $this->beforeApplicationDestroyed(function () {
            Mockery::close();
        });
    }

    #[Test]
    public function it_can_store_a_user_group()
    {
        $profileId = 1;
        $request = [
            'name' => 'Admins',
            'code' => 'ADM',
            'description' => 'System administrators'
        ];

        $userGroup = new UserGroup(['id' => 123] + $request);

        $this->currentUser->shouldReceive('getProfileId')->andReturn($profileId);

        $this->base
            ->shouldReceive('store')
            ->once()
            ->with(UserGroup::class, [
                'name' => $request['name'],
                'code' => $request['code'],
                'description' => $request['description'],
                'created_by' => $profileId,
                'updated_by' => $profileId,
            ])
            ->andReturn($userGroup);

        $result = $this->service->storeUserGroup($request);

        // Assert if values are as expected
        $this->assertEquals($request['name'], $result['data']->name);
        $this->assertEquals($request['code'], $result['data']->code);
        $this->assertEquals($request['description'], $result['data']->description);
        $this->assertEquals($userGroup->id, $result['data']->id);

        // Assert standard response structure
        $this->assertEquals(201, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals($userGroup->id, $result['last_id']);
    }

    #[Test]
    public function it_can_update_a_user_group()
    {
        $profileId = 1;
        $userGroupId = 123;
        $existing = new UserGroup([
            'id' => $userGroupId,
            'name' => 'Old Name',
            'code' => 'OLD',
            'description' => 'Old description',
        ]);

        $updated = new UserGroup([
            'id' => $userGroupId,
            'name' => 'New Name',
            'code' => 'NEW',
            'description' => 'New description',
        ]);
        $updated->id = $userGroupId;

        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->andReturn($existing);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(UserGroup::class, $userGroupId)
            ->andReturn($mockQuery);

        $this->currentUser->shouldReceive('getProfileId')->once()->andReturn($profileId);

        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($existing, [
                'name' => 'New Name',
                'code' => 'NEW',
                'description' => 'New description',
                'updated_by' => $profileId,
            ])
            ->andReturn($updated);

        $result = $this->service->updateUserGroup([
            'name' => 'New Name',
            'code' => 'NEW',
            'description' => 'New description',
        ], $userGroupId);

        // Assert if values are as expected
        $this->assertEquals('New Name', $result['data']->name);
        $this->assertEquals('NEW', $result['data']->code);
        $this->assertEquals('New description', $result['data']->description);
        $this->assertEquals($userGroupId, $result['data']->id);

        // Assert standard response structure
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals($userGroupId, $result['last_id']);
    }

    #[Test]
    public function it_can_delete_a_user_group()
    {
        $userGroupId = 99;
        $profileId = 1;

        // Mock model with required behavior for soft deletes and column check
        $userGroup = Mockery::mock(UserGroup::class)->makePartial();
        $userGroup->shouldReceive('getAttribute')->with('id')->andReturn($userGroupId);

        // Simulate modelUsesSoftDeletes() by making it a SoftDeletes model
        $userGroup->shouldReceive('getMorphClass')->andReturn(UserGroup::class); // dummy for type safety

        // showQuery returns a Builder mock that returns the user group
        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->once()->andReturn($userGroup);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(UserGroup::class, $userGroupId)
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
            ->andReturn($profileId);

        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($userGroup, ['updated_by' => $profileId])
            ->andReturn($userGroup);

        $this->base
            ->shouldReceive('delete')
            ->once()
            ->with($userGroup);

        $result = $this->service->deleteUserGroup($userGroupId);

        $this->assertEquals(204, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('User group deleted successfully!', $result['message']);
        $this->assertNull($result['data']);
        $this->assertEquals($userGroupId, $result['last_id']);
    }
}
