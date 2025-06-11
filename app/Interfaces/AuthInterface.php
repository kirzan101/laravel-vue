<?php

namespace App\Interfaces;

interface AuthInterface
{
    /**
     * Get the ID of the currently authenticated user profile.
     *
     * @return integer
     */
    public function getProfileId(): int;

    /**
     * Register a new user with profile.
     *
     * @param array $request
     * @return array<string, mixed>
     * @throws \Throwable
     */
    public function register(array $request): array;
}
