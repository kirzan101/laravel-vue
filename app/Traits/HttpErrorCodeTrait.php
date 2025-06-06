<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

trait HttpErrorCodeTrait
{
    /**
     * Get the HTTP status code based on the exception instance.
     *
     * @param Throwable $exception
     * @return int
     */
    public function httpCode(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof ModelNotFoundException        => 404,
            $exception instanceof AuthorizationException        => 403,
            $exception instanceof ValidationException           => 422,
            $exception instanceof NotFoundHttpException         => 404,
            $exception instanceof MethodNotAllowedHttpException => 405,
            $exception instanceof AuthenticationException       => 401,
            $exception instanceof PostTooLargeException         => 413,
            $exception instanceof TooManyRequestsHttpException  => 429,
            $exception instanceof TokenMismatchException        => 419,
            $exception instanceof HttpExceptionInterface        => $exception->getStatusCode(),
            default => 500,
        };
    }
}
