<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    
    public function handle(Request $request, Closure $next): Response
    {
        // Only force HTTPS in production and when not already secure
        if (
            env('APP_ENV') === 'production' && 
            !$request->secure() && 
            !in_array($request->ip(), ['127.0.0.1', '::1', 'localhost'])
        ) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
