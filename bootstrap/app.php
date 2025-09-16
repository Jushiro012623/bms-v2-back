<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $exceptions->renderable(function (Exception $e, Request $request) {
            if (Str::startsWith($request->path(), 'api/')) {
                if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
                    $details = "The requested endpoint '{$request->getRequestUri()}' was not found.";
                    return response()->error($details, [], 404);
                }
                if ($e instanceof ValidationException) {
                    return response()->error('The provided data is invalid.', $e->errors(), 422);
                }
                if ($e instanceof QueryException) {
                    if ($e instanceof QueryException) {
                        $errorCode = $e->errorInfo[1] ?? null;

                        [$title, $code] = match ($errorCode) {
                            1451 => ['Foreign key constraint violation.', 409],
                            1062 => ['Duplicate entry constraint violation.', 409],
                            default => ['Unknown database error.', 500],
                        };

                        $details = app()->environment(['local', 'testing'])
                            ? ['query' => $e->errorInfo[2] ?? null]
                            : [];

                        return response()->error($title, $details, $code);
                    }
                }
                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->error(
                        "The {$request->method()} method is not allowed for this endpoint.",
                        ['allowed_methods' => $e->getHeaders()['Allow'] ?? 'Unknown'],
                        405
                    );
                }

                $debug = [
                        'exception' => [class_basename($e)]
                ];

                if (app()->environment('local', 'testing')) {
                        $debug['info']  = $e->getMessage();
                        $debug['file']  = $e->getFile();
                        $debug['line']  = $e->getLine();
                        $debug['trace'] = $e->getTraceAsString();
                }
                return response()->error(
                    'An unexpected error occurred. Please try again later.',
                    $debug,
                    500
                );
            }
        });
    })->create();
