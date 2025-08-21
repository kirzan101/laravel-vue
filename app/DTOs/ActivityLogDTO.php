<?php

namespace App\DTOs;

class ActivityLogDTO extends AuditableDTO
{
    /**
     * Create a new ActivityLogDTO instance.
     */
    public function __construct(
        public readonly ?string $module = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
        public readonly ?string $type = null,
        public readonly ?array $properties = [],
        ?int $id = null,
        ?int $created_by = null,
        ?int $updated_by = null,
    ) {}
}
