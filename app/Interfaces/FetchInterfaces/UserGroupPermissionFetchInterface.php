<?php

namespace App\Interfaces\FetchInterfaces;

interface UserGroupPermissionFetchInterface
{
    /**
     * Fetch a list of user group permissions.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @return array An array of userGroups.
     */
    public function indexUserGroupPermissions(array $request = [], bool $isPaginated = false): array;

    /**
     * Fetch a specific user group permission by their ID.
     *
     * @param integer $userGroupPermissionId
     * @return array
     */
    public function showUserGroupPermission(int $userGroupPermissionId): array;
}
