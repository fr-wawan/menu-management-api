<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function success(mixed $data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function paginate(ResourceCollection $collection, string $message = '', int $code = 200): JsonResponse
    {
        $paginated = $collection->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginated['data'],
            'links'   => $paginated['links'],
            'meta'    => $paginated['meta'],
        ], $code);
    }

    protected function error(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
