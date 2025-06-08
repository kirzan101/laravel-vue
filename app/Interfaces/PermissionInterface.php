<?php

namespace App\Interfaces;

interface PermissionInterface
{
    /**
     * Store a new permission in the database.
     *
     * @param array $request
     * @return array
     */
    public function storePermission(array $request): array;

    /**
     * update an existing permission in the database.
     *
     * @param array $request
     * @param integer $permissionId
     * @return array
     */
    public function updatePermission(array $request, int $permissionId): array;

    /**
     * delete a permission from the database.
     *
     * @param integer $permissionId
     * @return array
     */
    public function deletePermission(int $permissionId): array;
}
