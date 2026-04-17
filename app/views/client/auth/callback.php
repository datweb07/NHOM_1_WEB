<?php require_once __DIR__ . '/../../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang xử lý đăng nhập... - FPT Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png">

    <style>
        :root {
            --fpt-red: #cb1c22;
            --fpt-red-hover: #a8151b;
        }

        body {
            background-color: #f8f9fa;
        }

        .custom-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .spinner-brand {
            border: 4px solid rgba(203, 28, 34, 0.1);
            border-top: 4px solid var(--fpt-red);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-7 col-lg-6 col-xl-5">

                <div class="text-center mb-4">
                    <img src="<?= ASSET_URL ?>/assets/client/images/others/fpt-shop-banner.png" alt="FPT Shop"
                        style="height: 100px;">
                </div>

                <div class="card custom-card">
                    <div class="card-body p-4 p-sm-5 text-center">

                        <div class="mb-4">
                            <div class="spinner-brand"></div>
                        </div>

                        <h5 class="fw-bold mb-2" id="status-text" style="color: #333;">Đang xử lý đăng nhập...</h5>
                        <p class="text-danger mt-3 fw-medium" id="error-text"
                            style="display: none; font-size: 0.95rem;"></p>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const hash = window.location.hash.substring(1);
        const params = new URLSearchParams(hash);
        const accessToken = params.get('access_token');
        const error = params.get('error');
        const errorDescription = params.get('error_description');

        const statusText = document.getElementById('status-text');
        const errorText = document.getElementById('error-text');

        if (error) {
            console.error('OAuth Error:', error, errorDescription);
            statusText.textContent = 'Đăng nhập thất bại';
            errorText.textContent = errorDescription || 'Đã có lỗi xảy ra';
            errorText.style.display = 'block';

            setTimeout(() => {
                window.location.href = 'login.php?error=oauth_failed&message=' +
                    encodeURIComponent(errorDescription || 'Đăng nhập thất bại');
            }, 2000);
        } else if (accessToken) {
            fetch('/client/auth/process-login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ access_token: accessToken })
            })
                .then(async response => {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error("Lỗi parse JSON. Server trả về:", text);
                        throw new Error("Server không trả về JSON hợp lệ");
                    }
                })
                .then(data => {
                    if (data.success) {
                        statusText.textContent = 'Đăng nhập thành công!';
                        setTimeout(() => {
                            window.location.href = '../../../../index.php';
                        }, 5000); 
                    } else {
                        statusText.textContent = 'Đăng nhập thất bại';
                        errorText.textContent = data.error || 'Không thể xác thực';
                        errorText.style.display = 'block';

                        setTimeout(() => {
                            window.location.href = 'login.php?error=' + encodeURIComponent(data.error);
                        }, 5000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusText.textContent = 'Lỗi kết nối';
                    errorText.textContent = 'Vui lòng kiểm tra kết nối internet';
                    errorText.style.display = 'block';

                    setTimeout(() => {
                        window.location.href = '/client/auth/login?error=network_error';
                    }, 5000);
                });
        } else {
            // không tìm thấy token
            statusText.textContent = 'Không tìm thấy thông tin đăng nhập';
            errorText.textContent = 'Vui lòng thử lại';
            errorText.style.display = 'block';

            setTimeout(() => {
                window.location.href = '/client/auth/login?error=no_token';
            }, 2000);
        }
    </script>
</body>

</html>