<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

/**
 * Trait CheckIfColumnExistsTrait
 *
 * Provides a method to check if a specific column exists in a model's underlying database table.
 */
trait CheckIfColumnExistsTrait
{
    /**
     * Check if the given column exists on the model's underlying database table.
     *
     * @param string $modelClass Fully qualified model class name
     * @param string $column     Column name to check
     * @return bool
     */
    public function modelHasColumn(string $modelClass, string $column): bool
    {
        if (!class_exists($modelClass)) {
            return false;
        }

        $model = new $modelClass;

        if (!method_exists($model, 'getTable')) {
            return false;
        }

        return Schema::hasColumn($model->getTable(), $column);
    }
}
