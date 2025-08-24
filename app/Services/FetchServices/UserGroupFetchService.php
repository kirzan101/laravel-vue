<?php

namespace App\Services\FetchServices;

use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use App\Models\UserGroup;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Pagination\Paginator;

class UserGroupFetchService implements UserGroupFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    public function __construct(private BaseFetchInterface $fetch) {}

    /**
     * Fetch a list of user groups with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function indexUserGroups(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): array
    {
        try {
            $query = $this->fetch->indexQuery(UserGroup::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            if (array_key_exists('id', $request) && !empty($request['id'])) {
                $query->where('id', $request['id']);
            }

            if (!empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new UserGroup())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $userGroups = $query->orderBy($sort_by, $sort)->paginate($per_page);
            } else {

                $userGroups = $query->get();
            }

            return $this->returnModelCollection(200, Helper::SUCCESS, 'Successfully fetched!', $userGroups);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single user group by ID.
     *
     * @param integer $id
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return array
     */
    public function showUserGroup(int $userGroupId, ?string $resourceClass = null): array
    {
        try {
            $query = $this->fetch->showQuery(UserGroup::class, $userGroupId);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $userGroup = $query->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $userGroup, $userGroupId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
