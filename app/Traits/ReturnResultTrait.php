<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

trait ReturnResultTrait
{
    /**
     * Set model result in a standardized response.
     *
     * @param int|null $code
     * @param string|null $status
     * @param string|null $message
     * @param int|null $lastId
     * @param JsonResource|null $result
     * @return array
     */
    public function returnResult(
        ?int $code = null,
        ?string $status = null,
        ?string $message = null,
        ?int $lastId = null,
        ?JsonResource $result = null
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
