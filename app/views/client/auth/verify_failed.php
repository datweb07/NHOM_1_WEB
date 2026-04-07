<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Xác thực thất bại - FPT Shop</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />

    <link rel="icon" href="<?= ASSET_URL ?>/assets/client/images/header/1.png" />

    <style>
      :root {
        --fpt-red: #cb1c22;
        --fpt-red-hover: #a8151b;
      }

      body {
        background-color: #f8f9fa;
        font-family:
          -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
          "Helvetica Neue", Arial, sans-serif;
      }

      .custom-card {
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

      .btn-outline-custom {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6;
        font-weight: 500;
        padding: 10px 20px;
      }

      .btn-outline-custom:hover {
        color: #212529;
        background-color: #f8f9fa;
        border-color: #c1c9d0;
      }

      .icon-circle-error {
        width: 80px;
        height: 80px;
        color: var(--fpt-red);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto;
      }

      .text-brand {
        color: var(--fpt-red);
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div
        class="row justify-content-center align-items-center min-vh-100 py-5"
      >
        <div class="col-md-7 col-lg-6 col-xl-5">
          <div class="text-center mb-4">
            <img
              src="<?= ASSET_URL ?>/assets/client/images/others/fpt-shop-banner.png"
              alt="FPT Shop"
              style="height: 100px"
            />
          </div>

          <div class="card custom-card">
            <div class="card-body p-4 p-sm-5 text-center">
              <div class="icon-circle-error mb-4">
                <i class="bi bi-x-circle"></i>
              </div>

              <h4 class="fw-bold mb-2">Xác thực thất bại!</h4>
              <p class="text-muted mb-4" style="font-size: 0.95rem">
                Rất tiếc, chúng tôi không thể kích hoạt tài khoản của bạn.
              </p>

              <div class="d-grid gap-3 mb-4">
                <a
                  href="/client/auth/register"
                  class="btn btn-primary-brand btn-lg"
                  style="font-size: 1rem"
                >
                  Đăng ký lại
                </a>
                <a
                  href="/client/auth/login"
                  class="btn btn-outline-custom btn-lg"
                  style="font-size: 1rem"
                >
                  Đăng nhập
                </a>
              </div>

              <div
                style="font-size: 0.9rem"
                class="text-muted border-top pt-4 mt-2"
              >
                Cần hỗ trợ?
                <a
                  href="mailto:dat82770@gmail.com"
                  class="text-brand text-decoration-none fw-semibold"
                  >Liên hệ với chúng tôi</a
                >
              </div>
            </div>
          </div>

          <div class="text-center mt-4">
            <a
              href="/"
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
  </body>
</html>
