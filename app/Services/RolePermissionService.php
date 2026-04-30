<?php

namespace App\Services;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\DTOs\RolePermissionDTO;
use App\Helpers\Helper;
use App\Interfaces\RolePermissionInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\Permission;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionService implements RolePermissionInterface
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
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new role permission in the database.
     *
     * @param RolePermissionDTO $rolePermissionDTO
     * @return ModelResponse
     */
    public function storeRolePermission(RolePermissionDTO $rolePermissionDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($rolePermissionDTO) {

                $rolePermissionData = $rolePermissionDTO->toArray();
                $rolePermission = $this->base->store(RolePermission::class, $rolePermissionData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Role permission created successfully!', $rolePermission, $rolePermission->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing role permission in the database.
     *
     * @param RolePermissionDTO $rolePermissionDTO
     * @param int $rolePermissionId
     * @return ModelResponse
     */
    public function updateRolePermission(RolePermissionDTO $rolePermissionDTO, int $rolePermissionId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($rolePermissionDTO, $rolePermissionId) {
                $rolePermission = $this->fetch->showQuery(RolePermission::class, $rolePermissionId)->firstOrFail();

                $rolePermissionDTO = RolePermissionDTO::fromModel($rolePermission, $rolePermissionDTO->toArray());

                $rolePermissionData = $rolePermissionDTO->toArray();
                $rolePermission = $this->base->update($rolePermission, $rolePermissionData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Role permission updated successfully!', $rolePermission, $rolePermissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given role permission in the database.
     *
     * @param int $rolePermissionId
     * @return ModelResponse
     */
    public function deleteRolePermission(int $rolePermissionId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($rolePermissionId) {
                $rolePermission = $this->fetch->showQuery(RolePermission::class, $rolePermissionId)->firstOrFail();

                $this->base->delete($rolePermission);

                return ModelResponse::success(204, Helper::SUCCESS, 'Role permission deleted successfully!', null, $rolePermissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Store multiple role permissions in the database.
     * 
     * This method is used to store multiple permissions for a role. It takes an array of permission IDs and a role ID, and creates a new role permission for each permission ID with the given role ID. The is_active field is set to true if the permission ID is in the default permissions, otherwise it is set to false.
     *
     * Process Overview:
     * - Fetch the default permissions from the database and get their IDs.
     * - Iterate over the provided permission IDs and create an array of role permission data, setting the is_active field based on whether the permission ID is in the default permissions.
     * - Store the role permissions in the database using the base interface's storeMultiple method.
     * - Return a CollectionResponse with the created role permissions.
     * 
     * @param array $permissionIds
     * @param int $roleId
     * @return CollectionResponse
     */
    public function storeMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse
    {
        try {
            return DB::transaction(function () use ($permissionIds, $roleId) {
                $defaultPermissionIds = $this->fetch->indexQuery(Permission::class)->pluck('id')->toArray();

                $rolePermissionsData = [];
                foreach ($permissionIds as $permissionId) {
                    $rolePermissionsData[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'is_active' => in_array($permissionId, $defaultPermissionIds), // Set is_active to true if the permission is in the default permissions, otherwise false
                    ];
                }

                if (!empty($rolePermissionsData)) {
                    $this->base->storeMultiple(RolePermission::class, $rolePermissionsData);
                }

                $rolePermissionCollection = new Collection([
                    $rolePermissionsData
                ]);

                return CollectionResponse::success(201, Helper::SUCCESS, 'Role permissions created successfully!', $rolePermissionCollection);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update multiple role permissions in the database.
     * 
     * This method is used to update multiple permissions for a role. It takes an array of permission IDs and a role ID, and updates the is_active field for each role permission associated with the given role ID based on whether the permission ID is in the provided array of permission IDs.
     *
     * Process Overview:
     * - Fetch all role permissions for the given role ID from the database.
     * - Iterate over the fetched role permissions and update the is_active field based on whether the permission ID is in the provided array of permission IDs.
     * - Return a CollectionResponse with the updated role permissions.
     * 
     * @param array $permissionIds
     * @param int $roleId
     * @return CollectionResponse
     */
    public function updateMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse
    {
        try {
            return DB::transaction(function () use ($permissionIds, $roleId) {
                // Fetch all role permissions for the given role ID
                $rolePermissions = $this->fetch->indexQuery(RolePermission::class)
                    ->where('role_id', $roleId)
                    ->get();

                // Update the is_active field for each role permission based on whether its permission_id is in the provided permissionIds array
                foreach ($rolePermissions as $rolePermission) {
                    $isActive = in_array($rolePermission->permission_id, $permissionIds);
                    $this->base->update($rolePermission, ['is_active' => $isActive]);
                }

                return CollectionResponse::success(200, Helper::SUCCESS, 'Role permissions updated successfully!', $rolePermissions);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete role permissions by role ID in the database.
     *
     * @param int $roleId
     * @return ModelResponse
     */
    public function deleteRolePermissionByRoleId(int $roleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($roleId) {

                $deleteResponse = $this->fetch->indexQuery(RolePermission::class)
                    ->where('role_id', $roleId)
                    ->delete();

                if ($deleteResponse === false) {
                    throw new \Exception('Failed to delete role permissions for the role.');
                }

                return ModelResponse::success(204, Helper::SUCCESS, 'Role permissions deleted successfully!', null, $roleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
