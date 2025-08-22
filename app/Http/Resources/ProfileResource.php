<?php

namespace App\Http\Resources;

use App\Traits\ReturnDatetimeFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    use ReturnDatetimeFormat;

    /**
     * Define relationships this resource may need.
     */
    public static array $relations = ['createdBy', 'updatedBy', 'user', 'profileUserGroup'];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->getFullName(),
            'name' => $this->getName(1),
            'avatar' => $this->avatar,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'type' => $this->type,
            'contact_numbers' => $this->contact_numbers,
            'user_id' => $this->user_id,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'is_admin' => (bool) $this->user->is_admin,
            'is_first_login' => (bool) $this->user->is_first_login,
            'status' => $this->user->status,
            'user_group_id' => $this->profileUserGroup->user_group_id ?? null,
            'last_login_at' => $this->returnShortDateTime($this->user->last_login_at),
            'created_at' => $this->returnShortDateTime($this->created_at),
            'updated_at' => $this->returnShortDateTime($this->updated_at),
            'createdBy' => $this->created_by ? $this->createdBy->getFullName() : null,
            'updatedBy' => $this->updated_by ? $this->updatedBy->getFullName() : null,
        ];
    }
}
