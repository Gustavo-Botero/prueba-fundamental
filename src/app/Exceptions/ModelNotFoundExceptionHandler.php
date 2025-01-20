<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ModelNotFoundExceptionHandler extends Exception
{
    /**
     * Handle the model not found exception.
     *
     * @param ModelNotFoundException $exception
     * @return JsonResponse
     */
    public static function handle(ModelNotFoundException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Resource not found',
        ], 404);
    }
}
