<?php

namespace App\Models;

use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class UserGroupPermission extends Model
{
    use ReturnModulePermissionTrait;

    /**
     * Boot method to clear cache when permission is saved or deleted.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(fn($ugp) => $ugp->clearPermissionCache());
        static::deleted(fn($ugp) => $ugp->clearPermissionCache());
    }

    /**
     * Clear the permission cache for the current permission.
     *
     * This method clears the cache for all profiles that have this permission.
     */
    public function clearPermissionCache()
    {
        // Ensure permission + user group are loaded
        $this->loadMissing('permission', 'userGroup.profileUserGroups.profile');

        $module = $this->permission?->module;
        if (!$module) {
            return;
        }

        foreach ($this->userGroup->profileUserGroups as $profileUserGroup) {
            $profile = $profileUserGroup->profile;
            if ($profile) {
                Cache::forget($this->getPermissionCacheKey($profile->id, $module));
            }
        }
    }

    protected $fillable = [
        'user_group_id',
        'permission_id',
        'is_active'
    ];

    /**
     * associate user group permission to user group
     *
     * @return BelongsTo
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * associate user group permission to permission
     *
     * @return BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
