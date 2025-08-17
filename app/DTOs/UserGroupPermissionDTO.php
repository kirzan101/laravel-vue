<?php

namespace App\DTOs;

class UserGroupPermissionDTO extends BaseDTO
{
    public function __construct(
        public readonly ?int $user_group_id = null,
        public readonly ?int $permission_id = null,
        public readonly bool $is_active = true,
        public readonly ?int $id = null,
    ) {}
}
