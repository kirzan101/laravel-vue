<?php

namespace App\DTOs;

use App\Helpers\Helper;

class UserDTO extends BaseDTO
{
    /**
     * The user's password.
     */
    public readonly ?string $password;

    /**
     * Create a new UserDTO instance.
     */
    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $email = null,
        ?string $password = null,
        public readonly string $status = Helper::ACCOUNT_STATUS_ACTIVE,
        public readonly bool $is_admin = false,
        public readonly bool $is_first_login = true,
        ?int $id = null,
    ) {
        // If no password is provided, default to the username (if set)
        $rawPassword = $password ?? $this->username;

        // If still null (username also not set), leave as null
        $this->password = $rawPassword !== null ? bcrypt($rawPassword) : null;
    }

    /**
     * Create a UserDTO for an already logged-in user.
     */
    public static function isAlreadyLoggedIn(array $data): self
    {
        return self::fromArray([
            ...$data,
            'is_first_login' => false,
        ]);
    }

    /**
     * Convert to array but hide password by default.
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        unset($array['password']); // Remove password from array
        return $array;
    }
}
