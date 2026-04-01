<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/relationships/YeuThich.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';

class YeuThichController
{
    private $yeuThichModel;

    public function __construct()
    {
        $this->yeuThichModel = new \YeuThich();
    }

    /**
     * Hiển thị danh sách sản phẩm yêu thích
     */
    public function index(): void
    {
        if (!\App\Core\Session::get('user_id')) {
            header('Location: /client/auth/login');
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $sanPhams = $this->yeuThichModel->layDanhSachTheoUser(\App\Core\Session::get('user_id'), $limit, $offset);
        $tongSanPham = $this->yeuThichModel->demTheoUser(\App\Core\Session::get('user_id'));
        $tongTrang = ceil($tongSanPham / $limit);

        require_once dirname(__DIR__, 2) . '/views/client/yeu_thich/index.php';
    }

    /**
     * Thêm sản phẩm vào yêu thích
     */
    public function them(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!\App\Core\Session::get('user_id')) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            return;
        }

        $sanPhamId = isset($_POST['san_pham_id']) ? (int)$_POST['san_pham_id'] : 0;

        if ($sanPhamId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }

        $result = $this->yeuThichModel->them(\App\Core\Session::get('user_id'), $sanPhamId);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Đã thêm vào yêu thích']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã có trong danh sách yêu thích']);
        }
    }

    /**
     * Xóa sản phẩm khỏi yêu thích
     */
    public function xoa(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!\App\Core\Session::get('user_id')) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            return;
        }

        $sanPhamId = isset($_POST['san_pham_id']) ? (int)$_POST['san_pham_id'] : 0;

        if ($sanPhamId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }

        $result = $this->yeuThichModel->xoa(\App\Core\Session::get('user_id'), $sanPhamId);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Đã xóa khỏi yêu thích' : 'Xóa thất bại'
        ]);
    }

    /**
     * Kiểm tra sản phẩm đã được yêu thích chưa
     */
    public function kiemTra(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!\App\Core\Session::get('user_id')) {
            echo json_encode(['success' => false, 'is_favorite' => false]);
            return;
        }

        $sanPhamId = isset($_GET['san_pham_id']) ? (int)$_GET['san_pham_id'] : 0;

        if ($sanPhamId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }

        $isFavorite = $this->yeuThichModel->kiemTraDaTonTai(\App\Core\Session::get('user_id'), $sanPhamId);

        echo json_encode(['success' => true, 'is_favorite' => $isFavorite]);
    }

    /**
     * Đếm số sản phẩm yêu thích
     */
    public function dem(): void
    {
        if (!\App\Core\Session::get('user_id')) {
            echo json_encode(['success' => false, 'count' => 0]);
            return;
        }

        $count = $this->yeuThichModel->demTheoUser(\App\Core\Session::get('user_id'));

        echo json_encode(['success' => true, 'count' => $count]);
    }
}
