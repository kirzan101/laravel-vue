<?php

namespace App\Interfaces;

interface UserGroupInterface
{
    /**
     * Store a new user group in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeUserGroup(array $request): array;

    /**
     * update an existing user group in the database.
     *
     * @param array $request
     * @param integer $userGroupId
     * @return array
     */
    public function updateUserGroup(array $request, int $userGroupId): array;

    /**
     * delete a user group from the database.
     *
     * @param integer $userGroupId
     * @return array
     */
    public function deleteUserGroup(int $userGroupId): array;
}
