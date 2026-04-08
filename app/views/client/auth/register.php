<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - FPT Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png">
    <style>
        :root {
            --fpt-red: #cb1c22;
            --fpt-red-hover: #a8151b;
        }

        body {
            background-color: #f8f9fa;
        }

        .auth-card {
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

        .btn-primary-brand:hover, .btn-primary-brand:focus {
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

        .form-check-input:checked {
            background-color: var(--fpt-red);
            border-color: var(--fpt-red);
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

        .btn-social {
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-social:hover {
            background: #f8f9fa;
            border-color: #cdd3d8;
        }

        .social-icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5 col-xl-4">
                
                <div class="text-center mb-4">
                    <img src="<?= ASSET_URL ?>/assets/client/images/others/fpt-shop-banner.png" alt="FPT Shop" style="height: 100px;">
                </div>

                <div class="card auth-card">
                    <div class="card-body p-4 p-sm-5">
                        <div class="mb-4">
                            <h4 class="fw-bold mb-1">Đăng ký</h4>
                            <p class="text-muted" style="font-size: 0.9rem;">Tạo tài khoản FPT Shop mới</p>
                        </div>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger py-2" role="alert" style="font-size: 0.9rem;">
                                <?php
                                $errorMessages = [
                                    'invalid_email' => 'Email không hợp lệ.',
                                    'empty_password' => 'Vui lòng nhập mật khẩu.',
                                    'empty_name' => 'Vui lòng nhập họ tên.',
                                    'email_exists' => 'Email này đã được sử dụng.',
                                    'registration_failed' => 'Đăng ký thất bại, vui lòng thử lại.',
                                    'mail_failed' => 'Đã tạo tài khoản nhưng không thể gửi email xác thực. Vui lòng liên hệ hỗ trợ.',
                                ];
                                echo $errorMessages[$_GET['error']] ?? 'Đã có lỗi xảy ra.';
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/client/auth/register">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium" style="font-size: 0.9rem;">Họ và tên</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="name" 
                                    name="name" 
                                    placeholder="Ví dụ: Nguyễn Văn A"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium" style="font-size: 0.9rem;">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="Ví dụ: email@gmail.com"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium" style="font-size: 0.9rem;">Mật khẩu</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Tạo mật khẩu (ít nhất 6 ký tự)"
                                    required
                                >
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" value="" id="termsCheck" required>
                                <label class="form-check-label text-muted" for="termsCheck" style="font-size: 0.85rem;">
                                    Tôi đồng ý với các <a href="#" class="text-brand text-decoration-none">Điều khoản sử dụng</a> và <a href="#" class="text-brand text-decoration-none">Chính sách bảo mật</a> của FPT Shop.
                                </label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-brand btn-lg" style="font-size: 1rem;">Đăng ký</button>
                            </div>

                            <div class="text-center mt-3" style="font-size: 0.95rem;">
                                <span class="text-muted">Đã có tài khoản?</span> 
                                <a href="/client/auth/login" class="text-decoration-none text-brand fw-medium">Đăng nhập ngay</a>
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
</body>
</html>