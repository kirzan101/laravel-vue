<?php

namespace App\Interfaces;

interface CurrentUserInterface
{
    /**
     * get the authenticated user's profile ID.
     *
     * @return integer
     */
    public function getProfileId(): ?int;

    /**
     * Get the authenticated user's ID.
     *
     * @return integer
     */
    public function getUserId(): ?int;
}
