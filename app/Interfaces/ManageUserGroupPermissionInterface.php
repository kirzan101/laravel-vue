<?php

namespace App\Interfaces;

use App\DTOs\UserGroupWithPermissionDTO;

interface ManageUserGroupPermissionInterface
{
    /**
     * Store a new user group with permissions in the database.
     *
     * @param UserGroupWithPermissionDTO $userGroupWithPermissionDTO
     * @return array
     */
    public function storeUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO): array;

    /**
     * update an existing user group with permissions in the database.
     *
     * @param UserGroupWithPermissionDTO $userGroupWithPermissionDTO
     * @param integer $userGroupId
     * @return array
     */
    public function updateUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO, int $userGroupId): array;
}
