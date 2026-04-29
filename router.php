<?php

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path       = parse_url($requestUri, PHP_URL_PATH);
$rootDir    = __DIR__;                                   
$publicDir  = $rootDir . DIRECTORY_SEPARATOR . 'public';

if (strpos($path, '/public/') === 0) {
    $path = substr($path, 7); 
}

$filePath = $publicDir . str_replace('/', DIRECTORY_SEPARATOR, $path);

if ($path !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
        chdir($publicDir);
        require $filePath;
        return true;
    }
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'js' => 'application/javascript',
        'css' => 'text/css',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($filePath);
        return true;
    }
    
    return false; 
}

chdir($publicDir);

require_once $publicDir . DIRECTORY_SEPARATOR . 'index.php';

?>