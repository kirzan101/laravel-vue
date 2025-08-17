<?php

namespace App\DTOs;

class AccountDTO
{
    public function __construct(
        public readonly UserDTO $user,
        public readonly ProfileDTO $profile,
        public readonly ?int $user_group_id = null,
    ) {}
}
