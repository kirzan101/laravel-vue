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
     * Create multiple records in the specified Eloquent model.
     *
     * This method inserts multiple records into the database in a single query.
     * It bypasses Eloquent model events (like `creating` and `created`) and 
     * doesn't handle timestamps (`created_at`, `updated_at`) by default.
     *
     * Note:
     * - This method is efficient for inserting large datasets, as it reduces 
     *   the number of database queries.
     * - Make sure to handle timestamps manually if necessary (e.g., add `created_at`
     *   and `updated_at` columns in the `$requests` array).
     * - This method does not trigger Eloquent's model events, so any logic tied
     *   to these events will not be executed.
     * 
     * @param string $modelClass The fully qualified class name of the model (e.g., `App\Models\User`).
     * @param array $requests An array of associative arrays, where each associative array 
     *                        represents a record to be inserted. The keys should match 
     *                        the column names of the model's associated database table.
     * @return int The number of rows affected by the insert operation.
     *
     * @throws \InvalidArgumentException If the provided class is not an Eloquent model.
     */
    public function storeMultiple(string $modelClass, array $requests): int
    {
        $this->validateModelClass($modelClass);

        return $modelClass::insert($requests);
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
