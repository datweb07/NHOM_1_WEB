<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/LichSuTimKiem.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';

class TimKiemController
{
    private $sanPhamModel;
    private $lichSuModel;

    public function __construct()
    {
        $this->sanPhamModel = new \SanPham();
        $this->lichSuModel = new \LichSuTimKiem();
    }

    /**
     * Tìm kiếm sản phẩm
     */
    public function timKiem(): void
    {
        $params = $_GET;
        if (isset($params['q']) && !isset($params['keyword'])) {
            $params['keyword'] = $params['q'];
        }
        unset($params['q']);

        $queryString = http_build_query($params);
        $targetUrl = '/san-pham' . ($queryString !== '' ? ('?' . $queryString) : '');

        header('Location: ' . $targetUrl);
        exit;

        $keyword = $_GET['q'] ?? '';
        $danhMucId = isset($_GET['danh_muc']) ? (int)$_GET['danh_muc'] : 0;
        $giaMin = isset($_GET['gia_min']) ? (float)$_GET['gia_min'] : null;
        $giaMax = isset($_GET['gia_max']) ? (float)$_GET['gia_max'] : null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Lưu lịch sử tìm kiếm nếu user đã đăng nhập
        if (!empty($keyword) && \App\Core\Session::get('user_id')) {
            $this->lichSuModel->luuLichSu(\App\Core\Session::get('user_id'), $keyword);
        }

        // Lấy danh sách sản phẩm
        $sanPhams = $this->sanPhamModel->layDanhSachPhanTrang(
            $keyword,
            $danhMucId,
            $giaMin,
            $giaMax,
            $limit,
            $offset
        );

        // Đếm tổng số sản phẩm
        $tongSanPham = $this->sanPhamModel->demSanPham($keyword, $danhMucId, $giaMin, $giaMax);
        $tongTrang = ceil($tongSanPham / $limit);

        // Lấy danh mục để hiển thị filter
        $danhMucs = $this->sanPhamModel->layDanhSachDanhMucHoatDong();

        require_once dirname(__DIR__, 2) . '/views/client/tim_kiem/index.php';
    }

    /**
     * Lấy lịch sử tìm kiếm
     */
    public function layLichSu(): void
    {
        if (!$_SERVER['REQUEST_METHOD'] === 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!\App\Core\Session::get('user_id')) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $lichSu = $this->lichSuModel->layLichSuTheoUser(\App\Core\Session::get('user_id'), $limit);

        echo json_encode(['success' => true, 'data' => $lichSu]);
    }

    /**
     * Xóa lịch sử tìm kiếm
     */
    public function xoaLichSu(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!\App\Core\Session::get('user_id')) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        $result = $this->lichSuModel->xoaLichSu(\App\Core\Session::get('user_id'));

        echo json_encode(['success' => $result, 'message' => $result ? 'Đã xóa lịch sử' : 'Xóa thất bại']);
    }

    /**
     * Lấy từ khóa phổ biến
     */
    public function layTuKhoaPhoBien(): void
    {
        if (!$_SERVER['REQUEST_METHOD'] === 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $tuKhoas = $this->lichSuModel->layTuKhoaPhoBien($limit);

        echo json_encode(['success' => true, 'data' => $tuKhoas]);
    }
}
