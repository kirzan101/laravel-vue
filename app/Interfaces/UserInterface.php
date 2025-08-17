<?php

namespace App\Interfaces;

use App\DTOs\UserDTO;

interface UserInterface
{
    /**
     * Store a new user in the database.
     *
     * @param UserDTO $userDTO
     * @return array
     */
    public function storeUser(UserDTO $userDTO): array;

    /**
     * update an existing user in the database.
     *
     * @param UserDTO $userDTO
     * @param integer $userId
     * @return array
     */
    public function updateUser(UserDTO $userDTO, int $userId): array;

    /**
     * delete a user from the database.
     *
     * @param integer $userId
     * @return array
     */
    public function deleteUser(int $userId): array;
}
