<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Format response sukses standard
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'meta' => [
                'status' => 'success',
                'code' => $code,
                'message' => $message,
            ],
            'data' => $data,
        ], $code);
    }

    /**
     * Format response error standard
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code, $errors = null): JsonResponse
    {
        return response()->json([
            'meta' => [
                'status' => 'error',
                'code' => $code,
                'message' => $message,
            ],
            'data' => null,
            'errors' => $errors,
        ], $code);
    }
}