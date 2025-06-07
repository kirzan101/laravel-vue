<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BaseService
{
    /**
     * Create a new record.
     *
     * @param string $modelClass
     * @param array $request
     * @return Model
     */
    public function store(string $modelClass, array $request): Model
    {
        $this->validateModelClass($modelClass);
        return $modelClass::create($request);
    }

    /**
     * Update a record.
     *
     * @param Model $model
     * @param array $request
     * @return Model
     */
    public function update(Model $model, array $request): Model
    {
        return tap($model)->update($request);
    }

    /**
     * Delete a record.
     *
     * @param Model $model
     * @return void
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }

    /**
     * Delete multiple records.
     *
     * @param string $modelClass
     * @param array $ids
     * @param string|null $columnName
     * @return void
     */
    public function deleteMultiple(string $modelClass, array $ids, ?string $columnName = 'id'): void
    {
        $this->validateModelClass($modelClass);

        if (empty($ids)) {
            return;
        }

        if (!Schema::hasColumn((new $modelClass)->getTable(), $columnName)) {
            throw new \InvalidArgumentException("Column '{$columnName}' does not exist on model {$modelClass}.");
        }

        $modelClass::whereIn($columnName, $ids)->delete();
    }

    /**
     * Ensure the class is a valid Eloquent model.
     *
     * @param string $modelClass
     * @return void
     */
    private function validateModelClass(string $modelClass): void
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Class must be an Eloquent model.");
        }
    }
}
