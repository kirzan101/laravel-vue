<?php

namespace App\DTOs;

class ManageRoleDTO extends BaseDTO
{
    /**
     * Create a new ManageRolePermissionDTO instance.
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?int $user_group_id = null,
        public bool $is_active = true,
        public readonly ?array $permissionIds = [],
    ) {}

    /**
     * Create a new ManageRolePermissionDTO instance with updated permission IDs.
     *
     * @param array|null $permissionIds
     * @return self
     */
    public function withPermissionIds(?array $permissionIds): self
    {
        $data = array_merge($this->toArray(), ['permissionIds' => $permissionIds]);

        return self::fromArray($data);
    }
}
