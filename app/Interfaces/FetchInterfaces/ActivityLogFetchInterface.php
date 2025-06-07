<?php

namespace App\Interfaces\FetchInterfaces;

interface ActivityLogFetchInterface
{
    /**
     * Fetch a list of activity logs.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @return array An array of activity logs.
     */
    public function indexActivityLogs(array $request = [], bool $isPaginated = false): array;

    /**
     * Fetch a specific activity log by their ID.
     *
     * @param integer $activityLogId
     * @return array
     */
    public function showActivityLog(int $activityLogId): array;
}
