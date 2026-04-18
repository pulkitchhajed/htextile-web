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

// Check if the PHP file exists
if (file_exists($fullPath) && pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
    // Set the working directory to the file's directory
    chdir(dirname($fullPath));
    // Include and execute the file
    require $fullPath;
} else {
    // File not found - return 404
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested page <strong>" . htmlspecialchars($path) . "</strong> could not be found.</p>";
}
