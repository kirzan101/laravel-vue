<?php

namespace App\DTOs;

class UserGroupDTO extends AuditableDTO
{
    /*
    * Create a new UserGroupDTO instance.
    */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $code = null,
        public readonly ?string $description = null,
        public readonly ?int $id = null,
        public readonly ?int $created_by = null,
        public readonly ?int $updated_by = null,
    ) {}
}
