<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Error reporting for debugging in production temporarily
if (isset($_GET['debug']) && $_GET['debug'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
try {
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (Exception $e) {
    // Log error and show basic error page
    error_log("Laravel Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    
    if (isset($_GET['debug']) && $_GET['debug'] === 'true') {
        echo "<h1>Laravel Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        echo "Application Error";
    }
}
