<?php

namespace App\Interfaces;

interface ManageAccountInterface
{
    /**
     * Register a new user with profile.
     *
     * @param array $request
     * @return array<string, mixed>
     * @throws \Throwable
     */
    public function register(array $request): array;

    /**
     * Update the authenticated user's profile.
     *
     * @param array $request
     * @param int $profileId
     * @return array<string, mixed>
     */
    public function updateUserProfile(array $request, int $profileId): array;

    /**
     * Change the password for the authenticated user's profile.
     *
     * @param array $request
     * @param int $profileId
     * @return array<string, mixed>
     */
    public function changeUserProfilePassword(array $request, int $profileId): array;
}
