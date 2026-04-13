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

	// ĐẶT ROUTE API NÀY LÊN GẦN TRÊN CÙNG
    if ($path === 'api/mega-menu') {
        require_once dirname(__DIR__, 2) . '/controllers/client/SanPhamController.php';
        $controller = new \App\Controllers\Client\SanPhamController();
        $controller->apiMegaMenu();
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

	// Address management routes
	if ($path === 'khach-hang/them-dia-chi') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
		$controller = new KhachHangController();
		$controller->themDiaChi();
		return;
	}

	if ($path === 'khach-hang/cap-nhat-dia-chi') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
		$controller = new KhachHangController();
		$controller->capNhatDiaChi();
		return;
	}

	if ($path === 'khach-hang/xoa-dia-chi') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
		$controller = new KhachHangController();
		$controller->xoaDiaChi();
		return;
	}

	if ($path === 'khach-hang/dat-mac-dinh') {
		require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
		$controller = new KhachHangController();
		$controller->datMacDinh();
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

	if (preg_match('#^danh-muc/([a-z0-9-]+)$#', $path, $matches)) {
        require_once dirname(__DIR__, 2) . '/controllers/client/SanPhamController.php';
        $controller = new \App\Controllers\Client\SanPhamController();
        // Gọi đến hàm danhSachTheoSlug() mà bạn đã viết sẵn trong Controller
        $controller->danhSachTheoSlug($matches[1]); 
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

	// Payment gateway callback routes (no authentication required)
	if ($path === 'thanh-toan/callback/vnpay') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->callbackVNPay();
		return;
	}

	if ($path === 'thanh-toan/callback/momo') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->callbackMomo();
		return;
	}

	if ($path === 'thanh-toan/callback/zalopay') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->callbackZaloPay();
		return;
	}

	// Payment gateway return URL routes
	if ($path === 'thanh-toan/return/vnpay') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->returnVNPay();
		return;
	}

	if ($path === 'thanh-toan/return/momo') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->returnMomo();
		return;
	}

	if ($path === 'thanh-toan/return/zalopay') {
		require_once dirname(__DIR__, 2) . '/controllers/client/ThanhToanController.php';
		$controller = new \App\Controllers\Client\ThanhToanController();
		$controller->returnZaloPay();
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

	// About routes
	if ($path === 'gioi-thieu') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->gioiThieu();
		return;
	}

	if ($path === 'quy-che-hoat-dong') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->quyCheShoatDong();
		return;
	}

	if ($path === 'gioi-thieu-may-doi-tra') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->gioiThieuMayDoiTra();
		return;
	}

	if ($path === 'chinh-sach-bao-hanh') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->chinhSachBaoHanh();
		return;
	}

	if ($path === 'chinh-sach-doi-tra') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->chinhSachDoiTra();
		return;
	}

	if ($path === 'chinh-sach-bao-mat') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->chinhSachBaoMat();
		return;
	}

	if ($path === 'cau-hoi-thuong-gap') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->cauHoiThuongGap();
		return;
	}

	if ($path === 'apple') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->apple();
		return;
	}

	if ($path === 'mang-di-dong') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->mangDiDong();
		return;
	}

	if ($path === 'goi-cuoc') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->goiCuoc();
		return;
	}

	if ($path === 'diem-cung-cap') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->diemCungCap();
		return;
	}

	if ($path === 'giao-hang') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->giaoHang();
		return;
	}

	if ($path === 'giao-hang-dien-may') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->giaoHangDienMay();
		return;
	}

	if ($path === 'giao-hang-online') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->giaoHangOnline();
		return;
	}

	if ($path === 'khach-hang-than-thiet') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->khachHangThanThiet();
		return;
	}

	if ($path === 'khui-hop') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->khuiHop();
		return;
	}

	if ($path === 'mua-hang-online') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->muaHangOnline();
		return;
	}

	if ($path === 'may-doi-tra') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->mayDoiTra();
		return;
	}

	if ($path === 'doi-tra') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->doiTra();
		return;
	}

	if ($path === 'bao-mat-du-lieu') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->baoMatDuLieu();
		return;
	}

	if ($path === 'quy-che') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->quyChe();
		return;
	}

	if ($path === 'bao-mat') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->baoMat();
		return;
	}

	if ($path === 'ho-tro-ky-thuat') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->hoTroKyThuat();
		return;
	}

	if ($path === 'bao-hanh') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->baoHanh();
		return;
	}

	if ($path === 'tra-gop') {
		require_once dirname(__DIR__, 2) . '/controllers/client/AboutController.php';
		$controller = new \App\Controllers\Client\AboutController();
		$controller->traGop();
		return;
	}


	require_once dirname(__DIR__, 2) . '/views/client/home/index.php';
}

