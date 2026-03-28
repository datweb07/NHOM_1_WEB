<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - FPT Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/public/assets/client/images/header/1.png">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Đăng nhập</h3>
                            <p class="text-muted">Chào mừng bạn quay trở lại</p>
                        </div>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php
                                $errorMessages = [
                                    'invalid_email' => 'Email không hợp lệ',
                                    'empty_password' => 'Vui lòng nhập mật khẩu',
                                    'invalid_credentials' => 'Email hoặc mật khẩu không đúng'
                                ];
                                echo $errorMessages[$_GET['error']] ?? 'Đã có lỗi xảy ra';
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/client/auth/login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="Nhập email của bạn"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
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
                                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">
                                    Chưa có tài khoản? 
                                    <a href="/client/auth/register" class="text-decoration-none">Đăng ký ngay</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="/" class="text-decoration-none text-muted">
                        <small>← Quay về trang chủ</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
