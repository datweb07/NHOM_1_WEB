<?php

function clientRoute(string $uri): void
{
	$path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

	if ($path === '' || $path === 'index.php') {
		require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
		return;
	}

	if ($path === 'san-pham' || $path === 'san-pham/list') {
		require_once dirname(__DIR__, 2) . '/views/client/san_pham/list.php';
		return;
	}

	if ($path === 'san-pham/chi-tiet' || $path === 'san-pham/detail') {
		require_once dirname(__DIR__, 2) . '/views/client/san_pham/detail.php';
		return;
	}

	//auth routes
	if ($path === 'client/auth/login') {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			require_once dirname(__DIR__, 2) . '/controllers/client/AuthController.php';
			\App\Controllers\Client\AuthController::login($_POST['email'] ?? '', $_POST['password'] ?? '');
			return;
		}
		require_once dirname(__DIR__, 2) . '/views/client/auth/login.php';
		return;
	}

	if ($path === 'client/auth/register') {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			require_once dirname(__DIR__, 2) . '/controllers/client/AuthController.php';
			\App\Controllers\Client\AuthController::register($_POST['email'] ?? '', $_POST['password'] ?? '', $_POST['name'] ?? '');
			return;
		}
		require_once dirname(__DIR__, 2) . '/views/client/auth/register.php';
		return;
	}

	//trang kiểm tra mail
	if ($path === 'client/auth/check-email') {
		require_once dirname(__DIR__, 2) . '/views/client/auth/check_email.php';
		return;
	}

	// Route xử lý link xác thực từ email
	if ($path === 'client/auth/verify-email') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AuthController.php';
		\App\Controllers\Client\AuthController::verifyEmail($_GET['token'] ?? '');
		return;
	}

	//xác thực thành công
	if ($path === 'client/auth/verified') {
		require_once dirname(__DIR__, 2) . '/views/client/auth/verified.php';
		return;
	}

	//xác thực thất bại
	if ($path === 'client/auth/verify-failed') {
		require_once dirname(__DIR__, 2) . '/views/client/auth/verify_failed.php';
		return;
	}

	//quên mật khẩu
	if ($path === 'client/auth/forgot-password') {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			require_once dirname(__DIR__, 2) . '/controllers/client/AuthController.php';
			\App\Controllers\Client\AuthController::requestPasswordReset($_POST['email'] ?? '');
			return;
		}
		require_once dirname(__DIR__, 2) . '/views/client/auth/forgot_password.php';
		return;
	}

	//new password
	if ($path === 'client/auth/reset-password') {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			require_once dirname(__DIR__, 2) . '/controllers/client/AuthController.php';
			\App\Controllers\Client\AuthController::resetPassword(
				$_POST['token'] ?? '',
				$_POST['new_password'] ?? '',
				$_POST['confirm_password'] ?? ''
			);
			return;
		}
		require_once dirname(__DIR__, 2) . '/controllers/client/AuthController.php';
		\App\Controllers\Client\AuthController::verifyResetToken($_GET['token'] ?? '');
		return;
	}

	//đặt lại mk thành công
	if ($path === 'client/auth/reset-success') {
		require_once dirname(__DIR__, 2) . '/views/client/auth/reset_success.php';
		return;
	}

	if ($path === 'client/auth/logout' || $path === 'logout.php') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AuthController.php';
		\App\Controllers\Client\AuthController::logout();
		return;
	}

	if ($path === 'client/profile') {
		require_once dirname(__DIR__, 2) . '/views/client/khach_hang/profile.php';
		return;
	}

	if ($path === 'khach-hang/cap-nhat-ho-so') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
		$controller = new KhachHangController();
		$controller->capNhatHoSo();
		return;
	}

	if ($path === 'khach-hang/doi-mat-khau') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
		$controller = new KhachHangController();
		$controller->doiMatKhau();
		return;
	}

	//cập nhật avatar
	if ($path === 'khach-hang/cap-nhat-avatar') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
		$controller = new KhachHangController();
		$controller->capNhatAvatar();
		return;
	}

	require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
}

