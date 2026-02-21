<?php

namespace App\Interfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\DTOs\UserGroupPermissionDTO;

interface UserGroupPermissionInterface
{
    /**
     * Store a new user group permission in the database.
     *
     * @param UserGroupPermissionDTO $userGroupPermissionDTO
     * @return ModelResponse
     */
    public function storeUserGroupPermission(UserGroupPermissionDTO $userGroupPermissionDTO): ModelResponse;

    /**
     * update an existing user group permission in the database.
     *
     * @param UserGroupPermissionDTO $userGroupPermissionDTO
     * @param integer $userGroupPermissionId
     * @return ModelResponse
     */
    public function updateUserGroupPermission(UserGroupPermissionDTO $userGroupPermissionDTO, int $userGroupPermissionId): ModelResponse;

    /**
     * delete a user group permission from the database.
     *
     * @param integer $userGroupPermissionId
     * @return ModelResponse
     */
    public function deleteUserGroupPermission(int $userGroupPermissionId): ModelResponse;

    /**
     * Store multiple user group permissions in the database.
     *
     * @param array $permissionIds
     * @param int $userGroupId
     * @return CollectionResponse
     */
    public function storeMultipleUserGroupPermission(array $permissionIds, int $userGroupId): CollectionResponse;

    /**
     * Update multiple user group permissions in the database.
     *
     * @param array $permissionIds
     * @param int $userGroupId
     * @return CollectionResponse
     */
    public function updateMultipleUserGroupPermission(array $permissionIds, int $userGroupId): CollectionResponse;
}
