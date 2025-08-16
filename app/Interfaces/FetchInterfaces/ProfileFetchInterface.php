<?php

namespace App\Interfaces\FetchInterfaces;

interface ProfileFetchInterface
{
    /**
     * Fetch a list of profiles.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return array An array of profiles.
     */
    public function indexProfiles(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array;

    /**
     * Fetch a specific profile by their ID.
     *
     * @param integer $profileId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showProfile(int $profileId, ?string $resourceClass = null): array;
}
