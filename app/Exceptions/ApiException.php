<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception
{
    public function __construct(string $message, protected int $status = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], $this->status);
    }

    public static function notFound(string $message): JsonResponse
    {
        return (new self($message, Response::HTTP_NOT_FOUND))->render();
    }

    public static function unauthorized(string $message = "Unauthorized."): JsonResponse
    {
        return (new self($message, Response::HTTP_UNAUTHORIZED))->render();
    }
}
