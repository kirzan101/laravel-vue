<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;

interface UserGroupPermissionFetchInterface
{
    /**
     * Fetch a list of user group permissions.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse The response containing the list of user group permissions, either paginated or as a collection.
     */
    public function indexUserGroupPermissions(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a specific user group permission by their ID.
     *
     * @param integer $userGroupPermissionId The ID of the user group permission to fetch.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the result.
     * @return ModelResponse The response containing the user group permission data.
     */
    public function showUserGroupPermission(int $userGroupPermissionId, ?string $resourceClass = null): ModelResponse;
}
