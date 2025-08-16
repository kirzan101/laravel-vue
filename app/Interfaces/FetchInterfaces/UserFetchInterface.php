<?php

namespace App\Interfaces\FetchInterfaces;

interface UserFetchInterface
{
    /**
     * Fetch a list of users.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return array An array of users.
     */
    public function indexUsers(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array;

    /**
     * Fetch a specific user by their ID.
     *
     * @param integer $userId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showUser(int $userId, ?string $resourceClass = null): array;
}
