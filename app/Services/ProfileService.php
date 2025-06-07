<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\ProfileInterface;
use App\Models\Profile;
use App\Services\FetchServices\BaseFetchService;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileService implements ProfileInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseService $service,
        private BaseFetchService $fetchService,
        private UserService $userService
    ) {}

    /**
     * Store a new profile in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeProfile(array $request): array
    {
        try {
            DB::beginTransaction();

            [
                'userId' => $userId,
            ] = $this->userService->storeUser([
                'username' => $request['username'] ?? null,
                'email' => $request['email'] ?? null,
                'password' => $request['username'], // Default password is the username
                'is_admin' => $request['is_admin'] ?? false,
                'status' => Helper::ACCOUNT_STATUS_ACTIVE,
                'is_first_login' => $request['is_first_login'] ?? true,
            ]);

            $profile = $this->service->store(Profile::class, [
                'avatar' => $request['avatar'] ?? null,
                'first_name' => $request['first_name'] ?? null,
                'middle_name' => $request['middle_name'] ?? null,
                'last_name' => $request['last_name'] ?? null,
                'nickname' => $request['nickname'] ?? null,
                'type' => $request['type'] ?? null,
                'contact_numbers' => $request['contact_numbers'] ?? [],
                'user_id' => $userId,
                'created_by' => Auth::user()->profile->id ?? 1,
                'updated_by' => Auth::user()->profile->id ?? 1,
            ]);

            DB::commit();

            return $this->returnModel(201, Helper::SUCCESS, 'Profile created successfully!', $profile, $profile->id);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * update an existing profile in the database.
     *
     * @param integer $profileId
     * @param array $request
     * @return array
     */
    public function updateProfile(array $request, int $profileId): array
    {
        try {
            DB::beginTransaction();

            $profile = $this->fetchService->showQuery(Profile::class, $profileId)->firstOrFail();

            // Update user details
            $this->userService->updateUser([
                'username' => $request['username'] ?? $profile->user->username,
                'email' => $request['email'] ?? $profile->user->email,
                'status' => $request['status'] ?? $profile->user->status,
                'is_admin' => $request['is_admin'] ?? $profile->user->is_admin,
            ], $profile->user_id);

            $profile = $this->service->update($profile, [
                'avatar' => $request['avatar'] ?? $profile->avatar,
                'first_name' => $request['first_name'] ?? $profile->first_name,
                'middle_name' => $request['middle_name'] ?? $profile->middle_name,
                'last_name' => $request['last_name'] ?? $profile->last_name,
                'nickname' => $request['nickname'] ?? $profile->nickname,
                'type' => $request['type'] ?? $profile->type,
                'contact_numbers' => $request['contact_numbers'] ?? $profile->contact_numbers,
                'updated_by' => Auth::user()->profile->id ?? 1,
            ]);

            DB::commit();

            return $this->returnModel(200, Helper::SUCCESS, 'Profile updated successfully!', $profile, $profileId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * delete a profile from the database.
     *
     * @param integer $profileId
     * @return array
     */
    public function deleteProfile(int $profileId): array
    {
        try {
            DB::beginTransaction();

            $profile = $this->fetchService->showQuery(Profile::class, $profileId)->firstOrFail();

            // total delete is not allowed, only soft delete
            // If you want to implement soft delete, you can uncomment the line below
            // $this->service->delete($profile);

            //instead of deleting, we will just update the user status to inactive
            $this->service->update($profile, [
                'updated_by' => Auth::user()->profile->id ?? 1, //added to track who updated the profile
            ]);

            $this->userService->updateUser([
                'status' => Helper::ACCOUNT_STATUS_INACTIVE, // Set user status to inactive instead of deleting
            ], $profile->user_id);

            DB::commit();

            return $this->returnModel(204, Helper::SUCCESS, 'Profile deleted successfully!', null, $profileId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
