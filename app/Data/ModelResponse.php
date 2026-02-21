<?php

namespace App\Data;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class ModelResponse extends BaseResponse
{
    /**
     * Constructor for ModelResponse.
     */
    public function __construct(
        int $code,
        string $status,
        string $message,
        public readonly ?Model $data = null,
        public readonly ?int $lastId = null
    ) {
        // call BaseResponse constructor
        parent::__construct($code, $status, $message);
    }

    /**
     * Create a success response.
     */
    public static function success(
        int $code = 200,
        string $status = Helper::SUCCESS,
        string $message = 'Success',
        ?Model $data = null,
        ?int $lastId = null
    ): self {
        return new self($code, $status, $message, $data, $lastId);
    }

    /**
     * Create an error response.
     */
    public static function error(
        int $code = 400,
        string $status = Helper::ERROR,
        string $message = 'Error',
        ?Model $data = null,
        ?int $lastId = null
    ): self {
        return new self($code, $status, $message, $data, $lastId);
    }

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'last_id' => $this->lastId,
            'data' => $this->data,
        ]);
    }
}
