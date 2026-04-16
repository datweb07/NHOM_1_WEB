<?php

require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/models/abstract/NguoiDung.php';
require_once dirname(__DIR__, 2) . '/core/View.php';

use App\Core\View;

class DashboardController
{
    private DonHang $donHangModel;
    private ThanhToan $thanhToanModel;
    private SanPham $sanPhamModel;
    private NguoiDung $nguoiDungModel;

    public function __construct()
    {
        $this->donHangModel = new DonHang();
        $this->thanhToanModel = new ThanhToan();
        $this->sanPhamModel = new SanPham();
        
        // Create anonymous class extending NguoiDung for querying
        $this->nguoiDungModel = new class extends NguoiDung {
            public function __construct() {
                parent::__construct();
            }
        };
    }

    public function index(): void
    {
        // Query pending orders count (trang_thai = 'CHO_DUYET')
        $pendingOrdersSql = "SELECT COUNT(*) as total FROM don_hang WHERE trang_thai = 'CHO_DUYET'";
        $pendingOrdersResult = $this->donHangModel->query($pendingOrdersSql);
        $pendingOrders = $pendingOrdersResult[0]['total'] ?? 0;

        // Query active users count (loai_tai_khoan = 'MEMBER')
        $activeUsersSql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE loai_tai_khoan = 'MEMBER'";
        $activeUsersResult = $this->nguoiDungModel->query($activeUsersSql);
        $totalUsers = $activeUsersResult[0]['total'] ?? 0;

        // Query available products count (trang_thai = 'CON_BAN')
        $activeProductsSql = "SELECT COUNT(*) as total FROM san_pham WHERE trang_thai = 'CON_BAN'";
        $activeProductsResult = $this->sanPhamModel->query($activeProductsSql);
        $activeProducts = $activeProductsResult[0]['total'] ?? 0;

        // Query pending payments count (trang_thai_duyet = 'CHO_DUYET')
        $pendingPaymentsSql = "SELECT COUNT(*) as total FROM thanh_toan WHERE trang_thai_duyet = 'CHO_DUYET'";
        $pendingPaymentsResult = $this->thanhToanModel->query($pendingPaymentsSql);
        $pendingPayments = $pendingPaymentsResult[0]['total'] ?? 0;

        // Calculate monthly revenue (current month)
        $monthlyRevenue = $this->calculateMonthlyRevenue();

        // Calculate monthly orders count (current month)
        $currentMonth = date('Y-m');
        $monthlyOrdersSql = "SELECT COUNT(*) as total 
                             FROM don_hang 
                             WHERE DATE_FORMAT(ngay_tao, '%Y-%m') = '$currentMonth'";
        $monthlyOrdersResult = $this->donHangModel->query($monthlyOrdersSql);
        $monthlyOrders = $monthlyOrdersResult[0]['total'] ?? 0;

        // Prepare data for view
        $data = [
            'pendingOrders' => (int)$pendingOrders,
            'totalUsers' => (int)$totalUsers,
            'activeProducts' => (int)$activeProducts,
            'pendingPayments' => (int)$pendingPayments,
            'monthlyRevenue' => (float)$monthlyRevenue,
            'monthlyOrders' => (int)$monthlyOrders,
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => '/admin']
            ]
        ];

        // Load dashboard view (admin views don't use layout wrapper)
        View::render('admin/dashboard/index', $data, null);
    }

    /**
     * Calculate monthly revenue with corrected logic
     * - Only include orders with THANH_CONG payment approval
     * - Exclude orders with COMPLETED refunds
     * - Exclude cancelled/returned orders
     * 
     * @return float Monthly revenue amount
     */
    private function calculateMonthlyRevenue(): float
    {
        $currentMonth = date('Y-m');
        
        $sql = "SELECT COALESCE(SUM(don_hang.tong_thanh_toan), 0) as revenue 
                FROM don_hang 
                INNER JOIN thanh_toan ON don_hang.id = thanh_toan.don_hang_id
                LEFT JOIN refund ON thanh_toan.id = refund.thanh_toan_id AND refund.status = 'COMPLETED'
                WHERE DATE_FORMAT(don_hang.ngay_tao, '%Y-%m') = '$currentMonth'
                AND thanh_toan.trang_thai_duyet = 'THANH_CONG'
                AND don_hang.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
                AND refund.id IS NULL";
        
        $result = $this->donHangModel->query($sql);
        
        return (float)($result[0]['revenue'] ?? 0);
    }

    /**
     * Calculate total revenue with corrected logic
     * - Only include orders with THANH_CONG payment approval
     * - Exclude orders with COMPLETED refunds
     * - Exclude cancelled/returned orders
     * 
     * @return float Total revenue amount
     */
    private function calculateTotalRevenue(): float
    {
        $sql = "SELECT COALESCE(SUM(don_hang.tong_thanh_toan), 0) as revenue 
                FROM don_hang 
                INNER JOIN thanh_toan ON don_hang.id = thanh_toan.don_hang_id
                LEFT JOIN refund ON thanh_toan.id = refund.thanh_toan_id AND refund.status = 'COMPLETED'
                WHERE thanh_toan.trang_thai_duyet = 'THANH_CONG'
                AND don_hang.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
                AND refund.id IS NULL";
        
        $result = $this->donHangModel->query($sql);
        
        return (float)($result[0]['revenue'] ?? 0);
    }
}
