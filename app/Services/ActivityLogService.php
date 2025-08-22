<?php

namespace App\Services;

use App\DTOs\ActivityLogDTO;
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
     * @param ActivityLogDTO $activityLogDTO
     * @return array
     * @throws \Throwable
     */
    public function storeActivityLog(ActivityLogDTO $activityLogDTO): array
    {
        try {
            return DB::transaction(function () use ($activityLogDTO) {
                $currentProfileId = $this->currentUser->getProfileId();

                $activityLogData = $activityLogDTO->withDefaultAudit($currentProfileId)->toArray();
                $activityLog = $this->base->store(ActivityLog::class, $activityLogData);

                return $this->returnModel(201, Helper::SUCCESS, 'Activity log created successfully!', $activityLog, $activityLog->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing activity log in the database.
     * @param ActivityLogDTO $activityLogDTO
     * @param int $activityLogId
     * @return array
     * @throws \Throwable
     */
    public function updateActivityLog(ActivityLogDTO $activityLogDTO, int $activityLogId): array
    {
        try {
            return DB::transaction(function () use ($activityLogDTO, $activityLogId) {

                $activityLog = $this->fetch->showQuery(ActivityLog::class, $activityLogId)->firstOrFail();

                $currentProfileId = $this->currentUser->getProfileId();
                $activityLogData = ActivityLogDTO::fromModel($activityLog, $activityLogDTO->toArray())
                    ->touchUpdatedBy($currentProfileId)
                    ->toArray();

                $activityLog = $this->base->update($activityLog, $activityLogData);

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
