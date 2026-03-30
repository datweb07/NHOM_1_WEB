<?php

namespace App\Controllers\Client;

require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../../models/roles/KhachHang.php';

use App\Core\Session;

class AuthController
{
    public static function login(string $email, string $password): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /client/auth/login?error=invalid_email');
            exit;
        }

        if (empty(trim($password))) {
            header('Location: /client/auth/login?error=empty_password');
            exit;
        }

        //check database
        $khachHang = new \KhachHang();
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

        //lỗi
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

        //thực hiện register
        $khachHang = new \KhachHang();
        $result = $khachHang->dang_ky($email, $password, $name);

        if ($result === null) {
            header('Location: /client/auth/register?error=email_exists');
            exit;
        }

        if ($result) {
            $token = $result['token'];

            //lấy url
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost:3000';
            $verifyUrl = $scheme . '://' . $host . '/client/auth/verify-email?token=' . $token;

            //email content
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
      font-family:
        -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Arial,
        sans-serif;
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
}
