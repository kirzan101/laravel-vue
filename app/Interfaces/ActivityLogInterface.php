<?php

namespace App\Interfaces;

use App\DTOs\ActivityLogDTO;

interface ActivityLogInterface
{
    /**
     * Store a new activity log in the database.
     *
     * @param ActivityLogDTO $activityLogDTO
     * @return array
     */
    public function storeActivityLog(ActivityLogDTO $activityLogDTO): array;

    /**
     * update an existing activity log in the database.
     *
     * @param ActivityLogDTO $activityLogDTO
     * @param integer $activityLogId
     * @return array
     */
    public function updateActivityLog(ActivityLogDTO $activityLogDTO, int $activityLogId): array;

    /**
     * delete a activity log from the database.
     *
     * @param integer $activityLogId
     * @return array
     */
    public function deleteActivityLog(int $activityLogId): array;
}
