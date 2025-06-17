<?php

namespace App\Interfaces;

interface AuthInterface
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
}
