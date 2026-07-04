<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Structured request-in / response-out logger for the API stack.
 *
 * Equivalent to wrapping every controller method in try/catch + info logs —
 * but centralised, so no controller changes are needed. Exceptions themselves
 * are logged by the $exceptions->report() hook in bootstrap/app.php.
 */
class LogApiActivity
{
    /** Request fields that should never hit the log file. */
    private const SENSITIVE_KEYS = [
        'password', 'password_confirmation', 'current_password',
        'token', 'api_key', 'secret',
        'card', 'card_number', 'cvv', 'cvc',
        'MYFATOORAH_API_KEY',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        Log::channel('api')->info('API request', [
            'method'   => $request->method(),
            'path'     => '/' . ltrim($request->path(), '/'),
            'ip'       => $request->ip(),
            'user_id'  => optional($request->user())->id,
            'params'   => $this->scrub($request->all()),
            'time'     => now()->toIso8601String(),
        ]);

        $response = $next($request);

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
        $status = $response->getStatusCode();

        Log::channel('api')->log(
            $status >= 500 ? 'error' : ($status >= 400 ? 'warning' : 'info'),
            'API response',
            [
                'method'      => $request->method(),
                'path'        => '/' . ltrim($request->path(), '/'),
                'status'      => $status,
                'duration_ms' => $durationMs,
            ]
        );

        return $response;
    }

    /** Recursively strip password/token/card-style keys before logging. */
    private function scrub(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), array_map('strtolower', self::SENSITIVE_KEYS), true)) {
                $data[$key] = '***';
                continue;
            }
            if (is_array($value)) {
                $data[$key] = $this->scrub($value);
            }
        }
        return $data;
    }
}
