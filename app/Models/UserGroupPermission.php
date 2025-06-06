<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGroupPermission extends Model
{
    protected $fillable = [
        'user_group_id',
        'permission_id',
        'isActive'
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
