<?php

namespace App\DTOs;

class RolePermissionDTO extends BaseDTO
{
    /**
     * Create a new RolePermissionDTO instance.
     */
    public function __construct(
        public int $role_id,
        public int $permission_id,
        public bool $is_active = true,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
