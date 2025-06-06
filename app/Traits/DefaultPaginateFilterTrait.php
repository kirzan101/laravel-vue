<?php

namespace App\Traits;

trait DefaultPaginateFilterTrait
{
    /**
     * Set default filters for pagination.
     *
     * @param array $request
     * @return array
     */
    public function paginateFilter(array $request): array
    {
        $perPage = isset($request['per_page']) && $request['per_page'] !== null
            ? (int) $request['per_page']
            : 10;

        // Limit perPage to a maximum of 100
        $perPage = min($perPage, 100);

        $sortBy = $request['sort_by'] ?? 'id';
        $sortDirection = $request['sort_direction'] ?? 'desc';

        return [
            'per_page' => $perPage,
            'sort_by' => $sortBy,
            'sort' => $sortDirection,
        ];
    }
}
