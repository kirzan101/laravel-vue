<?php

namespace Tests\Unit\Services;

use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\Permission;
use App\Models\UserGroupPermission;
use App\Services\UserGroupPermissionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Helpers\TestDoubles;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;


class UserGroupPermissionServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    protected UserGroupPermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();

        // Initialize the service with mocked dependencies
        $this->service = new UserGroupPermissionService(
            $this->base,
            $this->fetch,
        );

        $this->beforeApplicationDestroyed(function () {
            Mockery::close();
        });
    }

    #[Test]
    public function it_can_store_user_group_permission()
    {
        $request = [
            'user_group_id' => 1,
            'permission_id' => 2,
            'is_active' => true,
        ];

        $model = new UserGroupPermission($request);
        $model->id = 100;

        $this->base
            ->shouldReceive('store')
            ->once()
            ->with(UserGroupPermission::class, $request)
            ->andReturn($model);

        $result = $this->service->storeUserGroupPermission($request);

        $this->assertEquals(201, $result['code']);
        $this->assertEquals(Helper::SUCCESS, $result['status']);
        $this->assertEquals($model->id, $result['last_id']);
    }

    #[Test]
    public function it_can_update_user_group_permission()
    {
        $id = 1;
        $request = [
            'user_group_id' => 1,
            'permission_id' => 3,
            'is_active' => false,
        ];

        $existing = new UserGroupPermission([
            'user_group_id' => 1,
            'permission_id' => 2,
            'is_active' => true,
        ]);
        $existing->id = $id;

        $updated = clone $existing;
        $updated->permission_id = 3;
        $updated->is_active = false;

        // Mock the query builder and firstOrFail() using Mockery
        $fakeQueryBuilder = \Mockery::mock(Builder::class);
        $fakeQueryBuilder
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($existing);

        $this->fetch
            ->shouldReceive('showQuery')
            ->with(UserGroupPermission::class, $id)
            ->andReturn($fakeQueryBuilder);

        $this->base
            ->shouldReceive('update')
            ->with($existing, Mockery::subset($request))
            ->andReturn($updated);

        $result = $this->service->updateUserGroupPermission($request, $id);
        // dd($result);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals(Helper::SUCCESS, $result['status']);
        $this->assertEquals($id, $result['last_id']);
    }

    #[Test]
    public function it_can_delete_user_group_permission()
    {
        $id = 1;
        $userGroupPermission = new UserGroupPermission([
            'id' => $id,
            'user_group_id' => 1,
            'permission_id' => 2,
        ]);
        $userGroupPermission->id = $id;

        $fakeQueryBuilder = \Mockery::mock(Builder::class);
        $fakeQueryBuilder
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($userGroupPermission);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(UserGroupPermission::class, $id)
            ->andReturn($fakeQueryBuilder);

        $this->base
            ->shouldReceive('delete')
            ->once()
            ->with($userGroupPermission);

        $result = $this->service->deleteUserGroupPermission($id);

        $this->assertEquals(204, $result['code']);
        $this->assertEquals(Helper::SUCCESS, $result['status']);
        $this->assertEquals($id, $result['last_id']);
    }

    #[Test]
    public function it_can_store_multiple_user_group_permissions()
    {
        $userGroupId = 1;
        $activePermissionIds = [1, 3];
        $defaultPermissions = new Collection([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        $permissionQueryBuilder = \Mockery::mock(Builder::class);
        $permissionQueryBuilder
            ->shouldReceive('pluck')
            ->once()
            ->with('id')
            ->andReturn($defaultPermissions->pluck('id'));

        $this->fetch
            ->shouldReceive('indexQuery')
            ->once()
            ->with(Permission::class)
            ->andReturn($permissionQueryBuilder);

        $expectedStoredData = [
            ['user_group_id' => 1, 'permission_id' => 1, 'is_active' => true],
            ['user_group_id' => 1, 'permission_id' => 2, 'is_active' => false],
            ['user_group_id' => 1, 'permission_id' => 3, 'is_active' => true],
        ];

        $this->base
            ->shouldReceive('storeMultiple')
            ->once()
            ->with(UserGroupPermission::class, $expectedStoredData)
            ->andReturnTrue();

        $result = $this->service->storeMultipleUserGroupPermission($activePermissionIds, $userGroupId);

        $this->assertEquals(201, $result['code']);
        $this->assertEquals(Helper::SUCCESS, $result['status']);
        $this->assertEquals('User group permission created successfully!', $result['message']);
        $this->assertInstanceOf(Collection::class, $result['data']);
    }

    #[Test]
    public function it_can_update_multiple_user_group_permissions()
    {
        $userGroupId = 1;
        $activePermissionIds = [1];

        $existingPermissions = new Collection([
            new UserGroupPermission(['id' => 10, 'permission_id' => 1, 'user_group_id' => $userGroupId]),
            new UserGroupPermission(['id' => 11, 'permission_id' => 2, 'user_group_id' => $userGroupId]),
        ]);

        $fakeQueryBuilder = \Mockery::mock(Builder::class);
        $fakeQueryBuilder
            ->shouldReceive('where')
            ->once()
            ->with('user_group_id', $userGroupId)
            ->andReturnSelf();

        $fakeQueryBuilder
            ->shouldReceive('get')
            ->once()
            ->andReturn($existingPermissions);

        $this->fetch
            ->shouldReceive('indexQuery')
            ->once()
            ->with(UserGroupPermission::class)
            ->andReturn($fakeQueryBuilder);

        foreach ($existingPermissions as $perm) {
            $expectedActive = in_array($perm->permission_id, $activePermissionIds);

            $this->base
                ->shouldReceive('update')
                ->once()
                ->with($perm, ['is_active' => $expectedActive])
                ->andReturnTrue(); // Return bool to match real implementation
        }

        $result = $this->service->updateMultipleUserGroupPermission($activePermissionIds, $userGroupId);

        $this->assertEquals(200, $result['code']);
        $this->assertEquals(Helper::SUCCESS, $result['status']);
        $this->assertInstanceOf(Collection::class, $result['data']);
    }
}
