<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\UserGroupWithPermissionDTO;

interface ManageUserGroupPermissionInterface
{
    /**
     * Store a new user group with permissions in the database.
     *
     * @param UserGroupWithPermissionDTO $userGroupWithPermissionDTO
     * @return ModelResponse
     */
    public function storeUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO): ModelResponse;

    /**
     * Update an existing user group with permissions in the database.
     *
     * @param UserGroupWithPermissionDTO $userGroupWithPermissionDTO
     * @param integer $userGroupId
     * @return ModelResponse
     */
    public function updateUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO, int $userGroupId): ModelResponse;
}
