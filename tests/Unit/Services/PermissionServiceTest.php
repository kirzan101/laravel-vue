<?php

namespace Tests\Unit\Services;

use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ModuleNameResolverInterface;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;

#[CoversClass(PermissionService::class)]
class PermissionServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&ModuleNameResolverInterface */
    protected ModuleNameResolverInterface $moduleResolver;

    protected PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->moduleResolver = Mockery::mock(ModuleNameResolverInterface::class);

        $this->service = new PermissionService(
            $this->base,
            $this->fetch,
            $this->moduleResolver
        );

        $this->beforeApplicationDestroyed(fn() => Mockery::close());
    }

    #[Test]
    public function it_can_store_a_permission()
    {
        $request = ['module' => 'equipment', 'type' => 'read', 'is_active' => true];

        $resolvedModule = 'equipment_management';

        $mockPermission = new Permission([
            'id' => 1,
            'module' => $resolvedModule,
            'type' => 'read',
            'is_active' => true,
        ]);

        $this->moduleResolver
            ->shouldReceive('resolve')
            ->once()
            ->with($request['module'])
            ->andReturn($resolvedModule);

        $this->base
            ->shouldReceive('store')
            ->once()
            ->with(Permission::class, [
                'module' => $resolvedModule,
                'type' => $request['type'],
                'is_active' => $request['is_active'],
            ])
            ->andReturn($mockPermission);

        $result = $this->service->storePermission($request);

        $this->assertEquals(201, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Permission created successfully!', $result['message']);
        $this->assertInstanceOf(Permission::class, $result['data']);
    }

    #[Test]
    public function it_can_update_a_permission()
    {
        $permissionId = 1;
        $request = ['module' => 'equipment', 'type' => 'create', 'is_active' => false];

        $original = new Permission([
            'id' => $permissionId,
            'module' => 'old_module',
            'type' => 'read',
            'is_active' => true,
        ]);

        $resolvedModule = 'equipment_module';

        $updated = new Permission([
            'id' => $permissionId,
            'module' => $resolvedModule,
            'type' => 'create',
            'is_active' => false,
        ]);

        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->andReturn($original);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Permission::class, $permissionId)
            ->andReturn($mockQuery);

        $this->moduleResolver
            ->shouldReceive('resolve')
            ->once()
            ->with($request['module'])
            ->andReturn($resolvedModule);

        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($original, [
                'module' => $resolvedModule,
                'type' => $request['type'],
                'is_active' => $request['is_active'],
            ])
            ->andReturn($updated);

        $result = $this->service->updatePermission($request, $permissionId);

        // Assert if values are as expected
        $this->assertEquals($resolvedModule, $result['data']->module);
        $this->assertEquals('create', $result['data']->type);
        $this->assertFalse($result['data']->is_active);

        // Assert standard response structure
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Permission updated successfully!', $result['message']);
        $this->assertInstanceOf(Permission::class, $result['data']);
    }

    #[Test]
    public function it_can_delete_a_permission()
    {
        $permissionId = 1;

        $permission = new Permission(['id' => $permissionId]);

        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->andReturn($permission);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Permission::class, $permissionId)
            ->andReturn($mockQuery);

        $this->base
            ->shouldReceive('delete')
            ->once()
            ->with($permission);

        $result = $this->service->deletePermission($permissionId);

        $this->assertEquals(204, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Permission deleted successfully!', $result['message']);
        $this->assertNull($result['data']);
    }
}
