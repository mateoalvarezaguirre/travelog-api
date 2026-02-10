<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middlewares\ForceJsonResponseMiddleware::class,
            \App\Http\Middlewares\CamelCaseMiddleware::class,
        ]);
        $middleware->alias([
            'optional_auth' => \App\Http\Middlewares\OptionalSanctumMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request): JsonResponse {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request): JsonResponse {
            return response()->json(['message' => $e->getMessage() ?: 'Forbidden.'], 403);
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request): JsonResponse {
            return response()->json(['message' => 'Resource not found.'], 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request): JsonResponse {
            return response()->json(['message' => 'Not found.'], 404);
        });

        $exceptions->render(function (ValidationException $e, Request $request): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request): JsonResponse {
            return response()->json(['message' => 'Too many requests.'], 429);
        });
    })->create();
