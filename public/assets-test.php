<?php
// Test asset serving
header('Content-Type: text/html; charset=UTF-8');

echo "<h1>🔍 Asset Debug</h1>";

echo "<h2>📁 Build Directory</h2>";
$buildDir = __DIR__ . '/build';
if (is_dir($buildDir)) {
    echo "<p>✅ Build directory exists</p>";
    echo "<pre>";
    system("ls -la " . escapeshellarg($buildDir));
    echo "</pre>";
    
    $assetsDir = $buildDir . '/assets';
    if (is_dir($assetsDir)) {
        echo "<h3>📦 Assets Directory</h3>";
        echo "<pre>";
        system("ls -la " . escapeshellarg($assetsDir));
        echo "</pre>";
    } else {
        echo "<p>❌ Assets directory not found</p>";
    }
} else {
    echo "<p>❌ Build directory not found</p>";
}

echo "<h2>🌐 Test Asset URLs</h2>";
$manifest = __DIR__ . '/build/manifest.json';
if (file_exists($manifest)) {
    $manifestData = json_decode(file_get_contents($manifest), true);
    echo "<h3>📜 Manifest Contents:</h3>";
    echo "<pre>" . json_encode($manifestData, JSON_PRETTY_PRINT) . "</pre>";
    
    foreach ($manifestData as $file => $data) {
        if (isset($data['file'])) {
            $assetUrl = '/build/' . $data['file'];
            echo "<p>Testing: <a href='$assetUrl' target='_blank'>$assetUrl</a></p>";
        }
    }
} else {
    echo "<p>❌ Manifest file not found</p>";
}

echo "<hr>";
echo "<p><a href='/'>← Back to Home</a> | <a href='/debug.php'>Debug</a></p>";
?>
