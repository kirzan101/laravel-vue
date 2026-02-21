<?php

namespace App\Data;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Collection;

class CollectionResponse extends BaseResponse
{
    /**
     * Constructor for CollectionResponse.
     */
    public function __construct(
        int $code,
        string $status,
        string $message,
        public readonly ?Collection $data = null
    ) {
        parent::__construct($code, $status, $message);
    }

    /**
     * Create a success response.
     */
    public static function success(
        int $code = 200,
        string $status = Helper::SUCCESS,
        string $message = 'Success',
        ?Collection $data = null
    ): self {
        return new self($code, $status, $message, $data);
    }

    /**
     * Create an error response.
     */
    public static function error(
        int $code = 400,
        string $status = Helper::ERROR,
        string $message = 'Error',
        ?Collection $data = null
    ): self {
        return new self($code, $status, $message, $data);
    }

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'data' => $this->data,
        ]);
    }
}
