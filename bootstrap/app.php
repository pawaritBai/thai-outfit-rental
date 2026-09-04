<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsCustomer;
use App\Http\Middleware\IsMakeUp;
use App\Http\Middleware\IsOwnShop;
use App\Http\Middleware\isActive;
use App\Http\Middleware\IsPhotographer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'is_admin' => IsAdmin::class,
            'is_makeup' => IsMakeUp::class,
            'is_shopowner' => IsOwnShop::class,
            'is_photographer' => IsPhotographer::class,
            'is_customer' => IsCustomer::class,
            'is_active' => isActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
