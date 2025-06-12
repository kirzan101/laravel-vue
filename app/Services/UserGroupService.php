<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\UserGroupInterface;
use App\Models\UserGroup;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserGroupService implements UserGroupInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private PermissionService $permission,
        private UserGroupPermissionService $userGroupPermission,
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
            DB::beginTransaction();

            $userGroup = $this->base->store(UserGroup::class, [
                'name' => $request['name'] ?? null,
                'code' => $request['code'] ?? null,
                'description' => $request['description'] ?? null,
                'created_by' => Auth::user()->profile->id ?? 1,
                'updated_by' => Auth::user()->profile->id ?? 1,
            ]);

            // Store user group permissions
            $permissions = $request['permissions'] ?? [];
            [
                'status' => $status,
                'message' => $message,
            ] = $this->userGroupPermission->storeMultipleUserGroupPermission($permissions, $userGroup->id);

            // Throw an exception if there is an error
            if ($status === Helper::ERROR) {
                throw new \RuntimeException($message);
            }

            DB::commit();

            return $this->returnModel(201, Helper::SUCCESS, 'User group created successfully!', $userGroup, $userGroup->id);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * update an existing user group in the database.
     *
     * @param integer $userGroupId
     * @param array $request
     * @return array
     */
    public function updateUserGroup(array $request, int $userGroupId): array
    {
        try {
            DB::beginTransaction();

            $userGroup = $this->fetch->showQuery(UserGroup::class, $userGroupId)->firstOrFail();

            $userGroup = $this->base->update($userGroup, [
                'name' => $request['name'] ?? $userGroup->name,
                'code' => $request['code'] ?? $userGroup->code,
                'description' => $request['description'] ?? $userGroup->description,
                'updated_by' => Auth::user()->profile->id ?? 1,
            ]);

            // update user group permissions
            $permissions = $request['permissions'] ?? [];
            [
                'status' => $status,
                'message' => $message,
            ] = $this->userGroupPermission->updateMultipleUserGroupPermission($permissions, $userGroup->id);

            // Throw an exception if there is an error
            if ($status === Helper::ERROR) {
                throw new \RuntimeException($message);
            }

            DB::commit();

            return $this->returnModel(200, Helper::SUCCESS, 'User group updated successfully!', $userGroup, $userGroupId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * delete a user group from the database.
     *
     * @param integer $userGroupId
     * @return array
     */
    public function deleteUserGroup(int $userGroupId): array
    {
        try {
            DB::beginTransaction();

            $userGroup = $this->fetch->showQuery(UserGroup::class, $userGroupId)->firstOrFail();

            $this->base->delete($userGroup);

            DB::commit();

            return $this->returnModel(204, Helper::SUCCESS, 'User group deleted successfully!', null, $userGroupId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
