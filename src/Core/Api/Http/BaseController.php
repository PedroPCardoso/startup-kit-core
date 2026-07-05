<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Api\Http;

use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Illuminate\Http\JsonResponse;

abstract class BaseController
{
    protected function respondWith(Result $result, int $okStatus = 200): JsonResponse
    {
        return $result->match(
            ok: fn(mixed $data) => new JsonResponse(
                ['data' => $data, 'status' => 'ok'],
                $okStatus,
            ),
            err: function ($error) {
                $status = $this->mapErrorCodeToHttpStatus($error->code());

                return new JsonResponse(
                    [
                        'status' => 'error',
                        'error' => [
                            'code' => $error->code(),
                            'message' => $error->message(),
                            'context' => $error->context(),
                        ],
                    ],
                    $status,
                );
            },
        );
    }

    private function mapErrorCodeToHttpStatus(string $code): int
    {
        return match (true) {
            str_starts_with($code, 'not_found') => 404,
            str_starts_with($code, 'validation') => 422,
            str_starts_with($code, 'unauthorized') => 401,
            str_starts_with($code, 'forbidden') => 403,
            str_starts_with($code, 'conflict') => 409,
            str_starts_with($code, 'timeout') => 504,
            default => 500,
        };
    }
}
