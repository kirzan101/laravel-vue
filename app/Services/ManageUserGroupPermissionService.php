<?php

namespace App\Services;

use App\DTOs\UserGroupDTO;
use App\DTOs\UserGroupWithPermissionDTO;
use App\Helpers\Helper;
use App\Interfaces\ManageUserGroupPermissionInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\UserGroupInterface;
use App\Interfaces\UserGroupPermissionInterface;
use App\Models\UserGroup;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;

class ManageUserGroupPermissionService implements ManageUserGroupPermissionInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait,
        EnsureSuccessTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser,
        private UserGroupInterface $userGroup,
        private UserGroupPermissionInterface $userGroupPermission
    ) {}

    /**
     * Store a new user group with permissions in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO): array
    {
        try {
            return DB::transaction(function () use ($userGroupWithPermissionDTO) {

                $userGroupDTO = $userGroupWithPermissionDTO->userGroup;
                $userGroupResult = $this->userGroup->storeUserGroup($userGroupDTO);

                $this->ensureSuccess($userGroupResult, 'User group creation failed!');

                $userGroup = $userGroupResult['data'] ?? null;
                $userGroupId = $userGroupResult['last_id'] ?? null;

                // Store user group permissions
                $permissionIds = $userGroupWithPermissionDTO->permissionIds ?? [];
                $userGroupPermissionResult = $this->userGroupPermission->storeMultipleUserGroupPermission($permissionIds, $userGroupId);

                $this->ensureSuccess($userGroupPermissionResult, 'User group permission creation failed!');

                return $this->returnModel(201, Helper::SUCCESS, 'User group created successfully!', $userGroup, $userGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing user group with permissions in the database.
     *
     * @param UserGroupWithPermissionDTO $userGroupWithPermissionDTO
     * @param integer $userGroupId
     * @return array
     */
    public function updateUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO, int $userGroupId): array
    {
        try {
            return DB::transaction(function () use ($userGroupWithPermissionDTO, $userGroupId) {
                $currentProfileId = $this->currentUser->getProfileId();
                $userGroup = $this->fetch->showQuery(UserGroup::class, $userGroupId)->firstOrFail();

                $userGroupDTO = $userGroupWithPermissionDTO->userGroup
                    ->fromModel($userGroup)
                    ->touchUpdatedBy($currentProfileId);
                $userGroupResult = $this->userGroup->updateUserGroup($userGroupDTO, $userGroupId);

                $this->ensureSuccess($userGroupResult, 'User group update failed!');

                $userGroup = $userGroupResult['data'] ?? null;

                // Update user group permissions
                $permissionIds = $userGroupWithPermissionDTO->permissionIds ?? [];
                $userGroupPermissionResult = $this->userGroupPermission->updateMultipleUserGroupPermission($permissionIds, $userGroupId);

                $this->ensureSuccess($userGroupPermissionResult, 'User group permission update failed!');

                return $this->returnModel(200, Helper::SUCCESS, 'User group updated successfully!', $userGroup, $userGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
