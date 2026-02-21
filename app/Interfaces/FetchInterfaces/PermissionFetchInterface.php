<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;

interface PermissionFetchInterface
{
    /**
     * Fetch a list of permissions.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse The response containing the list of permissions, either paginated or as a collection.
     */
    public function indexPermissions(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a specific permission by their ID.
     *
     * @param integer $userGroupId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showPermission(int $userGroupId, ?string $resourceClass = null): ModelResponse;
}
