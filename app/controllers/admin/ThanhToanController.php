<?php

class ThanhToanController
{
    private $thanhToanModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
        $this->thanhToanModel = new ThanhToan();
    }

    /**
     * Display list of pending payments
     */
    public function index(): void
    {
        // Pagination parameters
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get pending payments
        $danhSachThanhToan = $this->thanhToanModel->layDanhSachChoDuyet($limit, $offset);
        
        // Count total for pagination
        $totalRecords = $this->thanhToanModel->demChoDuyet();
        $totalPages = ceil($totalRecords / $limit);

        $data = [
            'danhSachThanhToan' => $danhSachThanhToan,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/thanh_toan/index.php';
    }

    /**
     * Display payment detail with receipt image
     */
    public function detail($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/thanh-toan?error=invalid_id');
            exit;
        }

        $thanhToan = $this->thanhToanModel->getById($id);
        if ($thanhToan === null) {
            header('Location: /admin/thanh-toan?error=not_found');
            exit;
        }

        // Get order details
        require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
        $donHangModel = new DonHang();
        $donHang = $donHangModel->layChiTietDonHang((int)$thanhToan['don_hang_id']);

        $data = [
            'thanhToan' => $thanhToan,
            'donHang' => $donHang,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/thanh_toan/detail.php';
    }

    /**
     * Approve payment
     */
    public function approve($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/thanh-toan');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/thanh-toan?error=invalid_id');
            exit;
        }

        // Validate payment exists
        $thanhToan = $this->thanhToanModel->getById($id);
        if ($thanhToan === null) {
            header('Location: /admin/thanh-toan?error=not_found');
            exit;
        }

        // Get current admin user ID from session
        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }

        // Get optional note
        $ghiChu = trim((string)($_POST['ghi_chu'] ?? ''));
        
        // Approve payment
        $this->thanhToanModel->duyetThanhToan($id, $adminId, 'THANH_CONG', $ghiChu !== '' ? $ghiChu : null);
        
        header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&success=approved');
        exit;
    }

    /**
     * Reject payment
     */
    public function reject($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/thanh-toan');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/thanh-toan?error=invalid_id');
            exit;
        }

        // Validate payment exists
        $thanhToan = $this->thanhToanModel->getById($id);
        if ($thanhToan === null) {
            header('Location: /admin/thanh-toan?error=not_found');
            exit;
        }

        // Get current admin user ID from session
        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }

        // Get optional note
        $ghiChu = trim((string)($_POST['ghi_chu'] ?? ''));
        
        // Reject payment
        $this->thanhToanModel->tuChoiThanhToan($id, $adminId, $ghiChu !== '' ? $ghiChu : null);
        
        header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&success=rejected');
        exit;
    }
}
