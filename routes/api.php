<?php

declare(strict_types=1);

use Cardoso\StartupKit\Core\Api\Http\HealthController;
use Cardoso\StartupKit\Core\Api\Http\ShutdownController;
use Cardoso\StartupKit\Core\Api\Http\StartupController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('startup-kit-core.routes.prefix', ''))
    ->middleware(config('startup-kit-core.routes.middleware', ['api']))
    ->group(function () {
        Route::get('health', HealthController::class);
        Route::post('shutdown', ShutdownController::class);
        Route::get('startup', StartupController::class);
    });
