<?php

namespace App\Models;

use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{
    use ReturnModulePermissionTrait;

    /**
     * Boot method to clear cache when permission is saved or deleted.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(fn($perm) => $perm->clearPermissionCache());
        static::deleted(fn($perm) => $perm->clearPermissionCache());
    }

    /**
     * Clear the permission cache for the current permission.
     *
     * This method clears the cache for all profiles that have this permission.
     */
    public function clearPermissionCache()
    {
        $module = $this->module;

        foreach ($this->userGroupPermissions()->with('userGroup.profileUserGroup.profile')->get() as $ugp) {
            foreach ($ugp->userGroup->profileUserGroup as $profileUserGroup) {
                $profile = $profileUserGroup->profile;
                if ($profile) {
                    Cache::forget($this->getPermissionCacheKey($profile->id, $module));
                }
            }
        }
    }

    protected $fillable = [
        'module',
        'type',
        'is_active',
    ];
}
