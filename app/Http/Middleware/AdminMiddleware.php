<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        if (method_exists($user, 'hasAnyRole') && ! $user->hasAnyRole(['super_admin', 'admin'])) {
            abort(403);
        }

        return $next($request);
    }
}

