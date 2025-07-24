<?php
// Test endpoint - no Laravel, no redirects
echo "OK - Direct PHP Response";
echo "<br>Time: " . date('Y-m-d H:i:s');
echo "<br>Server: " . ($_SERVER['HTTP_HOST'] ?? 'unknown');
echo "<br>Protocol: " . ($_SERVER['REQUEST_SCHEME'] ?? 'unknown');
echo "<br>HTTPS: " . (isset($_SERVER['HTTPS']) ? 'YES' : 'NO');
echo "<br>X-Forwarded-Proto: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set');
?>
