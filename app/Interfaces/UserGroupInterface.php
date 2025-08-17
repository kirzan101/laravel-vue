<?php

namespace App\Interfaces;

use App\DTOs\UserGroupDTO;

interface UserGroupInterface
{
    /**
     * Store a new user group in the database.
     *
     * @param UserGroupDTO $userGroupDTO
     * @return array
     */
    public function storeUserGroup(UserGroupDTO $userGroupDTO): array;

    /**
     * update an existing user group in the database.
     *
     * @param UserGroupDTO $userGroupDTO
     * @param integer $userGroupId
     * @return array
     */
    public function updateUserGroup(UserGroupDTO $userGroupDTO, int $userGroupId): array;

    /**
     * delete a user group from the database.
     *
     * @param integer $userGroupId
     * @return array
     */
    public function deleteUserGroup(int $userGroupId): array;
}
