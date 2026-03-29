<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị - FPT Shop</title>
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

        /* Thẻ tag dành riêng cho trang Admin */
        .admin-badge {
            background-color: #343a40; /* Màu xám đen chuyên nghiệp */
            color: white;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 12px;
            text-transform: uppercase;
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
                        
                        <div class="text-center mb-4">
                            <div class="admin-badge fw-bold">Admin Portal</div>
                            <h4 class="fw-bold mb-1">Cổng quản trị nội bộ</h4>
                            <p class="text-muted" style="font-size: 0.9rem;">Vui lòng đăng nhập để tiếp tục</p>
                        </div>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger py-2" role="alert" style="font-size: 0.9rem;">
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

                        <form method="POST" action="/admin/auth/login">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium" style="font-size: 0.9rem;">Email quản trị</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="Ví dụ: admin@fptshop.com.vn"
                                    required
                                >
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium" style="font-size: 0.9rem;">Mật khẩu</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Nhập mật khẩu"
                                    required
                                >
                            </div>

                            <div class="d-grid mb-2">
                                <button type="submit" class="btn btn-primary-brand btn-lg" style="font-size: 1rem;">Đăng nhập hệ thống</button>
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