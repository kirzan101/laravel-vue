<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ManageAccountInterface;
use App\Interfaces\ProfileInterface;
use App\Interfaces\ProfileUserGroupInterface;
use App\Interfaces\UserInterface;
use App\Models\Profile;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ManageAccountService implements ManageAccountInterface
{
    use HttpErrorCodeTrait,
        ReturnModelTrait,
        EnsureSuccessTrait;

    public function __construct(
        private BaseFetchInterface $fetch,
        private UserInterface $user,
        private ProfileInterface $profile,
        private ProfileUserGroupInterface $profileUserGroup,
        private CurrentUserInterface $currentUser
    ) {}

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
                $profileId = $this->currentUser->getProfileId();

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
                $this->ensureSuccess($userResult, 'User creation failed!');

                $userId = $userResult['last_id'] ?? null;

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
                $this->ensureSuccess($profileResult, 'Profile creation failed!');

                $profile = $profileResult['data'];

                // create profile user group
                if (!empty($request['user_group_id'])) {
                    $profileUserGroupResult = $this->profileUserGroup->storeProfileUserGroup([
                        'profile_id' => $profile->id,
                        'user_group_id' => $request['user_group_id']
                    ]);

                    // Ensure profile user group creation was successful
                    $this->ensureSuccess($profileUserGroupResult, 'Profile user group creation failed!');
                }

                return $this->returnModel(201, Helper::SUCCESS, 'Profile registration successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing user profile.
     *
     * @param array $request
     * @param int $profileId
     * @return array<string, mixed>
     */
    public function updateUserProfile(array $request, int $profileId): array
    {
        try {
            return DB::transaction(function () use ($request, $profileId) {

                // Get the profile by profile ID
                $profile = $this->fetch->showQuery(Profile::class, $profileId)->firstOrFail();

                // Update profile
                $profileResult = $this->profile->updateProfile([
                    'avatar' => $request['avatar'] ?? $profile->avatar,
                    'first_name' => $request['first_name'] ?? $profile->first_name,
                    'middle_name' => $request['middle_name'] ?? $profile->middle_name,
                    'last_name' => $request['last_name'] ?? $profile->last_name,
                    'nickname' => $request['nickname'] ?? $profile->nickname,
                    'type' => $request['type'] ?? $profile->type,
                    'contact_numbers' => $request['contact_numbers'] ?? $profile->contact_numbers,
                    'updated_by' => $this->currentUser->getProfileId(),
                ], $profileId);

                // Ensure profile update was successful
                $this->ensureSuccess($profileResult, 'Profile update failed!');

                // Get user associated with the profile
                $user = $profile->user;

                if (!$user) {
                    throw new RuntimeException('User associated with the profile not found.');
                }

                // Update user
                $userResult = $this->user->updateUser([
                    'username' => $request['username'] ?? $user->username,
                    'email' => $request['email'] ?? $user->email,
                    'password' => !empty($request['password']) ? bcrypt($request['password']) : $user->password,
                ], $user->id);

                // Ensure user update was successful
                $this->ensureSuccess($userResult, 'User update failed!');

                // Update user group if provided
                if (!empty($request['user_group_id'])) {
                    $profileUserGroupResult = $this->profileUserGroup->updateProfileUserGroupWithProfileId([
                        'profile_id' => $profile->id,
                        'user_group_id' => $request['user_group_id']
                    ], $profileId);

                    // Ensure profile user group update was successful
                    $this->ensureSuccess($profileUserGroupResult, 'Profile user group update failed!');
                }

                return $this->returnModel(200, Helper::SUCCESS, 'Profile updated successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
