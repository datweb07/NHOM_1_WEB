<?php

namespace App\Core;

class Session
{
    // Session timeout in seconds (2 hours)
    private const TIMEOUT_DURATION = 7200;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check for session timeout
        self::checkTimeout();
    }

    private static function checkTimeout(): void
    {
        $lastActivity = self::get('last_activity');
        
        if ($lastActivity !== null) {
            $elapsed = time() - $lastActivity;
            
            if ($elapsed > self::TIMEOUT_DURATION) {
                // Session has timed out
                self::destroy();
                
                // Redirect to login with timeout message
                if (self::isAdmin()) {
                    header('Location: /admin/auth/login?timeout=1');
                } else {
                    header('Location: /auth/login?timeout=1');
                }
                exit;
            }
        }
        
        // Update last activity timestamp
        self::set('last_activity', time());
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    //xóa key
    public static function remove(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    //xóa toàn bộ session
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    //xử lý session_id cho khách vãng lai
    public static function getGuestSessionId(): string
    {
        self::start();
        if (!isset($_SESSION['guest_session_id'])) {

            //random session_id
            $_SESSION['guest_session_id'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['guest_session_id'];
    }


    public static function login(array $user): void
    {
        self::set('user_id', $user['id']);
        self::set('user_email', $user['email']);
        self::set('user_name', $user['ho_ten']);
        self::set('user_role', $user['loai_tai_khoan']); 
        self::set('last_activity', time());
        
        if (isset($user['avatar_url'])) {
            self::set('user_avatar', $user['avatar_url']);
        }
    }


    public static function logout(): void
    {
        self::remove('user_id');
        self::remove('user_email');
        self::remove('user_name');
        self::remove('user_role');
        self::remove('user_avatar');
    }


    public static function isLoggedIn(): bool
    {
        return self::get('user_id') !== null;
    }

 
    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }

    public static function getUserEmail(): ?string
    {
        return self::get('user_email');
    }

    public static function getUserName(): ?string
    {
        return self::get('user_name');
    }

    public static function getUserRole(): ?string
    {
        return self::get('user_role');
    }

    public static function getUserAvatar(): ?string
    {
        return self::get('user_avatar');
    }

    public static function isAdmin(): bool
    {
        return self::getUserRole() === 'ADMIN';
    }

    //lấy toàn bộ thông tin user đang login
    public static function getUser(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => self::getUserId(),
            'email' => self::getUserEmail(),
            'ho_ten' => self::getUserName(),
            'avatar_url' => self::getUserAvatar(),
            'loai_tai_khoan' => self::getUserRole()
        ];
    }
}

?>