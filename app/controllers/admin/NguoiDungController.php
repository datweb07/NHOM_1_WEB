<?php

class NguoiDungController
{
    private $nguoiDungModel;
    private $baseModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/roles/KhachHang.php';
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $this->nguoiDungModel = new KhachHang();
        $this->baseModel = new BaseModel('nguoi_dung');
    }

    public function index(): void
    {
        $loaiTaiKhoan = isset($_GET['loai_tai_khoan']) ? trim($_GET['loai_tai_khoan']) : '';
        $trangThai = isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get users based on search or filters
        if ($search !== '') {
            $danhSachNguoiDung = $this->nguoiDungModel->timKiem($search, $limit, $offset);
            $totalNguoiDung = count($this->nguoiDungModel->timKiem($search, 10000, 0));
        } else {
            $danhSachNguoiDung = $this->nguoiDungModel->layDanhSach($loaiTaiKhoan, $trangThai, $limit, $offset);
            $totalNguoiDung = $this->nguoiDungModel->demNguoiDung($loaiTaiKhoan, $trangThai);
        }
        
        $totalPages = ceil($totalNguoiDung / $limit);

        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';

        $data = [
            'loaiTaiKhoan' => $loaiTaiKhoan,
            'trangThai' => $trangThai,
            'search' => $search,
            'danhSachNguoiDung' => $danhSachNguoiDung,
            'totalNguoiDung' => $totalNguoiDung,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'success' => $success,
            'error' => $error,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/nguoi_dung/index.php';
    }

    public function detail($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/nguoi-dung?error=invalid_id');
            exit;
        }

        $nguoiDung = $this->baseModel->getById($id);
        if (!$nguoiDung) {
            header('Location: /admin/nguoi-dung?error=not_found');
            exit;
        }

        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';

        $data = [
            'nguoiDung' => $nguoiDung,
            'success' => $success,
            'error' => $error,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/nguoi_dung/detail.php';
    }

    public function block($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/nguoi-dung?error=invalid_id');
            exit;
        }

        $nguoiDung = $this->baseModel->getById($id);
        if (!$nguoiDung) {
            header('Location: /admin/nguoi-dung?error=not_found');
            exit;
        }

        // Prevent self-blocking
        require_once dirname(__DIR__, 2) . '/core/Session.php';
        Session::start();
        $currentUserId = Session::get('user_id');
        
        if ($id === $currentUserId) {
            header('Location: /admin/nguoi-dung?error=cannot_block_self');
            exit;
        }

        $this->nguoiDungModel->chanNguoiDung($id);
        header('Location: /admin/nguoi-dung?success=user_blocked');
        exit;
    }

    public function unblock($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/nguoi-dung?error=invalid_id');
            exit;
        }

        $nguoiDung = $this->baseModel->getById($id);
        if (!$nguoiDung) {
            header('Location: /admin/nguoi-dung?error=not_found');
            exit;
        }

        $this->nguoiDungModel->moChanNguoiDung($id);
        header('Location: /admin/nguoi-dung?success=user_unblocked');
        exit;
    }

    public function bulkUpdateStatus(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/nguoi-dung');
            exit;
        }

        $userIds = isset($_POST['user_ids']) && is_array($_POST['user_ids']) 
            ? array_map('intval', $_POST['user_ids']) 
            : [];
        
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';

        if (empty($userIds) || !in_array($action, ['block', 'unblock'], true)) {
            header('Location: /admin/nguoi-dung?error=invalid_bulk_action');
            exit;
        }

        // Prevent self-blocking
        require_once dirname(__DIR__, 2) . '/core/Session.php';
        Session::start();
        $currentUserId = Session::get('user_id');
        
        $successCount = 0;
        foreach ($userIds as $userId) {
            if ($userId === $currentUserId && $action === 'block') {
                continue; // Skip self-blocking
            }

            if ($action === 'block') {
                $this->nguoiDungModel->chanNguoiDung($userId);
            } else {
                $this->nguoiDungModel->moChanNguoiDung($userId);
            }
            $successCount++;
        }

        header("Location: /admin/nguoi-dung?success=bulk_updated&count=$successCount");
        exit;
    }
}

