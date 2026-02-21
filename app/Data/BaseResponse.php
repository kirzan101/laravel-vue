<?php

namespace App\Data;

abstract class BaseResponse
{
    public function __construct(
        public readonly int $code,
        public readonly string $status,
        public readonly string $message
    ) {}

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'status' => $this->status,
            'message' => $this->message,
        ];
    }
}
