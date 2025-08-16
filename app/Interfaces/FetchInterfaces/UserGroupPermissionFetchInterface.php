<?php

namespace App\Interfaces\FetchInterfaces;

interface UserGroupPermissionFetchInterface
{
    /**
     * Fetch a list of user group permissions.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return array An array of user group permissions.
     */
    public function indexUserGroupPermissions(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array;

    /**
     * Fetch a specific user group permission by their ID.
     *
     * @param integer $userGroupPermissionId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showUserGroupPermission(int $userGroupPermissionId, ?string $resourceClass = null): array;
}
