<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\ActivityLogInterface;
use App\Models\ActivityLog;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use Illuminate\Support\Facades\DB;

class ActivityLogService implements ActivityLogInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new activity log in the database.
     * @param array $request
     * @return array
     * @throws \Throwable
     */
    public function storeActivityLog(array $request): array
    {
        try {
            return DB::transaction(function () use ($request) {
                $profileId = $this->currentUser->getProfileId();

                $activityLog = $this->base->store(ActivityLog::class, [
                    'module' => $request['module'] ?? null,
                    'description' => $request['description'] ?? null,
                    'status' => $request['status'] ?? null,
                    'type' => $request['type'] ?? null,
                    'properties' => $request['properties'] ?? [],
                    'created_by' => $profileId,
                    'updated_by' => $profileId,
                ]);

                return $this->returnModel(201, Helper::SUCCESS, 'Activity log created successfully!', $activityLog, $activityLog->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing activity log in the database.
     * @param array $request
     * @param int $activityLogId
     * @return array
     * @throws \Throwable
     */
    public function updateActivityLog(array $request, int $activityLogId): array
    {
        try {
            return DB::transaction(function () use ($request, $activityLogId) {
                $activityLog = $this->fetch->showQuery(ActivityLog::class, $activityLogId)->firstOrFail();

                $activityLog = $this->base->update($activityLog, [
                    'module' => $request['module'] ?? $activityLog->module,
                    'description' => $request['description'] ?? $activityLog->description,
                    'status' => $request['status'] ?? $activityLog->status,
                    'type' => $request['type'] ?? $activityLog->type,
                    'properties' => $request['properties'] ?? $activityLog->properties,
                    'updated_by' => $this->currentUser->getProfileId(),
                ]);

                return $this->returnModel(200, Helper::SUCCESS, 'Activity log updated successfully!', $activityLog, $activityLogId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete an existing activity log from the database.
     * @param int $activityLogId
     * @return array
     * @throws \Throwable
     */
    public function deleteActivityLog(int $activityLogId): array
    {
        try {
            return DB::transaction(function () use ($activityLogId) {
                $activityLog = $this->fetch->showQuery(ActivityLog::class, $activityLogId)->firstOrFail();

                if ($this->modelUsesSoftDeletes($activityLog)) {
                    if ($this->modelHasColumn($activityLog, 'updated_by')) {
                        // record who deleted the activity log
                        $this->base->update($activityLog, [
                            'updated_by' => $this->currentUser->getProfileId(),
                        ]);
                    }
                }

                $this->base->delete($activityLog);

                return $this->returnModel(204, Helper::SUCCESS, 'Activity log deleted successfully!', null, $activityLogId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
