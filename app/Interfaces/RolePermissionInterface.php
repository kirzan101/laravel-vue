<?php

namespace App\Interfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\DTOs\RolePermissionDTO;

interface RolePermissionInterface
{
    /**
     * Store a new role permission in the database.
     *
     * @param  RolePermissionDTO $rolePermissionDTO
     * @return ModelResponse
     */
    public function storeRolePermission(RolePermissionDTO $rolePermissionDTO): ModelResponse;

    /**
     * Update an existing role permission in the database.
     *
     * @param  RolePermissionDTO $rolePermissionDTO
     * @param  int    $rolePermissionId
     * @return ModelResponse
     */
    public function updateRolePermission(RolePermissionDTO $rolePermissionDTO, int $rolePermissionId): ModelResponse;

    /**
     * Delete the given role permission in the database.
     *
     * @param  int  $rolePermissionId
     * @return ModelResponse
     */
    public function deleteRolePermission(int $rolePermissionId): ModelResponse;


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
    public function storeMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse;

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
    public function updateMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse;

    /**
     * Delete the role permissions associated with the given role ID in the database.
     *
     * @param int $roleId
     * @return ModelResponse
     */
    public function deleteRolePermissionByRoleId(int $roleId): ModelResponse;
}
