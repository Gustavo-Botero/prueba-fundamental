<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionHandler
{
    /**
     * Handle the HTTP exception.
     *
     * @param HttpException $exception
     * @return JsonResponse
     */
    public static function handle(HttpException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $exception->getMessage(),
        ], $exception->getStatusCode());
    }
}
