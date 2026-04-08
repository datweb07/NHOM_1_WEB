<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đặt lại mật khẩu thành công - FPT Shop</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png" />
    <style>
      :root {
        --fpt-red: #cb1c22;
        --fpt-red-hover: #a8151b;
      }

      body {
        background-color: #f8f9fa;
      }

      .success-card {
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

      .success-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
      }

      .success-icon svg {
        width: 40px;
        height: 40px;
        color: #28a745;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div
        class="row justify-content-center align-items-center min-vh-100 py-5"
      >
        <div class="col-md-6 col-lg-5 col-xl-4">
          <div class="text-center mb-4">
            <img
              src="<?= ASSET_URL ?>/assets/client/images/others/fpt-shop-banner.png"
              alt="FPT Shop"
              style="height: 100px"
            />
          </div>

          <div class="card success-card">
            <div class="card-body p-4 p-sm-5 text-center">
              <div class="success-icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </div>

              <h4 class="fw-bold mb-3">Đặt lại mật khẩu thành công!</h4>

              <div class="py-2 mb-4" role="alert" style="font-size: 0.9rem">
                <i class="bi bi-info-circle me-1"></i>
                Bạn sẽ được chuyển đến trang đăng nhập sau
                <span id="countdown">3</span> giây...
              </div>

              <div class="d-grid">
                <a
                  href="/client/auth/login"
                  class="btn btn-primary-brand btn-lg"
                  style="font-size: 1rem"
                >
                  Đăng nhập ngay
                </a>
              </div>
            </div>
          </div>

          <div class="text-center mt-4">
            <a
              href="/client/home"
              class="text-decoration-none text-muted"
              style="font-size: 0.9rem"
            >
              &larr; Quay về trang chủ
            </a>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Auto redirect after 3 seconds
      let countdown = 3;
      const countdownElement = document.getElementById("countdown");

      const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;

        if (countdown <= 0) {
          clearInterval(timer);
          window.location.href = "/client/auth/login";
        }
      }, 1000);
    </script>
  </body>
</html>
