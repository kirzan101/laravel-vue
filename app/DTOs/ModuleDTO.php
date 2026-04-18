<?php

namespace App\DTOs;

class ModuleDTO extends BaseDTO
{
    /**
     * Create a new ModuleDTO instance.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $icon = null,
        public ?string $route = null,
        public ?string $category = null,
        public bool $is_active = true,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
