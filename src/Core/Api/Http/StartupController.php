<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Api\Http;

use PedroPCardoso\StartupKit\Core\Api\Services\StartupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class StartupController extends Controller
{
    public function __construct(
        private readonly StartupService $startup,
    ) {}

    public function __invoke(): JsonResponse
    {
        $result = $this->startup->status();

        return new JsonResponse($result);
    }
}
