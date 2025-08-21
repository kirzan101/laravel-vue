<?php

namespace App\DTOs;

class UserGroupWithPermissionDTO extends BaseDTO
{
    public function __construct(
        public readonly UserGroupDTO $userGroup,
        public readonly ?array $permissionIds = [],
    ) {}
}
