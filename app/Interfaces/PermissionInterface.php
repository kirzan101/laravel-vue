<?php

namespace App\Interfaces;

use App\DTOs\PermissionDTO;

interface PermissionInterface
{
    /**
     * Store a new permission in the database.
     *
     * @param PermissionDTO $permissionDTO
     * @return array
     */
    public function storePermission(PermissionDTO $permissionDTO): array;

    /**
     * update an existing permission in the database.
     *
     * @param PermissionDTO $permissionDTO
     * @param integer $permissionId
     * @return array
     */
    public function updatePermission(PermissionDTO $permissionDTO, int $permissionId): array;

    /**
     * delete a permission from the database.
     *
     * @param integer $permissionId
     * @return array
     */
    public function deletePermission(int $permissionId): array;
}
