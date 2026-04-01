<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/DanhGia.php';
require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';

class DanhGiaController
{
    private $danhGiaModel;
    private $sanPhamModel;

    public function __construct()
    {
        $this->danhGiaModel = new \DanhGia();
        $this->sanPhamModel = new \SanPham();
    }

    /**
     * Lấy danh sách đánh giá của sản phẩm
     */
    public function layDanhSach(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $sanPhamId = isset($_GET['san_pham_id']) ? (int)$_GET['san_pham_id'] : 0;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

        if ($sanPhamId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }

        $danhGias = $this->danhGiaModel->layDanhGiaTheoSanPham($sanPhamId, $limit);
        $tongDanhGia = $this->danhGiaModel->demDanhGiaTheoSanPham($sanPhamId);
        $diemTrungBinh = $this->danhGiaModel->tinhDiemTrungBinh($sanPhamId);

        echo json_encode([
            'success' => true,
            'data' => $danhGias,
            'total' => $tongDanhGia,
            'average_rating' => round($diemTrungBinh, 1)
        ]);
    }

    /**
     * Thêm đánh giá
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
        $soSao = isset($_POST['so_sao']) ? (int)$_POST['so_sao'] : 0;
        $noiDung = isset($_POST['noi_dung']) ? trim($_POST['noi_dung']) : '';

        // Validate
        if ($sanPhamId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }

        if ($soSao < 1 || $soSao > 5) {
            echo json_encode(['success' => false, 'message' => 'Số sao phải từ 1 đến 5']);
            return;
        }

        if (empty($noiDung)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập nội dung đánh giá']);
            return;
        }

        // Kiểm tra đã đánh giá chưa
        if ($this->danhGiaModel->kiemTraDaDanhGia(\App\Core\Session::get('user_id'), $sanPhamId)) {
            echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
            return;
        }

        // Thêm đánh giá
        $result = $this->danhGiaModel->themDanhGia(
            \App\Core\Session::get('user_id'),
            $sanPhamId,
            $soSao,
            $noiDung
        );

        if ($result > 0) {
            // Cập nhật điểm đánh giá trung bình cho sản phẩm
            $diemTrungBinh = $this->danhGiaModel->tinhDiemTrungBinh($sanPhamId);
            $this->sanPhamModel->update($sanPhamId, ['diem_danh_gia' => $diemTrungBinh]);

            echo json_encode(['success' => true, 'message' => 'Đánh giá thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Đánh giá thất bại']);
        }
    }

    /**
     * Kiểm tra user đã đánh giá sản phẩm chưa
     */
    public function kiemTra(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!\App\Core\Session::get('user_id')) {
            echo json_encode(['success' => false, 'has_reviewed' => false]);
            return;
        }

        $sanPhamId = isset($_GET['san_pham_id']) ? (int)$_GET['san_pham_id'] : 0;

        if ($sanPhamId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }

        $hasReviewed = $this->danhGiaModel->kiemTraDaDanhGia(\App\Core\Session::get('user_id'), $sanPhamId);
        $review = null;

        if ($hasReviewed) {
            $review = $this->danhGiaModel->layDanhGiaCuaUser(\App\Core\Session::get('user_id'), $sanPhamId);
        }

        echo json_encode([
            'success' => true,
            'has_reviewed' => $hasReviewed,
            'review' => $review
        ]);
    }
}
