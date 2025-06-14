<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\ProfileUserGroupInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\AuthInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\ProfileUserGroup;
use Illuminate\Support\Facades\DB;

class ProfileUserGroupService implements ProfileUserGroupInterface
{
    use HttpErrorCodeTrait;
    use ReturnModelCollectionTrait;
    use ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch
    ) {}

    /**
     * Store a new profile user group in the database.
     * 
     * @param array $request
     * @return array
     */
    public function storeProfileUserGroup(array $request): array
    {
        try {
            return DB::transaction(function () use ($request) {
                $profileUserGroup = $this->base->store(ProfileUserGroup::class, [
                    'profile_id' => $request['profile_id'] ?? null,
                    'user_group_id' => $request['user_group_id'] ?? null
                ]);

                return $this->returnModel(201, Helper::SUCCESS, 'Profile user group created successfully!', $profileUserGroup, $profileUserGroup->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing profile user group in the database.
     * 
     * @param array $request
     * @param int $profileUserGroupId
     * @return array
     */
    public function updateProfileUserGroup(array $request, int $profileUserGroupId): array
    {
        try {
            return DB::transaction(function () use ($request, $profileUserGroupId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileUserGroupId)->firstOrFail();

                $profileUserGroup = $this->base->update($profileUserGroup, [
                    'profile_id' => $request['profile_id'] ?? $profileUserGroup->profile_id,
                    'user_group_id' => $request['user_group_id'] ?? $profileUserGroup->user_group_id,
                ]);

                // $this->returnModel(code, status, message, model, last_id);
                return $this->returnModel(200, Helper::SUCCESS, 'Profile user group updated successfully!', $profileUserGroup, $profileUserGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing profile user group in the database using profile id.
     * 
     * @param array $request
     * @param int $profileId
     * @return array
     */
    public function updateProfileUserGroupWithProfileId(array $request, int $profileId): array
    {
        try {
            return DB::transaction(function () use ($request, $profileId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileId, 'profile_id')->firstOrFail();

                $profileUserGroup = $this->base->update($profileUserGroup, [
                    'user_group_id' => $request['user_group_id'] ?? $profileUserGroup->user_group_id,
                ]);

                // $this->returnModel(code, status, message, model, last_id);
                return $this->returnModel(200, Helper::SUCCESS, 'Profile user group updated successfully!', $profileUserGroup, $profileUserGroup->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given profile user group in the database.
     * 
     * @param int $profileUserGroupId
     * @return array
     */
    public function deleteProfileUserGroup(int $profileUserGroupId): array
    {
        try {
            return DB::transaction(function () use ($profileUserGroupId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileUserGroupId)->firstOrFail();

                $this->base->delete($profileUserGroup);

                return $this->returnModel(204, Helper::SUCCESS, 'Profile user group deleted successfully!', null, $profileUserGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given profile user group in the database with profile id.
     * 
     * @param int $profileId
     * @return array
     */
    public function deleteProfileUserGroupWithProfileId(int $profileId): array
    {
        try {
            return DB::transaction(function () use ($profileId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileId, 'profile_id')->firstOrFail();

                $this->base->delete($profileUserGroup);

                return $this->returnModel(204, Helper::SUCCESS, 'Profile user group deleted successfully!', null, $profileUserGroup->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
