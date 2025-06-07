<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\ActivityLogInterface;
use App\Models\ActivityLog;
use App\Services\FetchServices\BaseFetchService;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityLogService implements ActivityLogInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseService $service,
        private BaseFetchService $fetchService
    ) {}

    public function storeActivityLog(array $request): array
    {
        try {
            DB::beginTransaction();

            $activityLog = $this->service->store(ActivityLog::class, [
                'module' => $request['module'] ?? null,
                'description' => $request['description'] ?? null,
                'status' => $request['status'] ?? null,
                'type' => $request['type'] ?? null,
                'properties' => $request['properties'] ?? [],
                'created_by' => Auth::user()->profile->id,
                'updated_by' => Auth::user()->profile->id,
            ]);

            DB::commit();

            return $this->returnModel(201, Helper::SUCCESS, 'Activity log created successfully!', $activityLog, $activityLog->id);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * update an existing activity log in the database.
     *
     * @param array $request
     * @param integer $activityLogId
     * @return array
     */
    public function updateActivityLog(array $request, int $activityLogId): array
    {
        try {
            DB::beginTransaction();

            $activityLog = $this->fetchService->showQuery(ActivityLog::class, $activityLogId)->firstOrFail();

            $activityLog = $this->service->update($activityLog, [
                'module' => $request['module'] ?? $activityLog->module,
                'description' => $request['description'] ?? $activityLog->description,
                'status' => $request['status'] ?? $activityLog->status,
                'type' => $request['type'] ?? $activityLog->type,
                'properties' => $request['properties'] ?? $activityLog->properties,
                'updated_by' => Auth::user()->profile->id,
            ]);

            DB::commit();

            return $this->returnModel(200, Helper::SUCCESS, 'Activity log updated successfully!', $activityLog, $activityLogId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete an existing activity log from the database.
     *
     * @param int $activityLogId
     * @return array
     */
    public function deleteActivityLog(int $activityLogId): array
    {
        try {
            DB::beginTransaction();

            $activityLog = $this->fetchService->showQuery(ActivityLog::class, $activityLogId)->firstOrFail();

            $this->service->delete($activityLog);

            DB::commit();

            return $this->returnModel(200, Helper::SUCCESS, 'Activity log deleted successfully!', null, $activityLogId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
