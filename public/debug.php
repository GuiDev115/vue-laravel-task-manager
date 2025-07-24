<?php
// Debug endpoint para verificar o estado da aplicação
header('Content-Type: text/html; charset=UTF-8');

echo "<h1>🔍 Laravel Debug Info</h1>";

echo "<h2>📁 File System</h2>";
echo "<ul>";
echo "<li>Current Directory: " . __DIR__ . "</li>";
echo "<li>Vendor Autoload: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? '✅ EXISTS' : '❌ MISSING') . "</li>";
echo "<li>Bootstrap App: " . (file_exists(__DIR__ . '/../bootstrap/app.php') ? '✅ EXISTS' : '❌ MISSING') . "</li>";
echo "<li>.env File: " . (file_exists(__DIR__ . '/../.env') ? '✅ EXISTS' : '❌ MISSING') . "</li>";
echo "<li>Storage Writable: " . (is_writable(__DIR__ . '/../storage') ? '✅ YES' : '❌ NO') . "</li>";
echo "</ul>";

echo "<h2>🐘 PHP Info</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Memory Limit: " . ini_get('memory_limit') . "</li>";
echo "<li>Memory Usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "</ul>";

echo "<h2>📊 Environment Variables</h2>";
echo "<ul>";
$important_vars = ['APP_ENV', 'APP_DEBUG', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE'];
foreach ($important_vars as $var) {
    $value = $_ENV[$var] ?? getenv($var) ?? 'NOT SET';
    if ($var === 'APP_KEY' || strpos($var, 'PASSWORD') !== false) {
        $value = $value !== 'NOT SET' ? '***HIDDEN***' : 'NOT SET';
    }
    echo "<li>$var: $value</li>";
}
echo "</ul>";

echo "<h2>🧪 Laravel Test</h2>";
try {
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        echo "<p>✅ Autoloader loaded successfully</p>";
        
        if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            echo "<p>✅ Laravel app bootstrapped</p>";
            
            // Test artisan
            if (class_exists('Illuminate\Foundation\Application')) {
                echo "<p>✅ Laravel classes available</p>";
            }
        } else {
            echo "<p>❌ Bootstrap file not found</p>";
        }
    } else {
        echo "<p>❌ Vendor autoload not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>📍 File: " . $e->getFile() . ":" . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<p><a href='/'>← Back to Home</a> | <a href='/health.php'>Health Check</a> | <a href='/ping.php'>Ping</a></p>";
?>
