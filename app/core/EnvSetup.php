<?php

use Dotenv\Dotenv;

class EnvSetup
{
    public static function env($path): array
    {
        $loaded = Dotenv::createArrayBacked($path)->safeLoad();

        $getEnvVal = function ($key, $default = '') use ($loaded) {
            if (array_key_exists($key, $loaded)) {
                return $loaded[$key];
            }
            $val = getenv($key);
            return $val !== false ? $val : $default;
        };

        $dbHost     = $getEnvVal('DB_HOST', 'localhost');
        $dbPort     = $getEnvVal('DB_PORT', '3307');
        $dbDatabase = $getEnvVal('DB_DATABASE', 'db_web');
        $dbUsername = $getEnvVal('DB_USERNAME', 'root');
        $dbPassword = $getEnvVal('DB_PASSWORD', '123456');

        if (empty($dbDatabase)) {
            throw new \Exception('Could not load DB_DATABASE. Please check your .env file.');
        }

        return [
            'DB_HOST'     => $dbHost,
            'DB_PORT'     => $dbPort,
            'DB_DATABASE' => $dbDatabase,
            'DB_USERNAME' => $dbUsername,
            'DB_PASSWORD' => $dbPassword,
        ];
    }
}