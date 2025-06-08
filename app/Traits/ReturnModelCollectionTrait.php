<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ReturnModelCollectionTrait
{
    /**
     * Set model results in a standardized response format.
     *
     * @param int|null $code       The HTTP status code (e.g., 200 for success, 400 for error).
     * @param string|null $status  The status of the request, typically 'success' or 'error'.
     * @param string|null $message A descriptive message, often used for success or error details.
     * @param Collection|LengthAwarePaginator|null $data The actual data being returned, which can either be a Laravel `Collection` or a `LengthAwarePaginator` (for paginated results).
     *
     * @return array Returns an array with the structure:
     *               [
     *                  'code' => $code,
     *                  'status' => $status,
     *                  'message' => $message,
     *                  'data' => $data,
     *               ]
     */
    public function returnModelCollection(
        ?int $code = null,
        ?string $status = null,
        ?string $message = null,
        Collection|LengthAwarePaginator|null $data = null
    ): array {
        return [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }
}
