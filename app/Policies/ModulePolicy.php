<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Module;
use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModulePolicy
{
    use ReturnModulePermissionTrait;

    /**
     * Get allowed actions for a user's profile on a given model.
     *
     * @param  User  $user
     * @param  Model $model
     * @return array<string>  List of allowed action types (e.g. ['view', 'update'])
     */
    protected function getCan(User $user, Model $model): array
    {
        $profileId = $user->profile?->id;

        if (!$profileId) {
            return [];
        }

        return $this->returnPermissions($model, $profileId);
    }

    /**
     * Get the concatenated permission string for a given action type and model.
     *
     * @param string $actionType The type of action (e.g. 'view', 'update').
     * @param Model $model The Eloquent model instance to determine the module (via table name).
     * @return string The concatenated permission string (e.g. 'view-profile').
     */
    protected function getConcatinatedPermission(string $actionType, Model $model): string
    {
        $tableName = $model->getTable(); // Get the table name of the model

        return Str::kebab("{$actionType}-{$tableName}"); // Concatenate action type and table name (e.g. 'view-profile')

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Module $module): bool
    {
        return in_array($this->getConcatinatedPermission(Helper::ACTION_TYPE_VIEW, $module), $this->getCan($user, $module));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($this->getConcatinatedPermission(Helper::ACTION_TYPE_CREATE, new UserGroup()), $this->getCan($user, new Module()));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Module $module): bool
    {
        return in_array($this->getConcatinatedPermission(Helper::ACTION_TYPE_UPDATE, $module), $this->getCan($user, $module));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Module $module): bool
    {
        return in_array($this->getConcatinatedPermission(Helper::ACTION_TYPE_DELETE, $module), $this->getCan($user, $module));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Module $module): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Module $module): bool
    {
        return (bool) $user->is_admin && in_array($this->getConcatinatedPermission(Helper::ACTION_TYPE_DELETE, $module), $this->getCan($user, $module));
    }
}
