<?php

namespace App\Services;

use App\DTOs\PermissionDTO;
use App\Interfaces\BaseInterface;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\ModuleNameResolverInterface;
use App\Interfaces\PermissionInterface;
use App\Models\Permission;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use Illuminate\Support\Facades\DB;


class PermissionService implements PermissionInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private ModuleNameResolverInterface $moduleResolver,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new permission in the database.
     *
     * @param PermissionDTO $permissionDTO
     * @return array
     */
    public function storePermission(PermissionDTO $permissionDTO): array
    {
        try {
            return DB::transaction(function () use ($permissionDTO) {

                $permissionData = $permissionDTO->toArray();
                $permission = $this->base->store(Permission::class, $permissionData);

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
     * @param PermissionDTO $permissionDTO
     * @param int $permissionId
     * @return array
     */
    public function updatePermission(PermissionDTO $permissionDTO, int $permissionId): array
    {
        try {
            return DB::transaction(function () use ($permissionDTO, $permissionId) {
                $permission = $this->fetch->showQuery(Permission::class, $permissionId)->firstOrFail();

                $permissionData = $permissionDTO->fromModel($permission)->toArray();
                $permission = $this->base->update($permission, $permissionData);

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

                if ($this->modelUsesSoftDeletes($permission)) {
                    if ($this->modelHasColumn($permission, 'updated_by')) {
                        // record who deleted the activity log
                        $this->base->update($permission, [
                            'updated_by' => $this->currentUser->getProfileId(),
                        ]);
                    }
                }

                $this->base->delete($permission);

                return $this->returnModel(204, 'success', 'Permission deleted successfully!', null, $permissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, 'error', $th->getMessage());
        }
    }
}
