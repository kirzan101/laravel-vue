<?php

namespace App\Interfaces;

interface UserGroupPermissionInterface
{
    /**
     * Store a new user group permission in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeUserGroupPermission(array $request): array;

    /**
     * update an existing user group permission in the database.
     *
     * @param array $request
     * @param integer $userGroupPermissionId
     * @return array
     */
    public function updateUserGroupPermission(array $request, int $userGroupPermissionId): array;

    /**
     * delete a user group permission from the database.
     *
     * @param integer $userGroupPermissionId
     * @return array
     */
    public function deleteUserGroupPermission(int $userGroupPermissionId): array;
}
