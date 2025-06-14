<?php

namespace App\Services;

use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\ModuleNameResolverInterface;
use App\Interfaces\PermissionInterface;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;


class PermissionService implements PermissionInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private ModuleNameResolverInterface $moduleResolver,
    ) {}

    /**
     * Store a new permission in the database.
     *
     * @param array $request
     * @return array
     */
    public function storePermission(array $request): array
    {
        try {
            return DB::transaction(function () use ($request) {
                $module = $this->moduleResolver->resolve($request['module'] ?? null);

                $permission = $this->base->store(Permission::class, [
                    'module' => $module,
                    'type' => $request['type'] ?? null,
                    'is_active' => $request['is_active'] ?? true,
                ]);

                return $this->returnModel(201, 'success', 'Permission created successfully!', $permission, $permission->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, 'error', $th->getMessage());
        }
    }

    /**
     * Update an existing permission in the database.
     *
     * @param array $request
     * @param int $permissionId
     * @return array
     */
    public function updatePermission(array $request, int $permissionId): array
    {
        try {
            return DB::transaction(function () use ($request, $permissionId) {
                $permission = $this->fetch->showQuery(Permission::class, $permissionId)->firstOrFail();

                $module = $permission->module;
                $newModule = $this->moduleResolver->resolve($request['module'] ?? null);

                if ($module !== $newModule) {
                    $module = $newModule;
                }

                $permission = $this->base->update($permission, [
                    'module' => $module,
                    'type' => $request['type'] ?? $permission->type,
                    'is_active' => $request['is_active'] ?? $permission->is_active,
                ]);

                return $this->returnModel(200, 'success', 'Permission updated successfully!', $permission, $permissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, 'error', $th->getMessage());
        }
    }

    /**
     * Delete a permission from the database.
     *
     * @param int $permissionId
     * @return array
     */
    public function deletePermission(int $permissionId): array
    {
        try {
            return DB::transaction(function () use ($permissionId) {
                $permission = $this->fetch->showQuery(Permission::class, $permissionId)->firstOrFail();

                $this->base->delete($permission);

                return $this->returnModel(204, 'success', 'Permission deleted successfully!', null, $permissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, 'error', $th->getMessage());
        }
    }
}
