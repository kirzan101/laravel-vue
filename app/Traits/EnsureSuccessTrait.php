<?php

namespace App\Traits;

use RuntimeException;
use App\Helpers\Helper;

trait EnsureSuccessTrait
{
    protected function ensureSuccess(array $response, string $fallbackMessage): void
    {
        if (($response['status'] ?? null) === Helper::ERROR) {
            throw new RuntimeException($response['message'] ?? $fallbackMessage);
        }
    }
}
