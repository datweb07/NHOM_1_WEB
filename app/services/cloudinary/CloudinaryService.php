<?php

use Cloudinary\Cloudinary;
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

class CloudinaryService
{
    private static $instance = null;

    public static function getInstance()
    {
        $envConfig = EnvSetup::env(dirname(__DIR__, 3)); 

        if (self::$instance === null) {
            $cloudName = $envConfig('CLOUDINARY_CLOUD_NAME');
            $apiKey    = $envConfig('CLOUDINARY_API_KEY');
            $apiSecret = $envConfig('CLOUDINARY_API_SECRET');

            $cloudinaryUrl = "cloudinary://{$apiKey}:{$apiSecret}@{$cloudName}?secure=true";

            self::$instance = new Cloudinary($cloudinaryUrl);
        }
        
        return self::$instance;
    }
}