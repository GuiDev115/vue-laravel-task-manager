<?php
// Simple health check endpoint
header('Content-Type: application/json');
http_response_code(200);

echo json_encode([
    'status' => 'OK',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'laravel_ready' => file_exists(__DIR__ . '/../vendor/autoload.php') && file_exists(__DIR__ . '/../bootstrap/app.php'),
    'storage_writable' => is_writable(__DIR__ . '/../storage'),
    'memory_usage' => memory_get_usage(true),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
]);
