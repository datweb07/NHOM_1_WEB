<?php

function clientRoute(string $uri): void
{
	$path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

	if ($path === '' || $path === 'index.php') {
		require_once dirname(__DIR__, 2) . '/controllers/client/HomeController.php';
		$controller = new \App\Controllers\Client\HomeController();
		$controller->index();
		return;
	}

	if ($path === 'san-pham' || $path === 'san-pham/list') {
		require_once dirname(__DIR__, 2) . '/controllers/client/SanPhamController.php';
		$controller = new \App\Controllers\Client\SanPhamController();
		$controller->danhSach();
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

	// Giỏ hàng routes
	if ($path === 'gio-hang') {
		require_once dirname(__DIR__, 2) . '/controllers/client/GioHangController.php';
		$controller = new \App\Controllers\Client\GioHangController();
		$controller->index();
		return;
	}

	if ($path === 'gio-hang/them') {
		require_once dirname(__DIR__, 2) . '/controllers/client/GioHangController.php';
		$controller = new \App\Controllers\Client\GioHangController();
		$controller->them();
		return;
	}

	if ($path === 'gio-hang/cap-nhat') {
		require_once dirname(__DIR__, 2) . '/controllers/client/GioHangController.php';
		$controller = new \App\Controllers\Client\GioHangController();
		$controller->capNhat();
		return;
	}

	if ($path === 'gio-hang/xoa') {
		require_once dirname(__DIR__, 2) . '/controllers/client/GioHangController.php';
		$controller = new \App\Controllers\Client\GioHangController();
		$controller->xoa();
		return;
	}

	if ($path === 'gio-hang/dem-san-pham') {
		require_once dirname(__DIR__, 2) . '/controllers/client/GioHangController.php';
		$controller = new \App\Controllers\Client\GioHangController();
		$controller->demSanPham();
		return;
	}

	// Sản phẩm routes
	if (preg_match('#^san-pham/([a-z0-9-]+)$#', $path, $matches)) {
		require_once dirname(__DIR__, 2) . '/controllers/client/SanPhamController.php';
		$controller = new \App\Controllers\Client\SanPhamController();
		$controller->chiTiet($matches[1]);
		return;
	}

	// Thanh toán routes
	if ($path === 'thanh-toan') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->index();
		return;
	}

	if ($path === 'thanh-toan/dat-hang') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->datHang();
		return;
	}

	if ($path === 'thanh-toan/kiem-tra-ma-giam-gia') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->kiemTraMaGiamGia();
		return;
	}

	// Đơn hàng routes
	if ($path === 'don-hang/huy') {
		require_once dirname(__DIR__, 2) . '/controllers/client/DonHangController.php';
		$controller = new \App\Controllers\Client\DonHangController();
		$controller->huy();
		return;
	}

	if (preg_match('#^don-hang/(\d+)$#', $path, $matches)) {
		require_once dirname(__DIR__, 2) . '/controllers/client/DonHangController.php';
		$controller = new \App\Controllers\Client\DonHangController();
		$controller->chiTiet((int)$matches[1]);
		return;
	}

	if ($path === 'don-hang') {
		require_once dirname(__DIR__, 2) . '/controllers/client/DonHangController.php';
		$controller = new \App\Controllers\Client\DonHangController();
		$controller->danhSach();
		return;
	}

	// Tìm kiếm routes
	if ($path === 'tim-kiem') {
		require_once dirname(__DIR__, 2) . '/controllers/client/TimKiemController.php';
		$controller = new \App\Controllers\Client\TimKiemController();
		$controller->timKiem();
		return;
	}

	if ($path === 'tim-kiem/lich-su') {
		require_once dirname(__DIR__, 2) . '/controllers/client/TimKiemController.php';
		$controller = new \App\Controllers\Client\TimKiemController();
		$controller->layLichSu();
		return;
	}

	if ($path === 'tim-kiem/xoa-lich-su') {
		require_once dirname(__DIR__, 2) . '/controllers/client/TimKiemController.php';
		$controller = new \App\Controllers\Client\TimKiemController();
		$controller->xoaLichSu();
		return;
	}

	if ($path === 'tim-kiem/tu-khoa-pho-bien') {
		require_once dirname(__DIR__, 2) . '/controllers/client/TimKiemController.php';
		$controller = new \App\Controllers\Client\TimKiemController();
		$controller->layTuKhoaPhoBien();
		return;
	}

	// Yêu thích routes
	if ($path === 'yeu-thich') {
		require_once dirname(__DIR__, 2) . '/controllers/client/YeuThichController.php';
		$controller = new \App\Controllers\Client\YeuThichController();
		$controller->index();
		return;
	}

	if ($path === 'yeu-thich/them') {
		require_once dirname(__DIR__, 2) . '/controllers/client/YeuThichController.php';
		$controller = new \App\Controllers\Client\YeuThichController();
		$controller->them();
		return;
	}

	if ($path === 'yeu-thich/xoa') {
		require_once dirname(__DIR__, 2) . '/controllers/client/YeuThichController.php';
		$controller = new \App\Controllers\Client\YeuThichController();
		$controller->xoa();
		return;
	}

	if ($path === 'yeu-thich/kiem-tra') {
		require_once dirname(__DIR__, 2) . '/controllers/client/YeuThichController.php';
		$controller = new \App\Controllers\Client\YeuThichController();
		$controller->kiemTra();
		return;
	}

	if ($path === 'yeu-thich/dem') {
		require_once dirname(__DIR__, 2) . '/controllers/client/YeuThichController.php';
		$controller = new \App\Controllers\Client\YeuThichController();
		$controller->dem();
		return;
	}

	// Đánh giá routes
	if ($path === 'danh-gia/danh-sach') {
		require_once dirname(__DIR__, 2) . '/controllers/client/DanhGiaController.php';
		$controller = new \App\Controllers\Client\DanhGiaController();
		$controller->layDanhSach();
		return;
	}

	if ($path === 'danh-gia/them') {
		require_once dirname(__DIR__, 2) . '/controllers/client/DanhGiaController.php';
		$controller = new \App\Controllers\Client\DanhGiaController();
		$controller->them();
		return;
	}

	if ($path === 'danh-gia/kiem-tra') {
		require_once dirname(__DIR__, 2) . '/controllers/client/DanhGiaController.php';
		$controller = new \App\Controllers\Client\DanhGiaController();
		$controller->kiemTra();
		return;
	}

	// Khuyến mãi routes
	if ($path === 'khuyen-mai') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhuyenMaiController.php';
		$controller = new \App\Controllers\Client\KhuyenMaiController();
		$controller->danhSachKhuyenMai();
		return;
	}

	if ($path === 'khuyen-mai/chi-tiet') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhuyenMaiController.php';
		$controller = new \App\Controllers\Client\KhuyenMaiController();
		$controller->chiTietKhuyenMai();
		return;
	}

	if ($path === 'ma-giam-gia') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhuyenMaiController.php';
		$controller = new \App\Controllers\Client\KhuyenMaiController();
		$controller->danhSachMaGiamGia();
		return;
	}

	require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
}

