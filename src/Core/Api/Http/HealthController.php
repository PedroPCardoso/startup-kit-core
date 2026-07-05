<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Api\Http;

use Cardoso\StartupKit\Core\Api\Services\HealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class HealthController extends Controller
{
    public function __construct(
        private readonly HealthService $health,
    ) {}

    public function __invoke(): JsonResponse
    {
        $result = $this->health->check();

        $statusCode = $result['status'] === 'ok' ? 200 : 503;

        return new JsonResponse($result, $statusCode);
    }
}
