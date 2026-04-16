<?php

require_once __DIR__ . '/../../../core/EnvSetup.php';
$envConfig = \EnvSetup::env(dirname(__DIR__, 4));
$recaptchaSiteKey = $envConfig('RECAPTCHA_SITE_KEY', '');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản - FPT Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        :root {
            --fpt-red: #cb1c22;
            --fpt-red-hover: #a8151b;
        }

        body {
            background-color: #f8f9fa;
        }

        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .btn-primary-brand {
            background-color: var(--fpt-red);
            border-color: var(--fpt-red);
            color: white;
            font-weight: 500;
            padding: 10px 20px;
        }

        .btn-primary-brand:hover,
        .btn-primary-brand:focus {
            background-color: var(--fpt-red-hover);
            border-color: var(--fpt-red-hover);
            color: white;
        }

        .form-control {
            padding: 10px 15px;
            border-color: #ced4da;
        }

        .form-control:focus {
            border-color: var(--fpt-red);
            box-shadow: 0 0 0 0.25rem rgba(203, 28, 34, 0.15);
        }

        .text-brand {
            color: var(--fpt-red);
        }

        a.text-brand:hover {
            color: var(--fpt-red-hover);
        }

        .divider-text {
            position: relative;
            text-align: center;
            margin: 24px 0;
        }

        .divider-text::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px solid #dee2e6;
            z-index: 1;
        }

        .divider-text span {
            background-color: #fff;
            padding: 0 15px;
            color: #6c757d;
            font-size: 0.85rem;
            position: relative;
            z-index: 2;
        }

        /* Căn giữa reCAPTCHA */
        .recaptcha-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5 col-xl-4">

                <div class="text-center mb-4">
                    <img src="<?= ASSET_URL ?>/assets/client/images/others/fpt-shop-banner.png" alt="FPT Shop"
                        style="height: 100px;">
                </div>

                <div class="card login-card">
                    <div class="card-body p-4 p-sm-5">
                        <div class="mb-4">
                            <h4 class="fw-bold mb-1">Đăng nhập</h4>
                            <p class="text-muted" style="font-size: 0.9rem;">Chào mừng bạn quay trở lại FPT Shop</p>
                        </div>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger py-2" role="alert" style="font-size: 0.9rem;">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?php
                                $errorMessages = [
                                    'invalid_email' => 'Email không hợp lệ.',
                                    'empty_password' => 'Vui lòng nhập mật khẩu.',
                                    'invalid_credentials' => 'Email hoặc mật khẩu không đúng.',
                                    'account_blocked' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin để được hỗ trợ.',
                                    'captcha_failed' => 'Xác thực reCAPTCHA thất bại. Vui lòng thử lại.',
                                    'captcha_missing' => 'Vui lòng xác nhận bạn không phải là robot.', 
                                    'oauth_failed' => 'Bạn đã từ chối cấp quyền đăng nhập.',
                                    'no_token' => 'Đăng nhập thất bại, vui lòng thử lại.',
                                    'invalid_token' => 'Phiên đăng nhập không hợp lệ.',
                                    'missing_user_data' => 'Không thể lấy thông tin từ Google.',
                                    'create_user_failed' => 'Không thể tạo tài khoản, vui lòng thử lại.',
                                    'network_error' => 'Lỗi kết nối, vui lòng kiểm tra internet.'
                                ];
                                echo $errorMessages[$_GET['error']] ?? 'Đã có lỗi xảy ra.';
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/client/auth/login" id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium" style="font-size: 0.9rem;">Email của
                                    bạn</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Ví dụ: example@gmail.com" required>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label fw-medium mb-0"
                                        style="font-size: 0.9rem;">Mật khẩu</label>
                                    <a href="/client/auth/forgot-password" class="text-decoration-none text-brand"
                                        style="font-size: 0.85rem;">Quên mật khẩu?</a>
                                </div>
                                <div class="position-relative">
                                    <input type="password" class="form-control pe-5" id="password" name="password"
                                        placeholder="Nhập mật khẩu" required>
                                    <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 toggle-password"
                                        style="cursor: pointer; color: #6c757d;"></i>
                                </div>
                            </div>

                            <div class="recaptcha-wrapper">
                                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey) ?>">
                                </div>
                            </div>
                            <div id="captchaError" class="text-danger small text-center mb-3 d-none">
                                Vui lòng xác nhận bạn không phải là robot.
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-brand btn-lg" style="font-size: 1rem;">Đăng
                                    nhập</button>
                            </div>

                            <div class="divider-text">
                                <span>Hoặc đăng nhập với</span>
                            </div>

                            <?php
                            require_once __DIR__ . '/../../../services/supabase/SupabaseService.php';
                            $googleLoginUrl = SupabaseAuthService::getGoogleLoginUrl();
                            ?>

                            <div class="d-grid mb-3">
                                <a href="<?= $googleLoginUrl ?>"
                                    class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center gap-2"
                                    style="font-size: 1rem;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                            fill="#4285F4" />
                                        <path
                                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                            fill="#34A853" />
                                        <path
                                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                            fill="#FBBC05" />
                                        <path
                                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                            fill="#EA4335" />
                                    </svg>
                                    Đăng nhập với Google
                                </a>
                            </div>

                            <div class="text-center mt-3" style="font-size: 0.95rem;">
                                <span class="text-muted">Bạn chưa có tài khoản?</span>
                                <a href="/client/auth/register" class="text-decoration-none text-brand fw-medium">Đăng
                                    ký ngay</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="/" class="text-decoration-none text-muted" style="font-size: 0.9rem;">
                        &larr; Quay về trang chủ
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {

                event.preventDefault();

                document.getElementById('captchaError').classList.remove('d-none');
            } else {
                document.getElementById('captchaError').classList.add('d-none');
            }
        });

        document.querySelectorAll('.toggle-password').forEach(function(icon) {
        icon.addEventListener('click', function() {

            let input = this.previousElementSibling; 
            
            if (input.type === "password") {
                input.type = "text";
                this.classList.remove('bi-eye-slash');
                this.classList.add('bi-eye');
                this.style.color = '#cb1c22'; 
            } else {
                input.type = "password";
                this.classList.remove('bi-eye');
                this.classList.add('bi-eye-slash');
                this.style.color = '#6c757d'; 
            }
        });
    });
    </script>
</body>

</html>