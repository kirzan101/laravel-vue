<?php

namespace App\Services\FetchServices;

use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\ActivityLogFetchInterface;
use App\Models\ActivityLog;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Pagination\Paginator;

class ActivityLogFetchService extends BaseFetchService implements ActivityLogFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of activity logs with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @return array
     */
    public function indexActivityLogs(array $request = [], bool $isPaginated = false): array
    {
        try {
            $query = $this->indexQuery(ActivityLog::class);

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

                $users = $query->orderBy($sort_by, $sort)->paginate($per_page);
            } else {

                $users = $query->get();
            }

            return $this->returnModelCollection(200, Helper::SUCCESS, 'Successfully fetched!', $users);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single activity log by ID.
     *
     * @param integer $id
     * @return array
     */
    public function showActivityLog(int $userId): array
    {
        try {
            $query = $this->showQuery(ActivityLog::class, $userId);

            $user = $query->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $user, $userId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
