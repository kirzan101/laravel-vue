<?php

namespace App\Traits;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;

trait ReturnModulePermissionTrait
{
    /**
     * Returns the permission types assigned to the user's profile for the given module.
     *
     * This method loads the user's profile along with its related user group and permissions.
     * It then filters the permissions based on the module name (from the model's table) and active status.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The Eloquent model instance representing the target module (used to determine the table name).
     * @param int $currentProfileId The ID of the profile for which permissions should be checked.
     *
     * @return array An array of permission types (e.g., ['view', 'update', 'delete']) for the specified module.
     */
    public function returnPermissions(Model $model, int $currentProfileId): array
    {
        // Load the profile with its associated user group and permissions
        $profile = Profile::with('profileUserGroup.userGroup.userGroupPermissions.permission')->find($currentProfileId);

        // If the profile or its related user group is missing, return an empty array
        if (!$profile?->profileUserGroup?->userGroup) {
            return [];
        }

        // Determine the module name using the model's table name
        // e.g., if the model is UserGroup, the table name is 'user_groups'
        $moduleName = $model->getTable();

        // Extract and filter permissions based on the module and active status
        $permissions = $profile->profileUserGroup->userGroup
            ->userGroupPermissions
            ->filter(fn($ugp) => $ugp->is_active)
            ->map(fn($ugp) => $ugp->permission)
            ->filter()
            ->filter(fn($perm) => $perm->module === $moduleName && $perm->is_active)
            ->pluck('type')
            ->toArray();

        return $permissions;
    }
}
