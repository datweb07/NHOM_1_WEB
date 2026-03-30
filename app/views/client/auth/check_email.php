<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra email - FPT Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
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

        .custom-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .text-brand {
            color: var(--fpt-red);
        }

        a.text-brand:hover {
            color: var(--fpt-red-hover);
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

        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: rgba(203, 28, 34, 0.08); 
            color: var(--fpt-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-7 col-lg-6 col-xl-5">
                
                <div class="text-center mb-4">
                    <img src="../../../../images/fpt-shop-banner.png" alt="FPT Shop" style="height: 100px;">
                </div>

                <div class="card custom-card">
                    <div class="card-body p-4 p-sm-5">
                        
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-2">Kiểm tra hộp thư của bạn!</h4>
                            <p class="text-muted" style="font-size: 0.95rem;">
                                Chúng tôi đã gửi email xác thực đến:<br>
                                <span class="mt-2 d-inline-block"><?= htmlspecialchars($_GET['email'] ?? 'email_cua_ban@example.com') ?></span>
                            </p>
                        </div>

                        <div class="d-grid mb-3">
                            <a href="/client/auth/login" class="btn btn-primary-brand btn-lg" style="font-size: 1rem;">Đến trang Đăng nhập</a>
                        </div>
                        
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