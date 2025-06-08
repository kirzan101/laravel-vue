<?php

namespace App\Interfaces\FetchInterfaces;

interface PermissionFetchInterface
{
    /**
     * Fetch a list of permissions.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @return array An array of userGroups.
     */
    public function indexPermissions(array $request = [], bool $isPaginated = false): array;

    /**
     * Fetch a specific permission by their ID.
     *
     * @param integer $userGroupId
     * @return array
     */
    public function showPermission(int $userGroupId): array;
}
