<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HttpsProtocol
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->secure() && env('APP_FORCE_HTTPS', false)) {
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);
    }
}