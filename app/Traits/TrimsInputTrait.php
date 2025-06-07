<?php

namespace App\Traits;

trait TrimsInputTrait
{
    /**
     * Recursively trim strings in the input array.
     *
     * @param array $data
     * @return array
     */
    protected function trimInputs(array $data): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return trim($value);
            }

            if (is_numeric($value)) {
                return trim($value);
            }

            if (is_array($value)) {
                return $this->trimInputs($value); // Handle nested arrays
            }

            return $value;
        }, $data);
    }
}
