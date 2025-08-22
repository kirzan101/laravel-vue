<?php

namespace App\Services;

use App\DTOs\AccountDTO;
use App\DTOs\ProfileDTO;
use App\DTOs\ProfileUserGroupDTO;
use App\DTOs\UserDTO;
use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
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
        private BaseInterface $base,
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
    public function register(AccountDTO $accountDTO): array
    {
        try {
            return DB::transaction(function () use ($accountDTO) {
                // set to null if this function use in registration page.
                $currentProfileId = $this->currentUser->getProfileId() ?? null;


                // Create user
                $userDto = $accountDTO->user;
                $userResult = $this->user->storeUser($userDto);

                // Ensure user creation was successful
                $this->ensureSuccess($userResult, 'User creation failed!');

                $userId = $userResult['last_id'] ?? null;

                // Create profile
                $profileDTO = $accountDTO->profile->withUser($userId);
                if ($currentProfileId) {
                    $profileDTO = $profileDTO->withDefaultAudit($currentProfileId);
                }

                $profileResult = $this->profile->storeProfile($profileDTO);

                // Ensure profile creation was successful
                $this->ensureSuccess($profileResult, 'Profile creation failed!');

                // Get the profile data
                $profile = $profileResult['data'];

                // create profile user group
                if (!empty($accountDTO->user_group_id)) {
                    $profileUserGroupDto = ProfileUserGroupDTO::fromArray([
                        'profile_id' => $profile->id,
                        'user_group_id' => $accountDTO->user_group_id
                    ]);
                    $profileUserGroupResult = $this->profileUserGroup->storeProfileUserGroup($profileUserGroupDto);

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
     * @param AccountDTO $accountDTO
     * @param int $profileId
     * @return array<string, mixed>
     */
    public function updateUserProfile(AccountDTO $accountDTO, int $profileId): array
    {
        try {
            return DB::transaction(function () use ($accountDTO, $profileId) {

                // Get the profile by profile ID
                $profile = $this->fetch->showQuery(Profile::class, $profileId)->firstOrFail();
                $currentProfileId = $this->currentUser->getProfileId();

                // Update profile
                $profileData = $accountDTO->profile;

                $profileResult = $this->profile->updateProfile($profileData, $profileId);

                // Ensure profile update was successful
                $this->ensureSuccess($profileResult, 'Profile update failed!');

                // Get user associated with the profile
                $user = $profile->user;

                if (!$user) {
                    throw new RuntimeException('User associated with the profile not found.');
                }

                // Update user
                $userData = $accountDTO->user;
                $userResult = $this->user->updateUser($userData, $user->id);

                // Ensure user update was successful
                $this->ensureSuccess($userResult, 'User update failed!');

                // Update user group if provided
                if (!empty($accountDTO->user_group_id)) {
                    $profileUserGroupDto = ProfileUserGroupDTO::fromArray([
                        'profile_id' => $profile->id,
                        'user_group_id' => $accountDTO->user_group_id
                    ]);
                    $profileUserGroupResult = $this->profileUserGroup->updateProfileUserGroupWithProfileId($profileUserGroupDto, $profileId);

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

    /**
     * Change the password for the authenticated user's profile.
     *
     * @param UserDTO $userDTO
     * @param int $profileId
     * @return array<string, mixed>
     */
    public function changeUserProfilePassword(UserDTO $userDTO, int $profileId): array
    {
        try {
            // Get the profile by profile ID
            $profile = $this->fetch->showQuery(Profile::class, $profileId)->firstOrFail();

            $user = $profile->user;

            if (!$user) {
                throw new RuntimeException('User associated with the profile not found.');
            }

            // Update user password
            $user = $this->base->update($user, [
                'is_first_login' => false,
                'password' => bcrypt($userDTO->password), // Access DTO property
            ]);

            if (!$user) {
                throw new RuntimeException('User password update failed!');
            }

            // update profile updated_at and updated_by
            $profile = $this->base->update($profile, [
                'updated_at' => now(),
                'updated_by' => $this->currentUser->getProfileId(),
            ]);

            if (!$profile) {
                throw new RuntimeException('Profile update failed!');
            }

            return $this->returnModel(
                200,
                Helper::SUCCESS,
                'Password changed successfully!',
                $profile,
                $profileId
            );
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
