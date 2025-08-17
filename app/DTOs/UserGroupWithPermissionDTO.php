<?php

namespace App\DTOs;

class UserGroupWithPermissionDTO
{
    public function __construct(
        public readonly UserGroupDTO $userGroup,
        public readonly ?array $permissions = [],
    ) {}
}
