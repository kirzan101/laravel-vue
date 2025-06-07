<?php

namespace App\Interfaces;

interface UserInterface
{
    /**
     * Store a new user in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeUser(array $request): array;

    /**
     * update an existing user in the database.
     *
     * @param array $request
     * @param integer $id
     * @return array
     */
    public function updateUser(array $request, int $id): array;

    /**
     * delete a user from the database.
     *
     * @param integer $id
     * @return array
     */
    public function deleteUser(int $id): array;
}
