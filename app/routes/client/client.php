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

	require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
}

