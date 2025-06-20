<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class CurrentUserService implements CurrentUserInterface
{
    use HttpErrorCodeTrait;
    use ReturnModelCollectionTrait;
    use ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
    ) {}

    /**
     * get the authenticated user's profile ID.
     *
     * @return integer
     */
    public function getProfileId(): int
    {
        $user = Auth::user();

        if (app()->environment('local')) {
            // If no user or profile in local, return fallback
            if (!$user || !$user->profile?->id) {
                // return 1;
            }
        }

        // For non-local environments, fail if missing
        if (!$user || !$user->profile?->id) {
            throw new RuntimeException('Authenticated user or profile not found.');
        }

        return $user->profile->id;
    }

    /**
     * Get the authenticated user's ID.
     *
     * @return integer
     */
    public function getUserId(): int
    {
        $user = Auth::user();

        if (app()->environment('local')) {
            // If no user or profile in local, return fallback
            if (!$user || !$user->id) {
                // return 1;
            }
        }

        // For non-local environments, fail if missing
        if (!$user || !$user->id) {
            throw new RuntimeException('Authenticated user or profile not found.');
        }

        return $user->id;
    }
}
