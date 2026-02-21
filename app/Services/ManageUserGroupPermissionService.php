<?php

namespace App\Services;

use App\Data\ModelResponse;
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
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;

class ManageUserGroupPermissionService implements ManageUserGroupPermissionInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait,
        EnsureSuccessTrait,
        EnsureDataTrait;

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
     * @return ModelResponse
     */
    public function storeUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($userGroupWithPermissionDTO) {

                $userGroupDTO = $userGroupWithPermissionDTO->userGroup;
                $userGroupResult = $this->userGroup->storeUserGroup($userGroupDTO);

                $this->ensureSuccess($userGroupResult->toArray(), 'User group creation failed!');

                $userGroup = $userGroupResult->data;
                $this->ensureModel($userGroup, 'User group creation failed!');
                $userGroupId = $userGroupResult->lastId ?? null;

                // Store user group permissions
                $permissionIds = $userGroupWithPermissionDTO->permissionIds ?? [];
                $userGroupPermissionResult = $this->userGroupPermission->storeMultipleUserGroupPermission($permissionIds, $userGroupId);

                $this->ensureSuccess($userGroupPermissionResult->toArray(), 'User group permission creation failed!');

                return ModelResponse::success(201, Helper::SUCCESS, 'User group created successfully!', $userGroup, $userGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing user group with permissions in the database.
     *
     * @param UserGroupWithPermissionDTO $userGroupWithPermissionDTO
     * @param integer $userGroupId
     * @return ModelResponse
     */
    public function updateUserGroupWithPermissions(UserGroupWithPermissionDTO $userGroupWithPermissionDTO, int $userGroupId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($userGroupWithPermissionDTO, $userGroupId) {
                $currentProfileId = $this->currentUser->getProfileId();
                $userGroup = $this->fetch->showQuery(UserGroup::class, $userGroupId)->firstOrFail();

                $userGroupDTO = $userGroupWithPermissionDTO->userGroup
                    ->fromModel($userGroup)
                    ->touchUpdatedBy($currentProfileId);

                $userGroupDTO = UserGroupDTO::fromModel($userGroup, $userGroupWithPermissionDTO->userGroup->toArray())->touchUpdatedBy($currentProfileId);

                $userGroupResult = $this->userGroup->updateUserGroup($userGroupDTO, $userGroupId);

                $this->ensureSuccess($userGroupResult->toArray(), 'User group update failed!');

                $userGroup = $userGroupResult->data ?? null;
                $this->ensureModel($userGroup, 'User group update failed!');

                // Update user group permissions
                $permissionIds = $userGroupWithPermissionDTO->permissionIds ?? [];
                $userGroupPermissionResult = $this->userGroupPermission->updateMultipleUserGroupPermission($permissionIds, $userGroupId);

                $this->ensureSuccess($userGroupPermissionResult->toArray(), 'User group permission update failed!');

                return ModelResponse::success(200, Helper::SUCCESS, 'User group updated successfully!', $userGroup, $userGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
