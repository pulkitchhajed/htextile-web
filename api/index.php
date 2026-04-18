<?php
/**
 * Vercel PHP Router - Single Entry Point
 * All requests are routed through this file
 */

// Get the requested path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Strip leading slash
$path = ltrim($path, '/');

// Default to index.php
if (empty($path) || $path === '/') {
    $path = 'index.php';
}

// Security: prevent directory traversal
$path = str_replace(['../', '..\\', '..'], '', $path);

// Build the full file path (relative to project root)
$projectRoot = dirname(__DIR__);
$fullPath = $projectRoot . '/' . $path;

// If the path is a directory, look for index.php inside it
if (is_dir($fullPath)) {
    $fullPath = rtrim($fullPath, '/') . '/index.php';
    $path = rtrim($path, '/') . '/index.php';
}

// Check if the file exists
if (file_exists($fullPath)) {
    if (pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
        // Set the working directory to the file's directory
        chdir(dirname($fullPath));
        // Include and execute the file
        require $fullPath;
    } else {
        // Serve static file fallback (for local php -S dev or Vercel edge cases)
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'bmp' => 'image/bmp'
        ];

        if (array_key_exists($ext, $mimeTypes)) {
            header("Content-Type: " . $mimeTypes[$ext]);
            header("Cache-Control: public, max-age=31536000");
            readfile($fullPath);
            exit;
        } else {
            // File not found or extension not allowed to be served directly
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
        }
    }
} else {
    // File not found - return 404
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested page <strong>" . htmlspecialchars($path) . "</strong> could not be found.</p>";
}
