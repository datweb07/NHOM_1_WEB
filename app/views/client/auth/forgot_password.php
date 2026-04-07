<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - FPT Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png">
    <style>
        :root {
            --fpt-red: #cb1c22;
            --fpt-red-hover: #a8151b;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .forgot-card {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5 col-xl-4">
                
                <div class="text-center mb-4">
                    <img src="<?= ASSET_URL ?>/assets/client/images/others/fpt-shop-banner.png" alt="FPT Shop" style="height: 100px;">
                </div>

                <div class="card forgot-card">
                    <div class="card-body p-4 p-sm-5">
                        <div class="mb-4">
                            <h4 class="fw-bold mb-1">Quên mật khẩu?</h4>
                            <p class="text-muted" style="font-size: 0.9rem;">Nhập email của bạn để nhận link đặt lại mật khẩu</p>
                        </div>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger py-2" role="alert" style="font-size: 0.9rem;">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?php
                                $errorMessages = [
                                    'invalid_email' => 'Email không hợp lệ.',
                                    'invalid_token' => 'Link đặt lại mật khẩu không hợp lệ.',
                                    'expired_token' => 'Link đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu link mới.'
                                ];
                                echo $errorMessages[$_GET['error']] ?? 'Đã có lỗi xảy ra.';
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/client/auth/forgot-password">
                            <div class="mb-4">
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

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-brand btn-lg" style="font-size: 1rem;">Gửi link đặt lại mật khẩu</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="/client/auth/login" class="text-decoration-none text-muted" style="font-size: 0.9rem;">
                        &larr; Quay về trang đăng nhập
                    </a>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
