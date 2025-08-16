<?php

namespace App\Interfaces\FetchInterfaces;

interface UserGroupFetchInterface
{
    /**
     * Fetch a list of user groups.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return array An array of userGroups.
     */
    public function indexUserGroups(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array;

    /**
     * Fetch a specific user group by their ID.
     *
     * @param integer $userGroupId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showUserGroup(int $userGroupId, ?string $resourceClass = null): array;
}
