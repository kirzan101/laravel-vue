<?php

namespace App\Interfaces\FetchInterfaces;

interface UserGroupFetchInterface
{
    /**
     * Fetch a list of user groups.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @return array An array of userGroups.
     */
    public function indexUserGroups(array $request = [], bool $isPaginated = false): array;

    /**
     * Fetch a specific user group by their ID.
     *
     * @param integer $userGroupId
     * @return array
     */
    public function showUserGroup(int $userGroupId): array;
}
