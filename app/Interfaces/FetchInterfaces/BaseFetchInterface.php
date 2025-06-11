<?php

namespace App\Interfaces\FetchInterfaces;

interface BaseFetchInterface
{
    /**
     * Fetch a list of records from the given model.
     *
     * @param string $modelClass
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function indexQuery(string $modelClass): \Illuminate\Database\Eloquent\Builder;

    /**
     * Return a query builder filtered by a column (default: id) for further chaining.
     *
     * @param string $modelClass
     * @param int|string $id
     * @param string|null $columnName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function showQuery(string $modelClass, int|string $id, ?string $columnName = 'id'): \Illuminate\Database\Eloquent\Builder;
}
