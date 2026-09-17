<?php

namespace App\Application\Services;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, ?string $message = null, array $meta = [], int $status = 200): JsonResponse
    {
        $response = ['success' => true, 'data' => $data];
        if ($message) {
            $response['message'] = $message;
        }
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }
        return response()->json($response, $status);
    }

    public static function created(mixed $data = null, ?string $message = null): JsonResponse
    {
        return static::success($data, $message, status: 201);
    }

    public static function error(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status);
    }

    public static function unauthorized(string $message = ''): JsonResponse
    {
        return static::error($message ?: __('auth.unauthorized'), status: 403);
    }

    public static function notFound(string $message = ''): JsonResponse
    {
        return static::error($message ?: __('common.not_found'), status: 404);
    }

    public static function paginated(\Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator, callable $transform = null): JsonResponse
    {
        $items = $transform ? $paginator->getCollection()->map($transform) : $paginator->items();
        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }
}
