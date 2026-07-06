<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Api\Http;

use PedroPCardoso\StartupKit\Core\Api\Services\ShutdownService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class ShutdownController extends Controller
{
    public function __construct(
        private readonly ShutdownService $shutdown,
    ) {}

    public function __invoke(): JsonResponse
    {
        $result = $this->shutdown->shutdown();

        return new JsonResponse($result, 202);
    }
}
