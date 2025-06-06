<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait ReturnModelTrait
{
    /**
     * Set model result in a standardized response format.
     *
     * @param int|null $code
     * @param string|null $status
     * @param string|null $message
     * @param int|null $lastId
     * @param Model|null $result
     * @return array
     */
    public function returnModel(
        ?int $code = null,
        ?string $status = null,
        ?string $message = null,
        ?int $lastId = null,
        ?Model $result = null
    ): array {
        return [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'last_id' => $lastId,
            'result' => $result,
        ];
    }
}
