<?php

namespace App\Interfaces;

use App\DTOs\ProfileDTO;

interface ProfileInterface
{
    /**
     * Store a new profile in the database.
     *
     * @param ProfileDTO $profileDTO
     * @return array
     */
    public function storeProfile(ProfileDTO $profileDTO): array;

    /**
     * update an existing profile in the database.
     *
     * @param ProfileDTO $profileDTO
     * @param integer $profileId
     * @return array
     */
    public function updateProfile(ProfileDTO $profileDTO, int $profileId): array;

    /**
     * delete a profile from the database.
     *
     * @param integer $profileId
     * @return array
     */
    public function deleteProfile(int $profileId): array;
}
