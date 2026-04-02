<?php

use Cloudinary\Cloudinary;
require_once __DIR__ . '/vendor/autoload.php';

class CloudinaryService
{
    private static $instance = null;

    public static function getInstance()
    {
        $envConfig = EnvSetup::env(__DIR__);
        if (self::$instance === null) {
            self::$instance = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $envConfig('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => $envConfig('CLOUDINARY_API_KEY'),
                    'api_secret' => $envConfig('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true // Ép sử dụng HTTPS
                ]
            ]);
        }
        return self::$instance;
    }
}

// Cách sử dụng ở nơi khác:
// $cloudinary = CloudinaryService::getInstance();
// $cloudinary->uploadApi()->upload('duong_dan_anh.jpg');