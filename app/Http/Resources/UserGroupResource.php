<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'createdBy' => $this->created_by ? $this->createdBy->getFullName() : null,
            'updatedBy' => $this->updated_by ? $this->updatedBy->getFullName() : null,
            'userGroupPermissions' => UserGroupPermissionResource::collection($this->userGroupPermissions),
        ];
    }
}
