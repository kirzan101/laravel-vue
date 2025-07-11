<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'avatar',
        'first_name',
        'middle_name',
        'last_name',
        'nickname',
        'type',
        'contact_numbers',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'contact_numbers' => 'array', // Store contact numbers as an array
    ];

    /**
     * Get the user that owns the profile.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the profile that created this profile.
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

    /**
     * Get the profile that last updated this profile.
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'updated_by');
    }

    /**
     * Get the profile user group associated with the profile.
     *
     * @return HasOne
     */
    public function profileUserGroup(): HasOne
    {
        return $this->hasOne(ProfileUserGroup::class);
    }

    /**
     * Get the full name of the profile, with the middle name shortened to its initials.
     *
     * Example:
     *  - middle_name: "San Jose" → "S.J."
     *
     * @return string
     */
    public function getFullName(): string
    {
        $middleInitials = '';

        if (!empty($this->middle_name)) {
            $words = preg_split('/\s+/', trim($this->middle_name));
            $initials = array_map(fn($word) => strtoupper($word[0]), $words);
            $middleInitials = implode('.', $initials) . '.';
        }

        return trim(sprintf(
            '%s %s %s',
            $this->first_name,
            $middleInitials,
            $this->last_name
        ));
    }
}
