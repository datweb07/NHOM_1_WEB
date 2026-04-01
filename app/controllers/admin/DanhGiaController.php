<?php
require_once dirname(dirname(__DIR__)) . '/models/entities/DanhGia.php';
require_once dirname(dirname(__DIR__)) . '/models/entities/SanPham.php';
require_once dirname(dirname(__DIR__)) . '/core/Session.php';

class DanhGiaController
{
    private DanhGia $danhGiaModel;
    private SanPham $sanPhamModel;

    public function __construct()
    {
        $this->danhGiaModel = new DanhGia();
        $this->sanPhamModel = new SanPham();
    }

    /**
     * Display reviews list with filtering and search
     */
    public function index(): void
    {
        // Get filter parameters
        $soSao = isset($_GET['so_sao']) && $_GET['so_sao'] !== '' ? (int)$_GET['so_sao'] : null;
        $sanPhamId = isset($_GET['san_pham_id']) && $_GET['san_pham_id'] !== '' ? (int)$_GET['san_pham_id'] : null;
        $keyword = $_GET['keyword'] ?? '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get reviews based on filters
        if ($keyword !== '') {
            $danhSachDanhGia = $this->danhGiaModel->timKiem($keyword, $limit, $offset);
        } else {
            $danhSachDanhGia = $this->danhGiaModel->layDanhSach($soSao, $sanPhamId, $limit, $offset);
        }

        // Get total count for pagination
        $totalReviews = $this->danhGiaModel->demDanhGia($soSao, $sanPhamId, $keyword !== '' ? $keyword : null);
        $totalPages = ceil($totalReviews / $limit);

        // Get all products for filter dropdown
        $danhSachSanPham = $this->sanPhamModel->layTatCa();

        // Get success/error messages
        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';

        // Load view
        require_once dirname(dirname(__DIR__)) . '/views/admin/danh_gia/index.php';
    }

    /**
     * Display review detail
     */
    public function detail(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header('Location: /admin/danh-gia?error=invalid_id');
            exit;
        }

        $danhGia = $this->danhGiaModel->layTheoId($id);

        if (!$danhGia) {
            header('Location: /admin/danh-gia?error=not_found');
            exit;
        }

        // Get additional info
        $sql = "SELECT dg.*, nd.ho_ten, nd.email, nd.sdt, sp.ten_san_pham, sp.slug
                FROM danh_gia dg
                LEFT JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
                LEFT JOIN san_pham sp ON dg.san_pham_id = sp.id
                WHERE dg.id = " . (int)$id;
        
        $result = $this->danhGiaModel->query($sql);
        $danhGia = $result[0] ?? null;

        if (!$danhGia) {
            header('Location: /admin/danh-gia?error=not_found');
            exit;
        }

        // Load view
        require_once dirname(dirname(__DIR__)) . '/views/admin/danh_gia/detail.php';
    }

    /**
     * Delete a review
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/danh-gia');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header('Location: /admin/danh-gia?error=invalid_id');
            exit;
        }

        $danhGia = $this->danhGiaModel->layTheoId($id);

        if (!$danhGia) {
            header('Location: /admin/danh-gia?error=not_found');
            exit;
        }

        // Delete review
        $deleted = $this->danhGiaModel->xoa($id);

        if ($deleted) {
            header('Location: /admin/danh-gia?success=deleted');
        } else {
            header('Location: /admin/danh-gia?error=delete_failed');
        }
        exit;
    }
}
