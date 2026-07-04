<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

        // Log every /api/* request + response into the dedicated `api` channel.
        $middleware->api(prepend: [
            \App\Http\Middleware\LogApiActivity::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'api.version' => \App\Http\Middleware\ApiVersionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Return JSON for all API route exceptions
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*'));

        // Centralised "try/catch" for every API controller method — logs the
        // exception (class, message, trace) into the `api` channel along with
        // request context, then lets Laravel render the JSON error response.
        $exceptions->report(function (\Throwable $e) {
            $request = request();
            if (!$request || !$request->is('api/*')) {
                return; // let default handler cover non-API errors
            }

            // Skip validation errors — they're already returned as 422 responses;
            // logging every one just adds noise.
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return;
            }

            \Illuminate\Support\Facades\Log::channel('api')->error('API exception', [
                'exception' => class_basename($e),
                'message'   => $e->getMessage(),
                'file'      => $e->getFile() . ':' . $e->getLine(),
                'method'    => $request->method(),
                'path'      => '/' . ltrim($request->path(), '/'),
                'user_id'   => optional($request->user())->id,
                'trace'     => collect($e->getTrace())->take(10)->map(fn ($f) =>
                    ($f['file'] ?? '?') . ':' . ($f['line'] ?? '?') . ' — ' . ($f['function'] ?? '?')
                )->all(),
            ]);
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Resource not found.'], 404);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Resource not found.'], 404);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                $message = app()->isProduction() ? 'Server error.' : $e->getMessage();
                return response()->json(['success' => false, 'message' => $message], 500);
            }
        });
    })->create();
