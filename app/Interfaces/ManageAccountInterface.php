<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\AccountDTO;
use App\DTOs\ChangePasswordDTO;
use App\DTOs\UserDTO;

interface ManageAccountInterface
{
    /**
     * Register a new user with profile.
     *
     * @param AccountDTO $accountDTO
     * @return ModelResponse
     * @throws \Throwable
     */
    public function register(AccountDTO $accountDTO): ModelResponse;

    /**
     * Update the authenticated user's profile.
     *
     * @param AccountDTO $accountDTO
     * @param int $profileId
     * @return ModelResponse
     */
    public function updateUserProfile(AccountDTO $accountDTO, int $profileId): ModelResponse;

    /**
     * Change the password for the authenticated user's profile.
     *
     * @param ChangePasswordDTO $changePasswordDTO
     * @return ModelResponse
     */
    public function changeUserProfilePassword(ChangePasswordDTO $changePasswordDTO): ModelResponse;

    /**
     * Reset user password
     *
     * @param integer $userId
     * @return ModelResponse
     */
    public function resetPassword(int $userId): ModelResponse;

    /**
     * Set user active status
     *
     * @param integer $userId
     * @return ModelResponse
     */
    public function setUserActiveStatus(int $userId): ModelResponse;
}
