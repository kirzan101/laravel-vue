<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserGroup;
use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserGroupPolicy
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
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserGroup $userGroup): bool
    {
        return in_array(Helper::ACTION_TYPE_VIEW, $this->getCan($user, $userGroup));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array(Helper::ACTION_TYPE_CREATE, $this->getCan($user, new UserGroup()));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserGroup $userGroup): bool
    {
        return in_array(Helper::ACTION_TYPE_UPDATE, $this->getCan($user, $userGroup));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserGroup $userGroup): bool
    {
        return in_array(Helper::ACTION_TYPE_DELETE, $this->getCan($user, $userGroup));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserGroup $userGroup): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserGroup $userGroup): bool
    {
        return (bool) $user->is_admin && in_array(Helper::ACTION_TYPE_DELETE, $this->getCan($user, $userGroup));
    }
}
