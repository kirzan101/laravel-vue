<?php

namespace App\Services;

use App\DTOs\AccountDTO;
use App\DTOs\ChangePasswordDTO;
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
use App\Models\User;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                $profile = $this->fetch
                    ->showQuery(Profile::class, $profileId)
                    ->with('user')
                    ->firstOrFail();

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
     * @param ChangePasswordDTO $changePasswordDTO
     * @return array<string, mixed>
     */
    public function changeUserProfilePassword(ChangePasswordDTO $changePasswordDTO): array
    {
        try {
            // Get the profile by profile ID
            $profile = $this->fetch
                ->showQuery(Profile::class, $changePasswordDTO->profile_id)
                ->with('user')
                ->firstOrFail();

            $user = $profile->user;

            if (!$user) {
                throw new RuntimeException('User associated with the profile not found.');
            }

            // check if password is valid
            $checkResult = $this->checkPasswordIsCorrect($user->id, $changePasswordDTO->current_password);

            if (!$checkResult) {
                // rollback is automatic when throwing inside transaction
                throw new RuntimeException('Invalid current password!');
            }

            // Update user password
            $user = $this->base->update($user, [
                'is_first_login' => false,
                'password' => bcrypt($changePasswordDTO->new_password), // Access DTO property
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
                $profile->id
            );
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Reset user password
     *
     * @param integer $userId
     * @return array
     */
    public function resetPassword(int $userId): array
    {
        try {
            return DB::transaction(function () use ($userId) {
                $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();

                // set default password to username
                $new_password = bcrypt($user->username);

                $user->update([
                    'password' => $new_password,
                    'is_first_login' => true,
                ]);

                return $this->returnModel(200, Helper::SUCCESS, 'Successfully reset password!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Set user active status
     *
     * @param integer $userId
     * @return array
     */
    public function setUserActiveStatus(int $userId): array
    {
        try {
            return DB::transaction(function () use ($userId) {
                $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();

                $currentStatus = $user->status;

                $newStatus = match ($currentStatus) {
                    Helper::ACCOUNT_STATUS_ACTIVE   => Helper::ACCOUNT_STATUS_INACTIVE,
                    Helper::ACCOUNT_STATUS_INACTIVE => Helper::ACCOUNT_STATUS_ACTIVE,
                    default => null,
                };

                if (is_null($newStatus)) {
                    throw new RuntimeException("User status is in an unexpected state: {$currentStatus}");
                }

                $user->update(['status' => $newStatus]);

                return $this->returnModel(200, Helper::SUCCESS, "Successfully changed status to {$newStatus}!");
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }


    /**
     * check if the inputed password is correct
     *
     * @param integer $userId
     * @param string $currentPassword
     * @return boolean
     */
    private function checkPasswordIsCorrect(int $userId, string $currentPassword): bool
    {
        $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();
        $result = Hash::check($currentPassword, $user->password);

        return $result;
    }
}
