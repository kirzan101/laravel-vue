<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\PermissionInterface;
use App\Models\Permission;
use App\Services\FetchServices\BaseFetchService;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionService implements PermissionInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseService $service,
        private BaseFetchService $fetchService
    ) {}

    /**
     * Store a permission group in the database.
     *
     * @param array $request
     * @return array
     */
    public function storePermission(array $request): array
    {
        try {
            DB::beginTransaction();

            $module = Helper::getModuleName($request['module'] ?? null);

            $permission = $this->service->store(Permission::class, [
                'module' => $module,
                'type' => $request['type'] ?? null, // value must be one of the Permission::TYPE_* constants
                'is_active' => $request['is_active'] ?? true,
            ]);

            DB::commit();

            return $this->returnModel(201, Helper::SUCCESS, 'Permission created successfully!', $permission, $permission->id);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * update an existing permission in the database.
     *
     * @param integer $permissionId
     * @param array $request
     * @return array
     */
    public function updatePermission(array $request, int $permissionId): array
    {
        try {
            DB::beginTransaction();

            $permission = $this->fetchService->showQuery(Permission::class, $permissionId)->firstOrFail();

            $module = $permission->module;

            // If the module name is changed, we need to ensure it is in snake_case and pluralized
            if ($permission->module !== Helper::getModuleName($request['module'] ?? null)) {
                $module = Helper::getModuleName($request['module'] ?? null);
            }

            $permission = $this->service->update($permission, [
                'module' => $module,
                'type' => $request['type'] ?? $permission->type, // value must be one of the Permission::TYPE_* constants
                'is_active' => $request['is_active'] ?? $permission->is_active,
            ]);

            DB::commit();

            return $this->returnModel(200, Helper::SUCCESS, 'Permission updated successfully!', $permission, $permissionId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * delete a permission from the database.
     *
     * @param integer $permissionId
     * @return array
     */
    public function deletePermission(int $permissionId): array
    {
        try {
            DB::beginTransaction();

            $permission = $this->fetchService->showQuery(Permission::class, $permissionId)->firstOrFail();

            $this->service->delete($permission);

            DB::commit();

            return $this->returnModel(204, Helper::SUCCESS, 'Permission deleted successfully!', null, $permissionId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
