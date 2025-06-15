<?php

namespace App\Services\FetchServices;

use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Models\Permission;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Pagination\Paginator;

class PermissionFetchService extends BaseFetchService implements PermissionFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of permissions with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @return array
     */
    public function indexPermissions(array $request = [], bool $isPaginated = false): array
    {
        try {
            $query = $this->indexQuery(Permission::class);

            if (!empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('module', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new Permission())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);


                $permissions = $query->orderBy($sort_by, $sort)->paginate($per_page);
            } else {

                $permissions = $query->get();
            }

            return $this->returnModelCollection(200, Helper::SUCCESS, 'Successfully fetched!', $permissions);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single permission by ID.
     *
     * @param integer $id
     * @return array
     */
    public function showPermission(int $permissionId): array
    {
        try {
            $query = $this->showQuery(Permission::class, $permissionId);

            $permission = $query->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $permission, $permissionId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
