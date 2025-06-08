<?php

namespace App\Interfaces;

interface ActivityLogInterface
{
    /**
     * Store a new activity log in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeActivityLog(array $request): array;

    /**
     * update an existing activity log in the database.
     *
     * @param array $request
     * @param integer $activityLogId
     * @return array
     */
    public function updateActivityLog(array $request, int $activityLogId): array;

    /**
     * delete a activity log from the database.
     *
     * @param integer $activityLogId
     * @return array
     */
    public function deleteActivityLog(int $activityLogId): array;
}
