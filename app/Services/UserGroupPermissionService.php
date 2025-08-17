<?php

namespace App\Services;

use App\DTOs\UserGroupPermissionDTO;
use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\UserGroupPermissionInterface;
use App\Models\Permission;
use App\Models\UserGroupPermission;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserGroupPermissionService implements UserGroupPermissionInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch
    ) {}

    /**
     * Store a new user group permission in the database.
     *
     * @param UserGroupPermissionDTO $userGroupPermissionDTO
     * @return array
     */
    public function storeUserGroupPermission(UserGroupPermissionDTO $userGroupPermissionDTO): array
    {
        try {
            return DB::transaction(function () use ($userGroupPermissionDTO) {

                $userGroupPermissionData = $userGroupPermissionDTO->toArray();
                $userGroupPermission = $this->base->store(UserGroupPermission::class, $userGroupPermissionData);

                return $this->returnModel(201, Helper::SUCCESS, 'User group permission created successfully!', $userGroupPermission, $userGroupPermission->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * update an existing user group permission in the database.
     *
     * @param UserGroupPermissionDTO $userGroupPermissionDTO
     * @param integer $userGroupPermissionId
     * @return array
     */
    public function updateUserGroupPermission(UserGroupPermissionDTO $userGroupPermissionDTO, int $userGroupPermissionId): array
    {
        try {
            return DB::transaction(function () use ($userGroupPermissionDTO, $userGroupPermissionId) {
                $userGroupPermission = $this->fetch->showQuery(UserGroupPermission::class, $userGroupPermissionId)->firstOrFail();

                $userGroupPermissionData = $userGroupPermissionDTO->fromModel($userGroupPermission)->toArray();
                $userGroupPermission = $this->base->update($userGroupPermission, $userGroupPermissionData);

                return $this->returnModel(200, Helper::SUCCESS, 'User group permission updated successfully!', $userGroupPermission, $userGroupPermissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * delete a user group permission from the database.
     *
     * @param integer $userGroupPermissionId
     * @return array
     */
    public function deleteUserGroupPermission(int $userGroupPermissionId): array
    {
        try {
            return DB::transaction(function () use ($userGroupPermissionId) {
                $userGroupPermission = $this->fetch->showQuery(UserGroupPermission::class, $userGroupPermissionId)->firstOrFail();

                $this->base->delete($userGroupPermission);

                return $this->returnModel(204, Helper::SUCCESS, 'User group permission deleted successfully!', null, $userGroupPermissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Store multiple permissions for a user group in the database.
     *
     * This method assigns a set of default permissions to a given user group, setting the `is_active` flag 
     * for each permission based on whether it exists in the provided list of permission IDs.
     * By default, all user groups will have all permissions, but the `is_active` flag will be updated for the 
     * permissions that match the provided list of permission IDs.
     * 
     * Process Overview:
     * - Fetches all permissions from the `Permission` model.
     * - Creates entries for each permission, associating it with the specified user group.
     * - Sets the `is_active` flag for each permission based on whether the permission ID is in the provided list.
     * 
     * Notes:
     * - This operation wraps the database transactions in a `try-catch` block to ensure data consistency.
     * - If any exception is thrown, the transaction is rolled back to prevent partial data changes.
     * - The `is_active` field is dynamically set based on whether a permission ID is present in the provided list.
     * 
     * @param array $permissionIds An array of permission IDs that should be marked as `active` for the user group.
     * @param int $userGroupId The ID of the user group for which permissions are being assigned.
     * @return array Returns an array containing the result of the operation:
     *               - HTTP status code
     *               - Response message (success or error)
     *               - Additional data (if applicable)
     * 
     * @throws \Throwable If an error occurs during the transaction, an exception will be thrown and caught,
     *                    triggering a rollback and returning the error message with an appropriate HTTP status code.
     */
    public function storeMultipleUserGroupPermission(array $permissionIds, int $userGroupId): array
    {
        try {
            return DB::transaction(function () use ($permissionIds, $userGroupId) {
                // Fetch all available permission IDs from the Permission model
                $defaultPermissionIds = $this->fetch->indexQuery(Permission::class)->pluck('id')->toArray();

                $userGroupPermissions = [];
                foreach ($defaultPermissionIds as $defaultPermissionId) {
                    $userGroupPermissions[] = [
                        'user_group_id' => $userGroupId,
                        'permission_id' => $defaultPermissionId,
                        'is_active' =>  in_array($defaultPermissionId, $permissionIds) // true if ID is in the list
                    ];
                }

                if (!empty($userGroupPermissions)) {
                    $this->base->storeMultiple(UserGroupPermission::class, $userGroupPermissions);
                }

                $userGroupPermissionCollection = new Collection([
                    $userGroupPermissions
                ]);

                return $this->returnModelCollection(201, Helper::SUCCESS, 'User group permission created successfully!', $userGroupPermissionCollection);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update the `is_active` status of multiple permissions for a specific user group.
     *
     * This method updates existing `UserGroupPermission` records by enabling or disabling each
     * permission based on whether its ID exists in the provided list. It assumes all possible
     * permissions are already assigned to the user group and only toggles their activation status.
     *
     * Process:
     * - Retrieves all permission records assigned to the specified user group.
     * - Iterates through each permission and sets `is_active` to `true` if its ID exists in the
     *   `$permissionIds` array, otherwise sets it to `false`.
     * - All changes are performed within a database transaction to ensure atomicity.
     *
     * Notes:
     * - This function does not create new permission entries; it only updates existing ones.
     * - If an error occurs during the process, all changes are rolled back.
     *
     * @param array $permissionIds List of permission IDs that should be marked as active.
     * @param int $userGroupId The ID of the user group whose permissions are being updated.
     * @return array A structured response containing the HTTP status, status text, message, and updated data collection.
     *
     * @throws \Throwable If any exception is thrown during the update process, it will be caught and an error response returned.
     */
    public function updateMultipleUserGroupPermission(array $permissionIds, int $userGroupId): array
    {
        try {
            return DB::transaction(function () use ($permissionIds, $userGroupId) {
                // Fetch all user group permissions for the given user group ID
                $userGroupPermissions = $this->fetch->indexQuery(UserGroupPermission::class)
                    ->where('user_group_id', $userGroupId)
                    ->get();

                // Update each permission's is_active status based on the provided permission IDs
                foreach ($userGroupPermissions as $userGroupPermission) {
                    $isActive = in_array($userGroupPermission->permission_id, $permissionIds);
                    $this->base->update($userGroupPermission, ['is_active' => $isActive]);
                }

                return $this->returnModelCollection(200, Helper::SUCCESS, 'User group permissions updated successfully!', $userGroupPermissions);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }
}
