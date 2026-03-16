<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\ApiExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $isApi = fn($request) => $request->is('api/*');

        $exceptions->render(function (AuthenticationException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiExceptionHandler::error('Unauthenticated.', 401);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiExceptionHandler::error('Resource not found.', 404);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiExceptionHandler::error('Resource not found.', 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiExceptionHandler::error('Method not allowed.', 405);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiExceptionHandler::error(
                    'Validation failed.',
                    422,
                    $e->errors()
                );
            }
        });
    })->create();
