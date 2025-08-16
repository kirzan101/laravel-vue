<?php

namespace App\Interfaces\FetchInterfaces;

interface PermissionFetchInterface
{
    /**
     * Fetch a list of permissions.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return array An array of permissions.
     */
    public function indexPermissions(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array;

    /**
     * Fetch a specific permission by their ID.
     *
     * @param integer $userGroupId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showPermission(int $userGroupId, ?string $resourceClass = null): array;
}
