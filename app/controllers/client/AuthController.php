<?php

namespace App\Controllers\Client;

require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../core/Functions.php';
require_once __DIR__ . '/../../core/EnvSetup.php';
require_once __DIR__ . '/../../models/roles/KhachHang.php';

use App\Core\Session;

class AuthController
{
  public static function login(string $email, string $password): bool
  {

    $envConfig = \EnvSetup::env(dirname(__DIR__, 3));
    $recaptchaSecret = $envConfig('RECAPTCHA_SECRET_KEY', '');

    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    if (empty($recaptchaResponse)) {
      header('Location: /client/auth/login?error=captcha_missing');
      exit;
    }

    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
      'secret' => $recaptchaSecret,
      'response' => $recaptchaResponse
    ];

    $options = [
      'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
      ]
    ];
    $context = stream_context_create($options);
    $verifyResponse = file_get_contents($verifyUrl, false, $context);

    if ($verifyResponse === false) {

      header('Location: /client/auth/login?error=network_error');
      exit;
    }

    $responseData = json_decode($verifyResponse);

    if (!$responseData || !$responseData->success) {

      header('Location: /client/auth/login?error=captcha_failed');
      exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header('Location: /client/auth/login?error=invalid_email');
      exit;
    }

    if (empty(trim($password))) {
      header('Location: /client/auth/login?error=empty_password');
      exit;
    }

    $khachHang = new \KhachHang();

    $emailSafe = addslashes(trim($email));
    $checkUser = $khachHang->query("SELECT trang_thai FROM nguoi_dung WHERE email = '$emailSafe' LIMIT 1");

    if (!empty($checkUser)) {
      $trangThai = $checkUser[0]['trang_thai'];

      if ($trangThai === 'BLOCKED') {
        header('Location: /client/auth/login?error=account_blocked');
        exit;
      }

    }
    if ($khachHang->dang_nhap($email, $password)) {
      Session::start();

      $userData = [
        'id' => $khachHang->getId(),
        'email' => $khachHang->getEmail(),
        'ho_ten' => $khachHang->getHoTen(),
        'loai_tai_khoan' => 'MEMBER',
        'avatar_url' => $khachHang->getAvatarUrl()
      ];

      Session::login($userData);

      header('Location: /client/profile');
      exit;
    }

    header('Location: /client/auth/login?error=invalid_credentials');
    exit;
  }

  public static function register(string $email, string $password, string $name): bool
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header('Location: /client/auth/register?error=invalid_email');
      exit;
    }

    if (empty(trim($password))) {
      header('Location: /client/auth/register?error=empty_password');
      exit;
    }

    if (empty(trim($name))) {
      header('Location: /client/auth/register?error=empty_name');
      exit;
    }

    $khachHang = new \KhachHang();
    $result = $khachHang->dang_ky($email, $password, $name);

    if ($result === null) {
      header('Location: /client/auth/register?error=email_exists');
      exit;
    }

    if ($result) {
      $token = $result['token'];

      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
      $host = $_SERVER['HTTP_HOST'] ?? 'localhost:3000';
      $verifyUrl = $scheme . '://' . $host . '/client/auth/verify-email?token=' . $token;

      $nameSafe = htmlspecialchars($name);
      $emailContent = "<!doctype html>
<html lang=\"vi\">
  <head>
    <meta charset=\"UTF-8\" />
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
    <title>Xác thực tài khoản FPT Shop</title>
  </head>
  <body
    style=\"
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      font-family: 'Roboto', Arial, sans-serif;
    \"
  >
    <table
      width=\"100%\"
      cellpadding=\"0\"
      cellspacing=\"0\"
      style=\"background-color: #f4f4f4; padding: 40px 0\"
    >
      <tr>
        <td align=\"center\">
          <table
            width=\"600\"
            cellpadding=\"0\"
            cellspacing=\"0\"
            style=\"
              max-width: 600px;
              width: 100%;
              background-color: #ffffff;
              border: 1px solid #e0e0e0;
            \"
          >
            <tr>
              <td style=\"padding: 40px 40px 30px\">
                <h2
                  style=\"
                    margin: 0 0 20px;
                    font-size: 18px;
                    color: #333333;
                    font-weight: 600;
                  \"
                >
                  Xác thực tài khoản của bạn
                </h2>

                <p style=\"margin: 0 0 16px; font-size: 15px; color: #333333\">
                  Xin chào <strong>$nameSafe</strong>,
                </p>

                <p
                  style=\"
                    margin: 0 0 24px;
                    font-size: 15px;
                    color: #555555;
                    line-height: 1.6;
                  \"
                >
                  Cảm ơn bạn đã đăng ký tài khoản tại FPT Shop. Vui lòng nhấn
                  vào nút bên dưới để xác thực địa chỉ email và hoàn tất quá
                  trình đăng ký:
                </p>

                <table
                  cellpadding=\"0\"
                  cellspacing=\"0\"
                  style=\"margin: 0 auto 30px\"
                >
                  <tr>
                    <td
                      align=\"center\"
                      bgcolor=\"#cb1c22\"
                      style=\"border-radius: 4px\"
                    >
                      <a
                        href=\"$verifyUrl\"
                        target=\"_blank\"
                        style=\"
                          display: inline-block;
                          padding: 12px 24px;
                          color: #ffffff;
                          font-size: 15px;
                          font-weight: 600;
                          text-decoration: none;
                          border: 1px solid #cb1c22;
                          border-radius: 4px;
                        \"
                      >
                        Xác thực tài khoản
                      </a>
                    </td>
                  </tr>
                </table>

                <p style=\"margin: 0 0 8px; font-size: 13px; color: #666666\">
                  Nếu nút trên không hoạt động, bạn có thể copy và dán đường
                  link sau vào trình duyệt:
                </p>
                <p
                  style=\"
                    margin: 0 0 24px;
                    font-size: 13px;
                    word-break: break-all;
                  \"
                >
                  <a
                    href=\"$verifyUrl\"
                    style=\"color: #cb1c22; text-decoration: none\"
                    >$verifyUrl</a
                  >
                </p>
              </td>
            </tr>

            <tr>
              <td
                style=\"
                  background-color: #f8f9fa;
                  border-top: 1px solid #eeeeee;
                  padding: 20px 40px;
                  text-align: center;
                \"
              >
                <p style=\"margin: 0 0 8px; font-size: 12px; color: #888888\">
                  © 2024 FPT Shop. Tất cả quyền được bảo lưu.
                </p>
                <p style=\"margin: 0; font-size: 12px; color: #aaaaaa\">
                  Email này được gửi tự động, vui lòng không trả lời.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>";

      $mailSent = sendMail($email, 'Xác thực tài khoản FPT Shop của bạn', $emailContent);

      if ($mailSent) {
        //kiểm tra mail
        header('Location: /client/auth/check-email?email=' . urlencode($email));
        exit;
      } else {
        //gửi mail thất bại
        header('Location: /client/auth/register?error=mail_failed');
        exit;
      }
    }

    //lỗi
    header('Location: /client/auth/register?error=registration_failed');
    exit;
  }

  public static function verifyEmail(string $token): void
  {
    if (empty(trim($token))) {
      header('Location: /client/auth/register?error=invalid_token');
      exit;
    }

    $khachHang = new \KhachHang();
    $verified = $khachHang->xac_thuc_email($token);

    if (!$verified) {
      header('Location: /client/auth/verify-failed');
      exit;
    }

    //xác thực thành công
    Session::start();
    $userData = [
      'id' => $khachHang->getId(),
      'email' => $khachHang->getEmail(),
      'ho_ten' => $khachHang->getHoTen(),
      'loai_tai_khoan' => 'MEMBER',
      'avatar_url' => $khachHang->getAvatarUrl()
    ];
    Session::login($userData);

    header('Location: /client/auth/verified');
    exit;
  }

  public static function logout(): void
  {
    Session::start();
    Session::logout();

    header('Location: /client/auth/login');
    exit;
  }

  public static function requestPasswordReset(string $email): void
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header('Location: /client/auth/forgot-password?error=invalid_email');
      exit;
    }

    $khachHang = new \KhachHang();
    $token = $khachHang->tao_reset_token($email);

    if ($token !== null) {
      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
      $host = $_SERVER['HTTP_HOST'] ?? 'localhost:3000';
      $resetUrl = $scheme . '://' . $host . '/client/auth/reset-password?token=' . $token;

      $emailSafe = htmlspecialchars($email);
      $emailContent = "<!doctype html>
<html lang=\"vi\">
  <head>
    <meta charset=\"UTF-8\" />
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
    <title>Đặt lại mật khẩu FPT Shop</title>
  </head>
  <body
    style=\"
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      font-family: 'Roboto', Arial, sans-serif;
    \"
  >
    <table
      width=\"100%\"
      cellpadding=\"0\"
      cellspacing=\"0\"
      style=\"background-color: #f4f4f4; padding: 40px 0\"
    >
      <tr>
        <td align=\"center\">
          <table
            width=\"600\"
            cellpadding=\"0\"
            cellspacing=\"0\"
            style=\"
              max-width: 600px;
              width: 100%;
              background-color: #ffffff;
              border: 1px solid #e0e0e0;
            \"
          >
            <tr>
              <td style=\"padding: 40px 40px 30px\">
                <h2
                  style=\"
                    margin: 0 0 20px;
                    font-size: 18px;
                    color: #333333;
                    font-weight: 600;
                  \"
                >
                  Đặt lại mật khẩu của bạn
                </h2>

                <p style=\"margin: 0 0 16px; font-size: 15px; color: #333333\">
                  Xin chào <strong>$emailSafe</strong>,
                </p>

                <p
                  style=\"
                    margin: 0 0 24px;
                    font-size: 15px;
                    color: #555555;
                    line-height: 1.6;
                  \"
                >
                  Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại FPT Shop. 
                  Vui lòng nhấn vào nút bên dưới để đặt lại mật khẩu:
                </p>

                <table
                  cellpadding=\"0\"
                  cellspacing=\"0\"
                  style=\"margin: 0 auto 30px\"
                >
                  <tr>
                    <td
                      align=\"center\"
                      bgcolor=\"#cb1c22\"
                      style=\"border-radius: 4px\"
                    >
                      <a
                        href=\"$resetUrl\"
                        target=\"_blank\"
                        style=\"
                          display: inline-block;
                          padding: 12px 24px;
                          color: #ffffff;
                          font-size: 15px;
                          font-weight: 600;
                          text-decoration: none;
                          border: 1px solid #cb1c22;
                          border-radius: 4px;
                        \"
                      >
                        Đặt lại mật khẩu
                      </a>
                    </td>
                  </tr>
                </table>

                <p style=\"margin: 0 0 8px; font-size: 13px; color: #666666\">
                  Nếu nút trên không hoạt động, bạn có thể copy và dán đường
                  link sau vào trình duyệt:
                </p>
                <p
                  style=\"
                    margin: 0 0 24px;
                    font-size: 13px;
                    word-break: break-all;
                  \"
                >
                  <a
                    href=\"$resetUrl\"
                    style=\"color: #cb1c22; text-decoration: none\"
                    >$resetUrl</a
                  >
                </p>

                <p style=\"margin: 0 0 8px; font-size: 13px; color: #888888\">
                  Link này sẽ hết hạn sau 24 giờ.
                </p>

                <p style=\"margin: 0; font-size: 13px; color: #888888\">
                  Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
                </p>
              </td>
            </tr>

            <tr>
              <td
                style=\"
                  background-color: #f8f9fa;
                  border-top: 1px solid #eeeeee;
                  padding: 20px 40px;
                  text-align: center;
                \"
              >
                <p style=\"margin: 0 0 8px; font-size: 12px; color: #888888\">
                  © 2024 FPT Shop. Tất cả quyền được bảo lưu.
                </p>
                <p style=\"margin: 0; font-size: 12px; color: #aaaaaa\">
                  Email này được gửi tự động, vui lòng không trả lời.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>";

      sendMail($email, 'Đặt lại mật khẩu FPT Shop của bạn', $emailContent);
    }

    header('Location: /client/auth/check-email?email=' . urlencode($email) . '&type=password_reset');
    exit;
  }

  public static function verifyResetToken(string $token): void
  {
    if (empty(trim($token))) {
      header('Location: /client/auth/forgot-password?error=invalid_token');
      exit;
    }

    $khachHang = new \KhachHang();

    $userData = $khachHang->xac_thuc_reset_token($token);

    if ($userData !== false) {
      require_once __DIR__ . '/../../views/client/auth/reset_password.php';
      return;
    }

    $khachHangCheck = new \KhachHang();

    if (!preg_match('/^[0-9a-f]{64}$/i', $token)) {
      header('Location: /client/auth/forgot-password?error=invalid_token');
      exit;
    }

    $escapedToken = addslashes($token);
    $tokenCheck = $khachHangCheck->query("SELECT forget_token_created_at FROM nguoi_dung WHERE forget_token = '$escapedToken' LIMIT 1");

    if (empty($tokenCheck)) {
      header('Location: /client/auth/forgot-password?error=invalid_token');
      exit;
    }

    header('Location: /client/auth/forgot-password?error=expired_token');
    exit;
  }

  public static function resetPassword(string $token, string $newPassword, string $confirmPassword): void
  {
    if (empty(trim($newPassword))) {
      header('Location: /client/auth/reset-password?token=' . urlencode($token) . '&error=empty_password');
      exit;
    }

    if (strlen(trim($newPassword)) < 6) {
      header('Location: /client/auth/reset-password?token=' . urlencode($token) . '&error=password_too_short');
      exit;
    }

    if ($newPassword !== $confirmPassword) {
      header('Location: /client/auth/reset-password?token=' . urlencode($token) . '&error=password_mismatch');
      exit;
    }

    $khachHang = new \KhachHang();
    $result = $khachHang->dat_lai_mat_khau($token, $newPassword);

    if (!$result) {
      header('Location: /client/auth/reset-password?token=' . urlencode($token) . '&error=update_failed');
      exit;
    }

    header('Location: /client/auth/reset-success');
    exit;
  }
}