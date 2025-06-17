<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ProfileInterface;
use App\Models\Profile;
use App\Services\FetchServices\BaseFetchService;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\DB;

class ProfileService implements ProfileInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser
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
            return DB::transaction(function () use ($request) {
                $profileId = $this->currentUser->getProfileId();

                $profile = $this->base->store(Profile::class, [
                    'avatar' => $request['avatar'] ?? null,
                    'first_name' => $request['first_name'] ?? null,
                    'middle_name' => $request['middle_name'] ?? null,
                    'last_name' => $request['last_name'] ?? null,
                    'nickname' => $request['nickname'] ?? null,
                    'type' => $request['type'] ?? null,
                    'contact_numbers' => $request['contact_numbers'] ?? [],
                    'user_id' => $request['user_id'] ?? null,
                    'created_by' => $profileId,
                    'updated_by' => $profileId,
                ]);

                return $this->returnModel(201, Helper::SUCCESS, 'Profile created successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
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
            return DB::transaction(function () use ($request, $profileId) {
                $currentUserProfileId = $this->currentUser->getProfileId();

                $profile = $this->fetch->showQuery(Profile::class, $profileId)->firstOrFail();

                // Update the profile with the provided data
                $profile = $this->base->update($profile, [
                    'avatar' => $request['avatar'] ?? $profile->avatar,
                    'first_name' => $request['first_name'] ?? $profile->first_name,
                    'middle_name' => $request['middle_name'] ?? $profile->middle_name,
                    'last_name' => $request['last_name'] ?? $profile->last_name,
                    'nickname' => $request['nickname'] ?? $profile->nickname,
                    'type' => $request['type'] ?? $profile->type,
                    'contact_numbers' => $request['contact_numbers'] ?? [],
                    'user_id' => $request['user_id'] ?? null,
                    'updated_by' => $currentUserProfileId,
                ]);

                return $this->returnModel(200, Helper::SUCCESS, 'Profile updated successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
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
            return DB::transaction(function () use ($profileId) {
                $currentUserProfileId = $this->currentUser->getProfileId();

                $profile = $this->fetch->showQuery(Profile::class, $profileId)->firstOrFail();

                $this->base->update($profile, [
                    'updated_by' => $currentUserProfileId, // record who deleted the profile
                ]);

                $this->base->delete($profile);

                return $this->returnModel(204, Helper::SUCCESS, 'Profile deleted successfully!', null, $profileId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
