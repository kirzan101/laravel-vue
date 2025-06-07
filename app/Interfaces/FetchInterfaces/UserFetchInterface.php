<?php

namespace App\Interfaces\FetchInterfaces;

interface UserFetchInterface
{
    /**
     * Fetch a list of users.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @return array An array of users.
     */
    public function indexUsers(array $request = [], bool $isPaginated = false): array;

    /**
     * Fetch a specific user by their ID.
     *
     * @param integer $userId
     * @return array
     */
    public function showUser(int $userId): array;
}
