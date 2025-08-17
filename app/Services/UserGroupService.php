<?php

namespace App\Services;

use App\DTOs\UserGroupDTO;
use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\PermissionInterface;
use App\Interfaces\UserGroupInterface;
use App\Interfaces\UserGroupPermissionInterface;
use App\Models\UserGroup;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureSuccessTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\DB;

class UserGroupService implements UserGroupInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        EnsureSuccessTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser,
        private PermissionInterface $permission,
        private UserGroupPermissionInterface $userGroupPermission
    ) {}

    /**
     * Store a new user group in the database.
     *
     * @param UserGroupDTO $userGroupDTO
     * @return array
     */
    public function storeUserGroup(UserGroupDTO $userGroupDTO): array
    {
        try {
            return DB::transaction(function () use ($userGroupDTO) {
                $currentProfileId = $this->currentUser->getProfileId();

                $userGroupData = $userGroupDTO->withDefaultAudit($currentProfileId)->toArray();
                $userGroup = $this->base->store(UserGroup::class, $userGroupData);

                return $this->returnModel(201, Helper::SUCCESS, 'User group created successfully!', $userGroup, $userGroup->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing user group in the database.
     *
     * @param integer $userGroupId
     * @param array $request
     * @return array
     */
    public function updateUserGroup(UserGroupDTO $userGroupDTO, int $userGroupId): array
    {
        try {
            return DB::transaction(function () use ($userGroupDTO, $userGroupId) {
                $currentProfileId = $this->currentUser->getProfileId();
                $userGroup = $this->fetch->showQuery(UserGroup::class, $userGroupId)->firstOrFail();

                $userGroupData = $userGroupDTO->fromModel($userGroup)
                    ->touchUpdatedBy($currentProfileId)
                    ->toArray();
                $userGroup = $this->base->update($userGroup, $userGroupData);

                return $this->returnModel(200, Helper::SUCCESS, 'User group updated successfully!', $userGroup, $userGroupId);
            });
        } catch (\Throwable $th) {

            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete a user group from the database.
     *
     * @param integer $userGroupId
     * @return array
     */
    public function deleteUserGroup(int $userGroupId): array
    {
        try {
            return DB::transaction(function () use ($userGroupId) {
                $userGroup = $this->fetch->showQuery(UserGroup::class, $userGroupId)->firstOrFail();

                if ($this->modelUsesSoftDeletes($userGroup)) {
                    if ($this->modelHasColumn($userGroup, 'updated_by')) {
                        // record who deleted the activity log
                        $this->base->update($userGroup, [
                            'updated_by' => $this->currentUser->getProfileId(),
                        ]);
                    }
                }

                $this->base->delete($userGroup); // only soft delete

                return $this->returnModel(204, Helper::SUCCESS, 'User group deleted successfully!', null, $userGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
