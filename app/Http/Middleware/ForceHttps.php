<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only force HTTPS in production and when not already secure
        if (
            env('APP_ENV') === 'production' && 
            !$request->secure() && 
            !$request->isLocal()
        ) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
