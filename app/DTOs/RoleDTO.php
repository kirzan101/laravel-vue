<?php

namespace App\DTOs;

class RoleDTO extends BaseDTO
{
    /**
     * Create a new RoleDTO instance.
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?int $user_group_id = null,
        public bool $is_active = true,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
