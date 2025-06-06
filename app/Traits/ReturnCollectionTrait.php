<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ReturnCollectionTrait
{
    /**
     * Set model results in a standardized collection response.
     *
     * @param int|null $code
     * @param string|null $status
     * @param string|null $message
     * @param AnonymousResourceCollection|null $results
     * @return array
     */
    public function returnCollection(
        ?int $code = null,
        ?string $status = null,
        ?string $message = null,
        ?AnonymousResourceCollection $results = null
    ): array {
        return [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'results' => $results,
        ];
    }
}
