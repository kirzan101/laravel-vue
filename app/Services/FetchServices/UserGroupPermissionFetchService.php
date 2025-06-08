<?php

namespace App\Services\FetchServices;

use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\UserGroupPermissionFetchInterface;
use App\Models\UserGroupPermission;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;

class UserGroupPermissionFetchService extends BaseFetchService implements UserGroupPermissionFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of user group permissions with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @return array
     */
    public function indexUserGroupPermissions(array $request = [], bool $isPaginated = false): array
    {
        try {
            $query = $this->indexQuery(UserGroupPermission::class);

            if ($isPaginated) {
                $allowedFields = (new UserGroupPermission())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort
                ] = $this->paginateFilter($request, $allowedFields);

                $userGroupPermissions = $query->orderBy($sort_by, $sort)->paginate($per_page);
            } else {

                $userGroupPermissions = $query->get();
            }

            return $this->returnModelCollection(200, Helper::SUCCESS, 'Successfully fetched!', $userGroupPermissions);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single user group permission by ID.
     *
     * @param integer $id
     * @return array
     */
    public function showUserGroupPermission(int $userGroupPermissionId): array
    {
        try {
            $query = $this->showQuery(UserGroupPermission::class, $userGroupPermissionId);

            $userGroupPermission = $query->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $userGroupPermission, $userGroupPermissionId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
