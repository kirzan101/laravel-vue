<?php

namespace App\Interfaces;

interface ProfileInterface
{
    /**
     * Store a new profile in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeProfile(array $request): array;

    /**
     * update an existing profile in the database.
     *
     * @param array $request
     * @param integer $id
     * @return array
     */
    public function updateProfile(array $request, int $id): array;

    /**
     * delete a profile from the database.
     *
     * @param integer $id
     * @return array
     */
    public function deleteProfile(int $id): array;
}
