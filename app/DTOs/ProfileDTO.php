<?php

namespace App\DTOs;

use App\Models\Profile;

class ProfileDTO extends AuditableDTO
{
    /**
     * Create a new ProfileDTO instance.
     */
    public function __construct(
        public readonly ?string $avatar = null,
        public readonly ?string $first_name = null,
        public readonly ?string $middle_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $nickname = null,
        public readonly ?string $type = null,
        public readonly array $contact_numbers = [],
        public readonly ?int $user_id = null,
        public readonly ?int $id = null,
        public readonly ?int $created_by = null,
        public readonly ?int $updated_by = null,
    ) {
        $this->contact_numbers = is_array($contact_numbers) ? $contact_numbers : [];
    }

    /**
     * Create a ProfileDTO with explicit user info.
     */
    public static function withUser(int $userId, array $data = []): self
    {
        $data['user_id'] = $userId;
        return self::fromArray($data);
    }
}
