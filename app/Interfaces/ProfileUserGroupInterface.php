<?php

namespace App\Interfaces;

interface ProfileUserGroupInterface
{
    /**
     * Store a new profile user group in the database.
     *
     * @param  array  $request
     * @return array
     */
    public function storeProfileUserGroup(array $request): array;

    /**
     * Update an existing profile user group in the database.
     *
     * @param  array  $request
     * @param  int    $profileUserGroupId
     * @return array
     */
    public function updateProfileUserGroup(array $request, int $profileUserGroupIdid): array;

    /**
     * Update an existing profile user group in the database using profile id.
     * 
     * @param array $request
     * @param int $profileId
     * @return array
     */
    public function updateProfileUserGroupWithProfileId(array $request, int $profileUserGroupId): array;

    /**
     * Delete the given profile user group in the database.
     *
     * @param  int  $profileUserGroupId
     * @return array
     */
    public function deleteProfileUserGroup(int $profileUserGroupId): array;

    /**
     * Delete the given profile user group in the database with profile id.
     * 
     * @param int $profileId
     * @return array
     */
    public function deleteProfileUserGroupWithProfileId(int $profileId): array;
}
