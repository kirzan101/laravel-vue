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
}
