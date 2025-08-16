<?php

namespace App\Traits;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
        // Use the profile + module name as a unique cache key
        $moduleName = $model->getTable();
        $cacheKey = "permissions:profile:{$currentProfileId}:module:{$moduleName}";

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($model, $currentProfileId, $moduleName) {
            // Load the profile with its associated user group and permissions
            $profile = Profile::with('profileUserGroup.userGroup.userGroupPermissions.permission')
                ->find($currentProfileId);

            if (!$profile?->profileUserGroup?->userGroup) {
                return [];
            }

            return $profile->profileUserGroup->userGroup
                ->userGroupPermissions
                ->filter(fn($ugp) => $ugp->is_active)
                ->map(fn($ugp) => $ugp->permission)
                ->filter()
                ->filter(fn($perm) => $perm->module === $moduleName && $perm->is_active)
                ->pluck('type')
                ->toArray();
        });
    }

    /**
     * Returns the permission types assigned to the user's profile for a specific module.
     *
     * This method is similar to returnPermissions but allows specifying the module directly.
     *
     * @param string $module The name of the module for which permissions should be checked.
     * @param int $currentProfileId The ID of the profile for which permissions should be checked.
     *
     * @return array An array of permission types (e.g., ['view', 'update', 'delete']) for the specified module.
     */
    public function returnCustomPermissions(string $module, int $currentProfileId): array
    {
        $cacheKey = "permissions:profile:{$currentProfileId}:module:{$module}";

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($module, $currentProfileId) {
            // Load the profile with its associated user group and permissions
            $profile = Profile::with('profileUserGroup.userGroup.userGroupPermissions.permission')
                ->find($currentProfileId);

            // If the profile or its related user group is missing, return an empty array
            if (!$profile?->profileUserGroup?->userGroup) {
                return [];
            }

            // Extract and filter permissions based on the module and active status
            return $profile->profileUserGroup->userGroup
                ->userGroupPermissions
                ->filter(fn($ugp) => $ugp->is_active)
                ->map(fn($ugp) => $ugp->permission)
                ->filter()
                ->filter(fn($perm) => $perm->module === $module && $perm->is_active)
                ->pluck('type')
                ->toArray();
        });
    }

    /**
     * Returns the cache key for permissions based on profile ID and module name.
     *
     * This method is used to generate a unique cache key for storing and retrieving permissions.
     *
     * @param int $profileId The ID of the profile for which permissions should be checked.
     * @param string $module The name of the module for which permissions should be checked.
     *
     * @return string The cache key for the specified profile and module.
     */
    protected function getPermissionCacheKey(int $profileId, string $module): string
    {
        return "permissions:profile:{$profileId}:module:{$module}";
    }
}
