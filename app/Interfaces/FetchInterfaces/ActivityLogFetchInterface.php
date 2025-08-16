<?php

namespace App\Interfaces\FetchInterfaces;

interface ActivityLogFetchInterface
{
    /**
     * Fetch a list of activity logs.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resource The resource class to transform the results.
     * @return array An array of activity logs.
     */
    public function indexActivityLogs(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array;

    /**
     * Fetch a specific activity log by their ID.
     *
     * @param integer $activityLogId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showActivityLog(int $activityLogId, ?string $resourceClass = null): array;
}
