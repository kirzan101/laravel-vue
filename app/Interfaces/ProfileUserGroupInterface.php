<?php

namespace App\Interfaces;

use App\DTOs\ProfileUserGroupDTO;

interface ProfileUserGroupInterface
{
    /**
     * Store a new profile user group in the database.
     *
     * @param  ProfileUserGroupDTO  $profileUserGroupDTO
     * @return array
     */
    public function storeProfileUserGroup(ProfileUserGroupDTO $profileUserGroupDTO): array;

    /**
     * Update an existing profile user group in the database.
     *
     * @param  ProfileUserGroupDTO  $profileUserGroupDTO
     * @param  int    $profileUserGroupId
     * @return array
     */
    public function updateProfileUserGroup(ProfileUserGroupDTO $profileUserGroupDTO, int $profileUserGroupId): array;

    /**
     * Update an existing profile user group in the database using profile id.
     * 
     * @param ProfileUserGroupDTO $profileUserGroupDTO
     * @param int $profileId
     * @return array
     */
    public function updateProfileUserGroupWithProfileId(ProfileUserGroupDTO $profileUserGroupDTO, int $profileId): array;

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
