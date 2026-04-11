<?php

function adminRoute(string $uri): void
{
    $path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($path === 'admin' || $path === 'admin/') {
        header('Location: /admin/dashboard');
        exit;
    }

    if ($path === 'admin/auth/login') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once dirname(__DIR__, 2) . '/controllers/admin/AuthController.php';
            \App\Controllers\Admin\AuthController::login($_POST['email'] ?? '', $_POST['password'] ?? '');
            return;
        }
        require_once dirname(__DIR__, 2) . '/views/admin/auth/login.php';
        return;
    }

    if ($path === 'admin/auth/logout') {
        require_once dirname(__DIR__, 2) . '/controllers/admin/AuthController.php';
        \App\Controllers\Admin\AuthController::logout();
        return;
    }

    if ($path === 'admin/dashboard') {
        require_once dirname(__DIR__, 2) . '/controllers/admin/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->index();
        return;
    }

    require_once dirname(__DIR__, 2) . '/controllers/admin/DanhMucController.php';
    $danhMucController = new DanhMucController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/DonHangController.php';
    $donHangController = new DonHangController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/ThanhToanController.php';
    $thanhToanController = new ThanhToanController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/SanPhamController.php';
    $sanPhamController = new SanPhamController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/KhuyenMaiController.php';
    $khuyenMaiController = new KhuyenMaiController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/MaGiamGiaController.php';
    $maGiamGiaController = new MaGiamGiaController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/NguoiDungController.php';
    $nguoiDungController = new NguoiDungController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/DanhGiaController.php';
    $danhGiaController = new DanhGiaController();
    require_once dirname(__DIR__, 2) . '/controllers/admin/BannerController.php';
    $bannerController = new BannerController();

    // Profile routes
    if ($path === 'admin/profile' && $method === 'GET') {
        require_once dirname(__DIR__, 2) . '/controllers/admin/ProfileController.php';
        $profileController = new ProfileController();
        $profileController->index();
        return;
    }

    if ($path === 'admin/profile/update' && $method === 'POST') {
        require_once dirname(__DIR__, 2) . '/controllers/admin/ProfileController.php';
        $profileController = new ProfileController();
        $profileController->update();
        return;
    }

    if ($path === 'admin/profile/update-avatar' && $method === 'POST') {
        require_once dirname(__DIR__, 2) . '/controllers/admin/ProfileController.php';
        $profileController = new ProfileController();
        $profileController->updateAvatar();
        return;
    }

    if ($path === 'admin/profile/change-password' && $method === 'POST') {
        require_once dirname(__DIR__, 2) . '/controllers/admin/ProfileController.php';
        $profileController = new ProfileController();
        $profileController->changePassword();
        return;
    }

    if ($path === 'admin/danh-muc' && $method === 'GET') {
        $danhMucController->index();
        return;
    }

    if ($path === 'admin/danh-muc/them') {
        if ($method === 'POST') {
            $danhMucController->store();
            return;
        }
        $danhMucController->create();
        return;
    }

    if ($path === 'admin/danh-muc/sua') {
        $id = $_GET['id'] ?? null;
        if ($method === 'POST') {
            $danhMucController->update($id);
            return;
        }
        $danhMucController->edit($id);
        return;
    }

    if ($path === 'admin/danh-muc/xoa') {
        $id = $_GET['id'] ?? null;
        $danhMucController->xoa($id);
        return;
    }

    if ($path === 'admin/danh-muc/hien') {
        $id = $_GET['id'] ?? null;
        $danhMucController->hien($id);
        return;
    }

    if ($path === 'admin/danh-muc/cap-nhat-hang-loat' && $method === 'POST') {
        $danhMucController->bulkUpdateStatus();
        return;
    }

    if ($path === 'admin/don-hang' && $method === 'GET') {
        $donHangController->index();
        return;
    }

    if ($path === 'admin/don-hang/chi-tiet' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $donHangController->detail($id);
        return;
    }

    if ($path === 'admin/don-hang/cap-nhat-trang-thai' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $donHangController->capNhatTrangThai($id);
        return;
    }

    if ($path === 'admin/don-hang/hoan-tien' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $donHangController->hoanTien($id);
        return;
    }

    if ($path === 'admin/thanh-toan' && $method === 'GET') {
        $thanhToanController->index();
        return;
    }

    if ($path === 'admin/thanh-toan/chi-tiet' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $thanhToanController->detail($id);
        return;
    }

    if ($path === 'admin/thanh-toan/duyet' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $thanhToanController->approve($id);
        return;
    }

    if ($path === 'admin/thanh-toan/tu-choi' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $thanhToanController->reject($id);
        return;
    }

    if ($path === 'admin/thanh-toan/xac-nhan-cod' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $thanhToanController->confirmCODPayment($id);
        return;
    }

    if ($path === 'admin/thanh-toan/xuat-csv' && $method === 'GET') {
        $thanhToanController->exportTransactions();
        return;
    }

    if ($path === 'admin/thanh-toan/cleanup-logs' && $method === 'POST') {
        $thanhToanController->cleanupLogs();
        return;
    }

    if ($path === 'admin/thanh-toan/health' && $method === 'GET') {
        $thanhToanController->healthDashboard();
        return;
    }

    if ($path === 'admin/san-pham' && $method === 'GET') {
        $sanPhamController->index();
        return;
    }

    if ($path === 'admin/san-pham/them') {
        if ($method === 'POST') {
            $sanPhamController->store();
            return;
        }
        $sanPhamController->create();
        return;
    }

    if ($path === 'admin/san-pham/sua') {
        $id = $_GET['id'] ?? null;
        if ($method === 'POST') {
            $sanPhamController->update($id);
            return;
        }
        $sanPhamController->edit($id);
        return;
    }

    if ($path === 'admin/san-pham/xoa') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->xoa($id);
        return;
    }

    if ($path === 'admin/san-pham/mo-ban') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->moBan($id);
        return;
    }

    if ($path === 'admin/san-pham/cap-nhat-hang-loat' && $method === 'POST') {
        $sanPhamController->bulkUpdateStatus();
        return;
    }

    if ($path === 'admin/san-pham/phien-ban' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->variants($id);
        return;
    }

    if ($path === 'admin/san-pham/phien-ban/them' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->createVariant($id);
        return;
    }

    if ($path === 'admin/san-pham/phien-ban/sua' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->updateVariant($id);
        return;
    }

    if ($path === 'admin/san-pham/phien-ban/xoa') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->deleteVariant($id);
        return;
    }

    if ($path === 'admin/san-pham/hinh-anh' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->images($id);
        return;
    }

    if ($path === 'admin/san-pham/upload-anh' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->uploadImage($id);
        return;
    }

    if ($path === 'admin/san-pham/xoa-anh') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->deleteImage($id);
        return;
    }

    if ($path === 'admin/san-pham/dat-anh-chinh') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->setMainImage($id);
        return;
    }

    if ($path === 'admin/san-pham/thong-so' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->specifications($id);
        return;
    }

    if ($path === 'admin/san-pham/cap-nhat-thong-so' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $sanPhamController->updateSpecifications($id);
        return;
    }

    if ($path === 'admin/khuyen-mai' && $method === 'GET') {
        $khuyenMaiController->index();
        return;
    }

    if ($path === 'admin/khuyen-mai/them') {
        if ($method === 'POST') {
            $khuyenMaiController->store();
            return;
        }
        $khuyenMaiController->create();
        return;
    }

    if ($path === 'admin/khuyen-mai/sua') {
        $id = $_GET['id'] ?? null;
        if ($method === 'POST') {
            $khuyenMaiController->update($id);
            return;
        }
        $khuyenMaiController->edit($id);
        return;
    }

    if ($path === 'admin/khuyen-mai/xoa') {
        $id = $_GET['id'] ?? null;
        $khuyenMaiController->delete($id);
        return;
    }

    if ($path === 'admin/khuyen-mai/lien-ket-san-pham') {
        $id = $_GET['id'] ?? null;
        if ($method === 'POST') {
            $khuyenMaiController->saveProductLinks($id);
            return;
        }
        $khuyenMaiController->linkProducts($id);
        return;
    }

    if ($path === 'admin/ma-giam-gia' && $method === 'GET') {
        $maGiamGiaController->index();
        return;
    }

    if ($path === 'admin/ma-giam-gia/them') {
        if ($method === 'POST') {
            $maGiamGiaController->store();
            return;
        }
        $maGiamGiaController->create();
        return;
    }

    if ($path === 'admin/ma-giam-gia/sua') {
        $id = $_GET['id'] ?? null;
        if ($method === 'POST') {
            $maGiamGiaController->update($id);
            return;
        }
        $maGiamGiaController->edit($id);
        return;
    }

    if ($path === 'admin/ma-giam-gia/xoa') {
        $id = $_GET['id'] ?? null;
        $maGiamGiaController->delete($id);
        return;
    }

    // Users
    if ($path === 'admin/nguoi-dung' && $method === 'GET') {
        $nguoiDungController->index();
        return;
    }

    if ($path === 'admin/nguoi-dung/chi-tiet' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $nguoiDungController->detail($id);
        return;
    }

    if ($path === 'admin/nguoi-dung/chan') {
        $id = $_GET['id'] ?? null;
        $nguoiDungController->block($id);
        return;
    }

    if ($path === 'admin/nguoi-dung/mo-chan') {
        $id = $_GET['id'] ?? null;
        $nguoiDungController->unblock($id);
        return;
    }

    if ($path === 'admin/nguoi-dung/cap-nhat-hang-loat' && $method === 'POST') {
        $nguoiDungController->bulkUpdateStatus();
        return;
    }

    // Reviews
    if ($path === 'admin/danh-gia' && $method === 'GET') {
        $danhGiaController->index();
        return;
    }

    if ($path === 'admin/danh-gia/chi-tiet' && $method === 'GET') {
        $danhGiaController->detail();
        return;
    }

    if ($path === 'admin/danh-gia/xoa' && $method === 'POST') {
        $danhGiaController->delete();
        return;
    }

    // Banner
    if ($path === 'admin/banner' && $method === 'GET') {
        $bannerController->index();
        return;
    }

    if ($path === 'admin/banner/them') {
        if ($method === 'POST') {
            $bannerController->store();
            return;
        }
        $bannerController->create();
        return;
    }

    if ($path === 'admin/banner/sua') {
        $id = $_GET['id'] ?? null;
        if ($method === 'POST') {
            $bannerController->update($id);
            return;
        }
        $bannerController->edit($id);
        return;
    }

    if ($path === 'admin/banner/xoa' && $method === 'POST') {
        $id = $_GET['id'] ?? null;
        $bannerController->delete($id);
        return;
    }

    // API: Lấy danh sách sản phẩm cho modal chọn sản phẩm
    if ($path === 'admin/api/san-pham' && $method === 'GET') {
        $bannerController->layDanhSachSanPham();
        return;
    }

    // API: Lấy thuộc tính động theo danh mục (AJAX)
    if ($path === 'admin/api/get-category-attributes' && $method === 'GET') {
        $sanPhamController->getCategoryAttributes();
        return;
    }

    http_response_code(404);
    require_once dirname(__DIR__, 2) . '/views/errors/404.php';
}
