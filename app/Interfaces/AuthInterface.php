<?php

namespace App\Interfaces;

interface AuthInterface
{
    /**
     * Log in a user with the provided credentials.
     *
     * @param array $request
     * @return array<string, mixed>
     */
    public function login(array $request): array;

    /**
     * Log out the currently authenticated user.
     *
     * @return array<string, mixed>
     */
    public function logout(): array;

    /**
     * change user password
     *
     * @param string $current_password
     * @param string $new_password
     * @param integer $user_id
     * @return array
     */
    public function changePassword(string $current_password, string $new_password, int $user_id): array;

    /**
     * Get user by email
     *
     * @param string $email
     * @return array
     */
    public function getUserByEmail(string $email): array;

    /**
     * Reset user password
     *
     * @param integer $user_id
     * @return array
     */
    public function resetPassword(int $user_id): array;

    /**
     * Set user active status
     *
     * @param integer $user_id
     * @return array
     */
    public function setUserActiveStatus(int $user_id): array;

    /**
     * Get the API token of the currently authenticated user.
     *
     * @return string|null
     */
    public function getToken(): ?string;
}
