<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait ReturnModelCollectionTrait
{
    /**
     * Set model results in a standardized response format.
     *
     * @param int|null $code
     * @param string|null $status
     * @param string|null $message
     * @param Collection|null $data
     * @return array
     */
    public function returnModelCollection(
        ?int $code = null,
        ?string $status = null,
        ?string $message = null,
        ?Collection $data = null
    ): array {
        return [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }
}
