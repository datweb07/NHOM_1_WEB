<?php

use Dotenv\Dotenv;

class EnvSetup
{
    public static function env($path)
    {
        $loaded = Dotenv::createArrayBacked($path)->load();

        return function ($key, $default = '') use ($loaded) {
            if (array_key_exists($key, $loaded)) {
                return $loaded[$key];
            }
            $val = getenv($key);
            return $val !== false ? $val : $default;
        };
    }
}