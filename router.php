<?php

/**
 * Router script for PHP built-in server (php -S)
 * Replaces the functionality of .htaccess RewriteRule for development.
 * Start server: php -S localhost:3000 router.php  (from project root)
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path       = parse_url($requestUri, PHP_URL_PATH);
$rootDir    = __DIR__;                                   // d:\NHOM_1_WEB
$publicDir  = $rootDir . DIRECTORY_SEPARATOR . 'public'; // d:\NHOM_1_WEB\public

// Check static files relative to ROOT (because paths like /public/assets/... start from root)
$filePath = $rootDir . $path;

if ($path !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false; // Let PHP built-in server serve static file as-is
}

// Change working directory to /public so that require '../app/...' in index.php work
chdir($publicDir);

// Route all dynamic requests through the front controller
require_once $publicDir . DIRECTORY_SEPARATOR . 'index.php';
