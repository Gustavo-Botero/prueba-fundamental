<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ValidationExceptionHandler
{
    /**
     * Handle the validation exception.
     *
     * @param ValidationException $exception
     * @return JsonResponse
     */
    public static function handle(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $exception->errors(),
        ], 422);
    }
}
