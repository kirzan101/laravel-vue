<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\PermissionInterface;
use App\Interfaces\UserGroupInterface;
use App\Interfaces\UserGroupPermissionInterface;
use App\Models\UserGroup;
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
        EnsureSuccessTrait;

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
     * @param array $request
     * @return array
     */
    public function storeUserGroup(array $request): array
    {
        try {
            return DB::transaction(function () use ($request) {
                $profileId = $this->currentUser->getProfileId();

                $userGroup = $this->base->store(UserGroup::class, [
                    'name' => $request['name'] ?? null,
                    'code' => $request['code'] ?? null,
                    'description' => $request['description'] ?? null,
                    'created_by' => $profileId,
                    'updated_by' => $profileId,
                ]);

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
    public function updateUserGroup(array $request, int $userGroupId): array
    {
        try {
            return DB::transaction(function () use ($request, $userGroupId) {
                $userGroup = $this->fetch->showQuery(UserGroup::class, $userGroupId)->firstOrFail();

                $userGroup = $this->base->update($userGroup, [
                    'name' => $request['name'] ?? $userGroup->name,
                    'code' => $request['code'] ?? $userGroup->code,
                    'description' => $request['description'] ?? $userGroup->description,
                    'updated_by' => $this->currentUser->getProfileId(),
                ]);

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

                // record who deleted the user group
                $this->base->update($userGroup, [
                    'updated_by' => $this->currentUser->getProfileId(),
                ]);

                $this->base->delete($userGroup); // only soft delete

                return $this->returnModel(204, Helper::SUCCESS, 'User group deleted successfully!', null, $userGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Store a new user group with permissions in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeUserGroupWithPermissions(array $request): array
    {
        try {
            return DB::transaction(function () use ($request) {
                $userGroupResult = $this->storeUserGroup([
                    'name' => $request['name'] ?? null,
                    'code' => $request['code'] ?? null,
                    'description' => $request['description'] ?? null,
                ]);
                $this->ensureSuccess($userGroupResult, 'User group creation failed!');

                $userGroup = $userGroupResult['data'] ?? null;
                $userGroupId = $userGroupResult['last_id'] ?? null;

                // Store user group permissions
                $permissions = $request['permissions'] ?? [];
                $userGroupPermissionResult = $this->userGroupPermission->storeMultipleUserGroupPermission($permissions, $userGroupId);

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
     * @param array $request
     * @param integer $userGroupId
     * @return array
     */
    public function updateUserGroupWithPermissions(array $request, int $userGroupId): array
    {
        try {
            return DB::transaction(function () use ($request, $userGroupId) {
                $userGroupResult = $this->updateUserGroup($request, $userGroupId);

                $this->ensureSuccess($userGroupResult, 'User group update failed!');

                $userGroup = $userGroupResult['data'] ?? null;

                // Update user group permissions
                $permissions = $request['permissions'] ?? [];
                $userGroupPermissionResult = $this->userGroupPermission->updateMultipleUserGroupPermission($permissions, $userGroupId);

                $this->ensureSuccess($userGroupPermissionResult, 'User group permission update failed!');

                return $this->returnModel(200, Helper::SUCCESS, 'User group updated successfully!', $userGroup, $userGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
