<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/core/EnvSetup.php';

$envConfig = EnvSetup::env(dirname(__DIR__));
$app_url = $envConfig('APP_URL') ?: 'http://localhost:3000/';

define('BASE_URL', rtrim($app_url, '/'));

if (strpos($app_url, 'localhost') !== false) {
    define('ASSET_URL', BASE_URL . '/public');
} else {
    define('ASSET_URL', BASE_URL);
}

define('DB_HOST', $envConfig('DB_HOST'));
define('DB_NAME', $envConfig('DB_DATABASE'));
define('DB_PORT', $envConfig('DB_PORT'));
define('DB_USER', $envConfig('DB_USERNAME'));
define('DB_PASSWORD', $envConfig('DB_PASSWORD'));

?>