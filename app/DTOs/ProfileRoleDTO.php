<?php

namespace App\DTOs;

class ProfileRoleDTO extends BaseDTO
{
    /**
     * Create a new ProfileRoleDTO instance.
     */
    public function __construct(
        public int $profile_id,
        public int $role_id,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
