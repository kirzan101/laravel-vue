<?php

namespace App\Services\FetchServices;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;
use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\FetchInterfaces\UserGroupPermissionFetchInterface;
use App\Models\UserGroupPermission;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Pagination\Paginator;

class UserGroupPermissionFetchService implements UserGroupPermissionFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    public function __construct(private BaseFetchInterface $fetch) {}

    /**
     * Fetch a list of user group permissions with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return PaginateResponse|CollectionResponse The response containing the list of user group permissions, either paginated or as a collection.
     */
    public function indexUserGroupPermissions(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse
    {
        try {
            $query = $this->fetch->indexQuery(UserGroupPermission::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            if ($isPaginated) {
                $allowedFields = (new UserGroupPermission())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $userGroupPermissions = $query->orderBy($sort_by, $sort)->paginate($per_page);
                return PaginateResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $userGroupPermissions);
            } else {

                $userGroupPermissions = $query->get();
                return CollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $userGroupPermissions);
            }
        } catch (\Throwable $th) {

            $code = $this->httpCode($th);
            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single user group permission by ID.
     *
     * @param integer $id
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse The response containing the user group permission data.
     */
    public function showUserGroupPermission(int $userGroupPermissionId, ?string $resourceClass = null): ModelResponse
    {
        try {
            $query = $this->fetch->showQuery(UserGroupPermission::class, $userGroupPermissionId);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $userGroupPermission = $query->firstOrFail();

            return ModelResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $userGroupPermission, $userGroupPermissionId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
