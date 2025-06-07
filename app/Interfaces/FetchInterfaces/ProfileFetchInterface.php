<?php

namespace App\Interfaces\FetchInterfaces;

interface ProfileFetchInterface
{
    /**
     * Fetch a list of profiles.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @return array An array of profiles.
     */
    public function indexProfiles(array $request = [], bool $isPaginated = false): array;

    /**
     * Fetch a specific profile by their ID.
     *
     * @param integer $profileId
     * @return array
     */
    public function showProfile(int $profileId): array;
}
