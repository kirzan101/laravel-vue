<?php

namespace App\DTOs;

class ModuleDTO extends BaseDTO
{
    /**
     * Create a new ModuleDTO instance.
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $icon = null,
        public readonly ?string $route = null,
        public readonly ?string $category = null,
        public readonly bool $is_active = true,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
