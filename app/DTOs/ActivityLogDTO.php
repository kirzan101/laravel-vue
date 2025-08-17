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
        public readonly ?int $id = null,
        public readonly ?int $created_by = null,
        public readonly ?int $updated_by = null,
    ) {}
}
