<?php

namespace App\Interfaces;

use App\DTOs\UserGroupPermissionDTO;

interface UserGroupPermissionInterface
{
    /**
     * Store a new user group permission in the database.
     *
     * @param UserGroupPermissionDTO $userGroupPermissionDTO
     * @return array
     */
    public function storeUserGroupPermission(UserGroupPermissionDTO $userGroupPermissionDTO): array;

    /**
     * update an existing user group permission in the database.
     *
     * @param UserGroupPermissionDTO $userGroupPermissionDTO
     * @param integer $userGroupPermissionId
     * @return array
     */
    public function updateUserGroupPermission(UserGroupPermissionDTO $userGroupPermissionDTO, int $userGroupPermissionId): array;

    /**
     * delete a user group permission from the database.
     *
     * @param integer $userGroupPermissionId
     * @return array
     */
    public function deleteUserGroupPermission(int $userGroupPermissionId): array;

    /**
     * Store multiple user group permissions in the database.
     *
     * @param array $permissionIds
     * @param int $userGroupId
     * @return array
     */
    public function storeMultipleUserGroupPermission(array $permissionIds, int $userGroupId): array;

    /**
     * Update multiple user group permissions in the database.
     *
     * @param array $permissionIds
     * @param int $userGroupId
     * @return array
     */
    public function updateMultipleUserGroupPermission(array $permissionIds, int $userGroupId): array;
}
