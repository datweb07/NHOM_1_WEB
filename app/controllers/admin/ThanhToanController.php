<?php

class ThanhToanController
{
    private $thanhToanModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
        $this->thanhToanModel = new ThanhToan();
    }

    public function index(): void
    {

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;


        $paymentMethod = trim((string)($_GET['payment_method'] ?? ''));
        $status = trim((string)($_GET['status'] ?? ''));
        $search = trim((string)($_GET['search'] ?? ''));


        $danhSachThanhToan = $this->thanhToanModel->layDanhSachVoiFilter(
            $paymentMethod,
            $status,
            $search,
            $limit,
            $offset
        );
        

        $totalRecords = $this->thanhToanModel->demVoiFilter($paymentMethod, $status, $search);
        $totalPages = ceil($totalRecords / $limit);

        $data = [
            'danhSachThanhToan' => $danhSachThanhToan,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'paymentMethod' => $paymentMethod,
            'status' => $status,
            'search' => $search,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/thanh_toan/index.php';
    }

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


        require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
        $donHangModel = new DonHang();
        $donHang = $donHangModel->layChiTietDonHang((int)$thanhToan['don_hang_id']);


        require_once dirname(__DIR__, 2) . '/models/entities/TransactionLog.php';
        $transactionLogModel = new TransactionLog();
        $transactionLogs = $transactionLogModel->getByThanhToanId($id);

        require_once dirname(__DIR__, 2) . '/models/entities/Refund.php';
        $refundModel = new Refund();
        $refunds = $refundModel->findByThanhToanId($id);

        $data = [
            'thanhToan' => $thanhToan,
            'donHang' => $donHang,
            'transactionLogs' => $transactionLogs,
            'refunds' => $refunds,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/thanh_toan/detail.php';
    }

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


        $thanhToan = $this->thanhToanModel->getById($id);
        if ($thanhToan === null) {
            header('Location: /admin/thanh-toan?error=not_found');
            exit;
        }


        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }


        $ghiChu = trim((string)($_POST['ghi_chu'] ?? ''));
        

        $this->thanhToanModel->duyetThanhToan($id, $adminId, 'THANH_CONG', $ghiChu !== '' ? $ghiChu : null);
        
        header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&success=approved');
        exit;
    }

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


        $thanhToan = $this->thanhToanModel->getById($id);
        if ($thanhToan === null) {
            header('Location: /admin/thanh-toan?error=not_found');
            exit;
        }


        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }


        $ghiChu = trim((string)($_POST['ghi_chu'] ?? ''));
        

        $this->thanhToanModel->tuChoiThanhToan($id, $adminId, $ghiChu !== '' ? $ghiChu : null);
        
        header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&success=rejected');
        exit;
    }

    public function confirmCODPayment($id): void
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


        $thanhToan = $this->thanhToanModel->getById($id);
        if ($thanhToan === null) {
            header('Location: /admin/thanh-toan?error=not_found');
            exit;
        }


        if (($thanhToan['phuong_thuc'] ?? '') !== 'COD') {
            header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&error=not_cod');
            exit;
        }


        if (($thanhToan['trang_thai_duyet'] ?? '') !== 'CHO_DUYET') {
            header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&error=already_processed');
            exit;
        }


        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }


        $this->thanhToanModel->duyetThanhToan(
            $id, 
            $adminId, 
            'THANH_CONG', 
            'Xác nhận thanh toán COD bởi admin'
        );


        require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
        $donHangModel = new DonHang();
        $donHangModel->capNhatTrangThai((int)$thanhToan['don_hang_id'], 'DA_XAC_NHAN');


        require_once dirname(__DIR__, 2) . '/models/entities/TransactionLog.php';
        $transactionLogModel = new TransactionLog();
        $transactionLogModel->logRequest($id, 'COD', [
            'action' => 'manual_confirmation',
            'admin_id' => $adminId,
            'timestamp' => date('Y-m-d H:i:s'),
            'note' => 'Admin manually confirmed COD payment'
        ]);

        header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&success=cod_confirmed');
        exit;
    }

    public function exportTransactions(): void
    {

        $fromDate = trim((string)($_GET['from_date'] ?? ''));
        $toDate = trim((string)($_GET['to_date'] ?? ''));
        

        if ($fromDate === '') {
            $fromDate = date('Y-m-d', strtotime('-30 days'));
        }
        if ($toDate === '') {
            $toDate = date('Y-m-d');
        }


        $transactions = $this->thanhToanModel->layTheoKhoangNgay($fromDate, $toDate);


        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="transactions_' . $fromDate . '_to_' . $toDate . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');


        $output = fopen('php://output', 'w');
        

        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));


        fputcsv($output, [
            'ID',
            'Mã đơn hàng',
            'Khách hàng',
            'Email',
            'Số tiền',
            'Phương thức',
            'Mã giao dịch gateway',
            'Trạng thái',
            'Ngày tạo',
            'Ngày duyệt',
            'Người duyệt',
            'Ghi chú'
        ]);


        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction['id'] ?? '',
                $transaction['ma_don_hang'] ?? '',
                $transaction['customer_name'] ?? 'Khách vãng lai',
                $transaction['customer_email'] ?? '',
                number_format((float)($transaction['so_tien'] ?? 0), 0, ',', '.'),
                $transaction['phuong_thuc'] ?? '',
                $transaction['gateway_transaction_id'] ?? '',
                $transaction['trang_thai_duyet'] ?? '',
                $transaction['created_at'] ?? $transaction['ngay_thanh_toan'] ?? '',
                $transaction['ngay_duyet'] ?? '',
                $transaction['nguoi_duyet_id'] ?? '',
                $transaction['ghi_chu_duyet'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    public function cleanupLogs(): void
    {

        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }


        require_once dirname(__DIR__, 2) . '/scripts/cleanup_old_logs.php';
        
        $cleanup = new LogCleanupScript();
        

        $statsBefore = $cleanup->getStatistics();
        

        $result = $cleanup->execute();
        

        $statsAfter = $cleanup->getStatistics();
        

        if ($result['success']) {
            $message = sprintf(
                'Đã xóa %d bản ghi log cũ hơn %d tháng. Thời gian thực thi: %.2f giây.',
                $result['deleted_count'],
                $statsBefore['retention_months'],
                $result['execution_time']
            );
            header('Location: /admin/thanh-toan?success=' . urlencode($message));
        } else {
            $message = 'Lỗi khi xóa log: ' . ($result['error'] ?? 'Unknown error');
            header('Location: /admin/thanh-toan?error=' . urlencode($message));
        }
        
        exit;
    }

    public function healthDashboard(): void
    {

        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }


        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        $healthModel = new GatewayHealth();
        

        $gateways = $healthModel->getAllGateways();
        

        $gatewayMetrics = [];
        foreach ($gateways as $gateway) {
            $gatewayName = $gateway['gateway_name'];
            $successCount = (int)($gateway['success_count'] ?? 0);
            $failureCount = (int)($gateway['failure_count'] ?? 0);
            $totalCount = $successCount + $failureCount;
            
            $successRate = $totalCount > 0 ? ($successCount / $totalCount) * 100 : 100.0;
            $failureRate = $totalCount > 0 ? ($failureCount / $totalCount) * 100 : 0.0;
            

            $hasAlert = $failureRate > 50 && $totalCount >= 10;
            
            $gatewayMetrics[] = [
                'name' => $gatewayName,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'total_count' => $totalCount,
                'success_rate' => $successRate,
                'failure_rate' => $failureRate,
                'last_success_at' => $gateway['last_success_at'],
                'last_failure_at' => $gateway['last_failure_at'],
                'updated_at' => $gateway['updated_at'],
                'has_alert' => $hasAlert,
                'avg_processing_time' => 0.0 
            ];
        }
        
        $data = [
            'gatewayMetrics' => $gatewayMetrics,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/thanh_toan/health.php';
    }

    public function showRefundForm($id): void
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

        header('Location: /admin/thanh-toan/chi-tiet?id=' . $id);
        exit;
    }

    public function processRefund($id): void
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

        $thanhToan = $this->thanhToanModel->getById($id);
        if ($thanhToan === null) {
            header('Location: /admin/thanh-toan?error=payment_not_found');
            exit;
        }

        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
        $reason = trim((string)($_POST['reason'] ?? ''));

        if ($amount <= 0) {
            header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&error=invalid_amount');
            exit;
        }

        if ($reason === '') {
            header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&error=reason_required');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        if ($adminId === null) {
            header('Location: /admin/auth/login');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/services/refund/RefundService.php';
        $refundService = new RefundService();
        
        $result = $refundService->initiateRefund($id, $amount, $reason, $adminId);

        if ($result['success']) {
            header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&success=refund_completed');
        } else {
            $errorMessage = urlencode($result['message']);
            header('Location: /admin/thanh-toan/chi-tiet?id=' . $id . '&error=' . $errorMessage);
        }
        exit;
    }

    public function duyetThanhToan($thanhToanId): void
    {
        if (!\App\Core\Session::isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này';
            header('Location: /admin/thanh-toan');
            exit;
        }
        
        require_once dirname(__DIR__, 2) . '/core/Session.php';
        \App\Core\Session::start();
        $adminId = \App\Core\Session::getUserId();
        
        $thanhToanModel = new ThanhToan();
        
        $result = $thanhToanModel->duyetThanhToan($thanhToanId, $adminId, 'THANH_CONG', 'Đã xác nhận thanh toán VietQR');
        
        if ($result) {
            $thanhToan = $thanhToanModel->findById($thanhToanId);
            require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
            $donHangModel = new DonHang();
            $donHangModel->update($thanhToan['don_hang_id'], [
                'trang_thai' => 'DA_XAC_NHAN'
            ]);
            
            $_SESSION['success'] = 'Đã duyệt thanh toán thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi duyệt thanh toán';
        }
        
        header('Location: /admin/thanh-toan/chi-tiet/' . $thanhToanId);
        exit;
    }
}
