<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_group_id',
        'is_active',
    ];

    /**
     * Get the user group that owns the role.
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * Get the permissions associated with the role.
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Get the profile roles associated with the role.
     */
    public function profileRoles(): HasMany
    {
        return $this->hasMany(ProfileRole::class);
    }
}
