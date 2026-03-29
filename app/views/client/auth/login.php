<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản - FPT Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/public/assets/client/images/header/1.png">
    <style>
        :root {
            --fpt-red: #cb1c22;
            --fpt-red-hover: #a8151b;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5 col-xl-4">
                
                <div class="text-center mb-4">
                    <img src="../../../../images/fpt-shop-banner.png" alt="FPT Shop" style="height: 100px;">
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
                                    'invalid_credentials' => 'Email hoặc mật khẩu không đúng.'
                                ];
                                echo $errorMessages[$_GET['error']] ?? 'Đã có lỗi xảy ra.';
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/client/auth/login">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium" style="font-size: 0.9rem;">Email của bạn</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="Ví dụ: example@gmail.com"
                                    required
                                >
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label fw-medium mb-0" style="font-size: 0.9rem;">Mật khẩu</label>
                                    <a href="/client/auth/forgot-password" class="text-decoration-none text-brand" style="font-size: 0.85rem;">Quên mật khẩu?</a>
                                </div>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Nhập mật khẩu"
                                    required
                                >
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-brand btn-lg" style="font-size: 1rem;">Đăng nhập</button>
                            </div>

                            <div class="text-center mt-3" style="font-size: 0.95rem;">
                                <span class="text-muted">Bạn chưa có tài khoản?</span> 
                                <a href="/client/auth/register" class="text-decoration-none text-brand fw-medium">Đăng ký ngay</a>
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