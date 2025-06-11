<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\AuthInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ProfileInterface;
use App\Interfaces\UserInterface;
use App\Traits\EnsureSuccessTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AuthService implements AuthInterface
{
    use HttpErrorCodeTrait,
        ReturnModelTrait,
        EnsureSuccessTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $baseFetch,
        private UserInterface $user,
        private ProfileInterface $profile,
        private AuthInterface $auth
    ) {}

    /**
     * get the authenticated user's profile ID.
     *
     * @return integer
     */
    public function getProfileId(): int
    {
        $user = Auth::user();

        if (!$user || !$user->profile?->id) {
            throw new RuntimeException('Authenticated user or profile not found.');
        }

        return $user->profile->id;
    }


    /**
     * Register a new user with profile.
     *
     * @param array $request
     * @return array<string, mixed>
     * @throws \Throwable
     */
    public function register(array $request): array
    {
        try {
            return DB::transaction(function () use ($request) {
                $profileId = $this->getProfileId();

                // Create user
                $userResult = $this->user->storeUser([
                    'username' => $request['username'] ?? null,
                    'email' => $request['email'] ?? null,
                    'password' => $request['username'], // default
                    'is_admin' => $request['is_admin'] ?? false,
                    'status' => Helper::ACCOUNT_STATUS_ACTIVE,
                    'is_first_login' => $request['is_first_login'] ?? true,
                ]);

                // Ensure user creation was successful
                $this->ensureSuccess($userResult, 'User creation failed.');

                $userId = $userResult['lastId'] ?? null;

                // Create profile
                $profileResult = $this->profile->storeProfile([
                    'avatar' => $request['avatar'] ?? null,
                    'first_name' => $request['first_name'] ?? null,
                    'middle_name' => $request['middle_name'] ?? null,
                    'last_name' => $request['last_name'] ?? null,
                    'nickname' => $request['nickname'] ?? null,
                    'type' => $request['type'] ?? null,
                    'contact_numbers' => $request['contact_numbers'] ?? [],
                    'user_id' => $userId,
                    'created_by' => $profileId,
                    'updated_by' => $profileId,
                ]);

                // Ensure profile creation was successful
                $this->ensureSuccess($profileResult, 'Profile creation failed.');

                $profile = $profileResult['data'];

                return $this->returnModel(201, Helper::SUCCESS, 'Registration successful', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
