<?php

namespace App\Services\FetchServices;

use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\ActivityLogFetchInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\ActivityLog;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Pagination\Paginator;

class ActivityLogFetchService implements ActivityLogFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    public function __construct(private BaseFetchInterface $fetch) {}

    /**
     * Fetch a list of activity logs with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function indexActivityLogs(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array
    {
        try {
            $query = $this->fetch->indexQuery(ActivityLog::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            if (isset($request['search']) && !empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('module', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if (!empty($request['status'])) {
                $status = $request['status'];
                $query->where('status', $status);
            }

            if (!empty($request['type'])) {
                $type = $request['type'];
                $query->where('type', $type);
            }

            if ($isPaginated) {
                $allowedFields = (new ActivityLog())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $activityLogs = $query->orderBy($sort_by, $sort)->paginate($per_page);
            } else {

                $activityLogs = $query->get();
            }

            return $this->returnModelCollection(200, Helper::SUCCESS, 'Successfully fetched!', $activityLogs);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single activity log by ID.
     *
     * @param integer $id
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showActivityLog(int $id, ?string $resourceClass = null): array
    {
        try {
            $query = $this->fetch->showQuery(ActivityLog::class, $id);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $activityLog = $query->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $activityLog, $id);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
