<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        
        // Force HTTPS in production, but be smarter about it
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
            
            // Trust Railway proxies
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $this->app['request']->server->set('HTTPS', 'on');
            }
        }
    }
}
