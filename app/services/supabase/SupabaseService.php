<?php

require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

class SupabaseAuthService
{
    public static function getGoogleLoginUrl()
    {
        $envConfig = EnvSetup::env(dirname(__DIR__, 3)); 
        
        $baseUrl = $envConfig('SUPABASE_URL') ?? '';
        $appUrl = $envConfig('APP_URL') ?? '';
        
        $redirectUrl = $appUrl . '/app/views/client/auth/callback.php'; 
        
        return $baseUrl . '/auth/v1/authorize?provider=google&redirect_to=' . urlencode($redirectUrl);
    }

    //gọi api đến supabase để verify token
    public static function verifyUserToken($accessToken)
    {
        $envConfig = EnvSetup::env(dirname(__DIR__, 3)); 
        
        $supabaseUrl = $envConfig('SUPABASE_URL') ?? '';
        $anonKey = $envConfig('SUPABASE_ANON_KEY') ?? '';

        //dùng cURL gọi Supabase để hỏi xem token này là của ai
        $ch = curl_init($supabaseUrl . '/auth/v1/user');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $anonKey,
            'Authorization: Bearer ' . $accessToken
        ]);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            error_log("Supabase API Error: " . $response);
            return null;
        }

        $userData = json_decode($response);

        if (isset($userData->id)) {
            $userData->sub = $userData->id;
            return $userData;
        }

        return null;
    }
}