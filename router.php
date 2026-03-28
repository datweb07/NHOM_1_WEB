<?php

//PHP built-in server (php -S)
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path       = parse_url($requestUri, PHP_URL_PATH);
$rootDir    = __DIR__;                                   
$publicDir  = $rootDir . DIRECTORY_SEPARATOR . 'public';

$filePath = $rootDir . $path;

if ($path !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false; 
}

chdir($publicDir);

require_once $publicDir . DIRECTORY_SEPARATOR . 'index.php';

?>