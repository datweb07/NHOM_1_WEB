<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../core/Session.php';
require_once __DIR__ . '/../../../services/supabase/SupabaseService.php';
require_once __DIR__ . '/../../../models/roles/KhachHang.php';

use App\Core\Session;

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'method_not_allowed']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['access_token'])) {
    echo json_encode(['success' => false, 'error' => 'missing_token']);
    exit;
}

$accessToken = $data['access_token'];

// Verify token with Supabase
$userData = SupabaseAuthService::verifyUserToken($accessToken);

if ($userData === null) {
    error_log("Google OAuth: Token verification failed");
    echo json_encode(['success' => false, 'error' => 'invalid_token']);
    exit;
}

// Extract user information from token
$supabaseId = $userData->sub ?? null; // UUID from Supabase
$email = $userData->email ?? null;
$name = $userData->user_metadata->full_name ?? $userData->user_metadata->name ?? 'User';
$avatarUrl = $userData->user_metadata->avatar_url ?? $userData->user_metadata->picture ?? null;

if (!$supabaseId || !$email) {
    error_log("Google OAuth: Missing supabase_id or email in token");
    echo json_encode(['success' => false, 'error' => 'missing_user_data']);
    exit;
}

$khachHang = new KhachHang();

// Step 1: Tìm user theo supabase_id
$existingUser = $khachHang->query("SELECT * FROM nguoi_dung WHERE supabase_id = '" . addslashes($supabaseId) . "' LIMIT 1");

if (!empty($existingUser)) {
    // User đã từng đăng nhập Google -> Cập nhật avatar nếu cần
    $userId = $existingUser[0]['id'];
    
    if ($avatarUrl && $avatarUrl !== $existingUser[0]['avatar_url']) {
        $khachHang->update($userId, [
            'avatar_url' => $avatarUrl,
            'ngay_cap_nhat' => date('Y-m-d H:i:s')
        ]);
    }
    
    $khachHang->setId($userId);
    $khachHang->setEmail($existingUser[0]['email']);
    $khachHang->setHoTen($existingUser[0]['ho_ten']);
    $khachHang->setAvatarUrl($avatarUrl ?? $existingUser[0]['avatar_url']);
    $khachHang->setLoaiTaiKhoan($existingUser[0]['loai_tai_khoan']);
    
    error_log("Google OAuth: User found by supabase_id - " . $email);
} else {
    // Step 2: Không tìm thấy supabase_id -> Tìm theo email
    $existingUser = $khachHang->query("SELECT * FROM nguoi_dung WHERE email = '" . addslashes($email) . "' LIMIT 1");
    
    if (!empty($existingUser)) {
        // User đã có tài khoản LOCAL -> Liên kết với Google
        $userId = $existingUser[0]['id'];
        
        $khachHang->update($userId, [
            'supabase_id' => $supabaseId,
            'auth_provider' => 'GOOGLE',
            'avatar_url' => $avatarUrl ?? $existingUser[0]['avatar_url'],
            'ngay_cap_nhat' => date('Y-m-d H:i:s')
        ]);
        
        $khachHang->setId($userId);
        $khachHang->setEmail($existingUser[0]['email']);
        $khachHang->setHoTen($existingUser[0]['ho_ten']);
        $khachHang->setAvatarUrl($avatarUrl ?? $existingUser[0]['avatar_url']);
        $khachHang->setLoaiTaiKhoan($existingUser[0]['loai_tai_khoan']);
        
        error_log("Google OAuth: Existing user linked with Google - " . $email);
    } else {
        // Step 3: User hoàn toàn mới -> Tạo tài khoản mới
        $now = date('Y-m-d H:i:s');
        $newUserId = $khachHang->create([
            'supabase_id' => $supabaseId,
            'auth_provider' => 'GOOGLE',
            'email' => $email,
            'ho_ten' => $name,
            'avatar_url' => $avatarUrl,
            'mat_khau' => null, // NULL for OAuth users
            'loai_tai_khoan' => 'MEMBER',
            'trang_thai' => 'ACTIVE', // Google OAuth users are pre-verified
            'ngay_tao' => $now,
            'ngay_cap_nhat' => $now
        ]);
        
        if (!$newUserId) {
            error_log("Google OAuth: Failed to create new user - " . $email);
            echo json_encode(['success' => false, 'error' => 'create_user_failed']);
            exit;
        }
        
        $khachHang->setId($newUserId);
        $khachHang->setEmail($email);
        $khachHang->setHoTen($name);
        $khachHang->setAvatarUrl($avatarUrl);
        $khachHang->setLoaiTaiKhoan('MEMBER');
        
        error_log("Google OAuth: New user created - " . $email);
    }
}

// Create session
Session::start();
Session::login([
    'id' => $khachHang->getId(),
    'email' => $khachHang->getEmail(),
    'ho_ten' => $khachHang->getHoTen(),
    'loai_tai_khoan' => $khachHang->getLoaiTaiKhoan(),
    'avatar_url' => $khachHang->getAvatarUrl()
]);

error_log("Google OAuth: Session created successfully for " . $email);

// Return success response
echo json_encode([
    'success' => true,
    'redirect' => '/client/profile'
]);
