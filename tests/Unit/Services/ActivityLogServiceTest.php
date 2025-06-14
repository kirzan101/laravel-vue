<?php

namespace Tests\Unit\Services;

use App\Interfaces\ActivityLogInterface;
use App\Interfaces\AuthInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;


class ActivityLogServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&AuthInterface */
    protected AuthInterface $auth;

    protected ActivityLogInterface $service;

    protected function setUp(): void
    {
        // Call the parent setUp method to ensure the test environment is properly initialized
        parent::setUp();

        // Mock the interfaces
        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->auth = $this->mockAuthInterface();

        // Create the service instance with the mocked dependencies
        $this->service = new ActivityLogService(
            $this->base,
            $this->fetch,
            $this->auth
        );

        // Ensure Mockery is closed after each test to prevent memory leaks
        $this->beforeApplicationDestroyed(function () {
            \Mockery::close();
        });
    }

    #[Test]
    public function it_can_store_an_activity_log(): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn($callback) => $callback());

        $data = [
            'module' => 'test_module',
            'description' => 'created something',
            'status' => 'success',
            'type' => 'create',
            'properties' => ['foo' => 'bar'],
        ];

        $fakeModel = new ActivityLog($data);
        $fakeModel->id = 1;

        $this->auth->shouldReceive('getProfileId')
            ->once()
            ->andReturn(1);

        $this->base
            ->shouldReceive('store')
            ->once()
            ->with(ActivityLog::class, \Mockery::on(
                fn($input) =>
                $input['description'] === 'created something' &&
                    $input['created_by'] === 1 &&
                    $input['updated_by'] === 1
            ))
            ->andReturn($fakeModel);

        $response = $this->service->storeActivityLog($data);

        $this->assertSame(201, $response['code']);
        $this->assertSame('success', $response['status']);
        $this->assertSame('Activity log created successfully!', $response['message']);
        $this->assertSame(1, $response['last_id']);
        $this->assertInstanceOf(ActivityLog::class, $response['data']);
    }

    #[Test]
    public function test_it_can_update_an_activity_log(): void
    {
        $id = 1;

        $existing = new ActivityLog([
            'id' => $id,
            'module' => 'old_module',
            'description' => 'old desc',
            'status' => 'pending',
            'type' => 'update',
            'properties' => [],
        ]);

        $updated = new ActivityLog([
            'id' => $id,
            'module' => 'old_module',
            'description' => 'new description',
            'status' => 'pending',
            'type' => 'update',
            'properties' => [],
        ]);

        // Mock the query builder and firstOrFail() using Mockery
        $fakeQueryBuilder = \Mockery::mock(Builder::class);
        $fakeQueryBuilder
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($existing);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(ActivityLog::class, $id)
            ->andReturn($fakeQueryBuilder);

        $this->auth->shouldReceive('getProfileId')
            ->once()
            ->andReturn(1);


        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($existing, \Mockery::on(
                fn($data) =>
                $data['description'] === 'new description' &&
                    $data['updated_by'] === 1
            ))
            ->andReturn($updated);

        $request = ['description' => 'new description'];

        $response = $this->service->updateActivityLog($request, $id);

        $this->assertSame(200, $response['code']);
        $this->assertSame('success', $response['status']);
        $this->assertSame('Activity log updated successfully!', $response['message']);
        $this->assertSame($id, $response['last_id']);
    }

    #[Test]
    public function it_can_delete_an_activity_log(): void
    {
        $id = 99;

        $log = new ActivityLog(['id' => $id]);

        $fakeQueryBuilder = \Mockery::mock(Builder::class);
        $fakeQueryBuilder
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($log);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(ActivityLog::class, $id)
            ->andReturn($fakeQueryBuilder);

        $this->base
            ->shouldReceive('delete')
            ->once()
            ->with($log);

        $response = $this->service->deleteActivityLog($id);

        $this->assertSame(204, $response['code']);
        $this->assertSame('success', $response['status']);
        $this->assertSame('Activity log deleted successfully!', $response['message']);
        $this->assertSame($id, $response['last_id']);
    }
}
