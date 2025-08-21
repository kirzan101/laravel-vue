<?php

namespace App\Interfaces;

use App\DTOs\AccountDTO;
use App\DTOs\UserDTO;

interface ManageAccountInterface
{
    /**
     * Register a new user with profile.
     *
     * @param AccountDTO $accountDTO
     * @return array<string, mixed>
     * @throws \Throwable
     */
    public function register(AccountDTO $accountDTO): array;

    /**
     * Update the authenticated user's profile.
     *
     * @param AccountDTO $accountDTO
     * @param int $profileId
     * @return array<string, mixed>
     */
    public function updateUserProfile(AccountDTO $accountDTO, int $profileId): array;

    /**
     * Change the password for the authenticated user's profile.
     *
     * @param UserDTO $userDTO
     * @param int $profileId
     * @return array<string, mixed>
     */
    public function changeUserProfilePassword(UserDTO $userDTO, int $profileId): array;
}
