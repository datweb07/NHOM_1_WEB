<?php

class DonHangController
{
    private $donHangModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
        $this->donHangModel = new DonHang();
    }

    public function index(): void
    {
        // Get filter parameters
        $trangThai = trim((string)($_GET['trang_thai'] ?? ''));
        $search = trim((string)($_GET['search'] ?? ''));
        $phuongThuc = trim((string)($_GET['phuong_thuc'] ?? ''));
        $dateFrom = trim((string)($_GET['date_from'] ?? ''));
        $dateTo = trim((string)($_GET['date_to'] ?? ''));
        
        // Pagination parameters
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Build query based on filters
        $danhSachDonHang = [];
        $totalRecords = 0;

        if ($search !== '') {
            // Search by ma_don_hang or customer name
            $danhSachDonHang = $this->donHangModel->timKiem(
                $search,
                $trangThai !== '' ? $trangThai : null,
                $limit,
                $offset
            );
            // Count for pagination (simplified - count all matching search)
            $allResults = $this->donHangModel->timKiem($search, $trangThai !== '' ? $trangThai : null, 999999, 0);
            $totalRecords = count($allResults);
        } elseif ($dateFrom !== '' && $dateTo !== '') {
            // Filter by date range
            $allResults = $this->donHangModel->layTheoKhoangNgay(
                $dateFrom,
                $dateTo,
                $trangThai !== '' ? $trangThai : null
            );
            $totalRecords = count($allResults);
            $danhSachDonHang = array_slice($allResults, $offset, $limit);
        } elseif ($phuongThuc !== '') {
            // Filter by payment method
            $allResults = $this->donHangModel->layTheoPhuongThuc($phuongThuc);
            // Apply status filter if provided
            if ($trangThai !== '') {
                $allResults = array_filter($allResults, function($item) use ($trangThai) {
                    return ($item['trang_thai'] ?? '') === $trangThai;
                });
            }
            $totalRecords = count($allResults);
            $danhSachDonHang = array_slice($allResults, $offset, $limit);
        } else {
            // Default listing with optional status filter
            $danhSachDonHang = $this->donHangModel->layDanhSach($trangThai !== '' ? $trangThai : null);
            $totalRecords = $this->donHangModel->demDonHang($trangThai !== '' ? $trangThai : null);
            $danhSachDonHang = array_slice($danhSachDonHang, $offset, $limit);
        }

        // Calculate pagination
        $totalPages = ceil($totalRecords / $limit);

        $data = [
            'danhSachDonHang' => $danhSachDonHang,
            'trangThaiFilter' => $trangThai,
            'searchFilter' => $search,
            'phuongThucFilter' => $phuongThuc,
            'dateFromFilter' => $dateFrom,
            'dateToFilter' => $dateTo,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/don_hang/index.php';
    }

    public function detail($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/don-hang?error=invalid_id');
            exit;
        }

        $donHang = $this->donHangModel->layChiTietDonHang($id);
        if ($donHang === null) {
            header('Location: /admin/don-hang?error=not_found');
            exit;
        }

        $chiTietDon = $this->donHangModel->laySanPhamTrongDon($id);
        $trangThaiKeTiep = $this->donHangModel->layTrangThaiKeTiep((string)$donHang['trang_thai']);

        $data = [
            'donHang' => $donHang,
            'chiTietDon' => $chiTietDon,
            'trangThaiKeTiep' => $trangThaiKeTiep,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/don_hang/detail.php';
    }

    public function capNhatTrangThai($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/don-hang');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/don-hang?error=invalid_id');
            exit;
        }

        $donHang = $this->donHangModel->layChiTietDonHang($id);
        if ($donHang === null) {
            header('Location: /admin/don-hang?error=not_found');
            exit;
        }

        $trangThaiMoi = trim((string)($_POST['trang_thai'] ?? ''));
        $trangThaiHienTai = (string)$donHang['trang_thai'];

        // Validate status transition
        if (!$this->donHangModel->trangThaiHopLe($trangThaiHienTai, $trangThaiMoi)) {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=invalid_transition');
            exit;
        }

        // Load order items for stock management
        require_once dirname(__DIR__, 2) . '/models/entities/ChiTietDon.php';
        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        
        $chiTietDonModel = new ChiTietDon();
        $phienBanModel = new PhienBanSanPham();
        
        $chiTietDon = $chiTietDonModel->layTheoDonHang($id);

        // Stock management integration
        // Reduce stock when order transitions to DA_XAC_NHAN
        if ($trangThaiMoi === 'DA_XAC_NHAN' && $trangThaiHienTai !== 'DA_XAC_NHAN') {
            foreach ($chiTietDon as $item) {
                $phienBanId = (int)$item['phien_ban_id'];
                $soLuong = (int)$item['so_luong'];
                
                // Get current stock
                $phienBan = $phienBanModel->getById($phienBanId);
                if ($phienBan) {
                    $soLuongTonHienTai = (int)$phienBan['so_luong_ton'];
                    
                    // Validate sufficient stock
                    if ($soLuongTonHienTai < $soLuong) {
                        header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=insufficient_stock');
                        exit;
                    }
                    
                    // Reduce stock
                    $soLuongTonMoi = $soLuongTonHienTai - $soLuong;
                    $phienBanModel->capNhatTonKho($phienBanId, $soLuongTonMoi);
                }
            }
        }

        // Restore stock when order transitions to DA_HUY or TRA_HANG
        if (($trangThaiMoi === 'DA_HUY' || $trangThaiMoi === 'TRA_HANG') && 
            ($trangThaiHienTai === 'DA_XAC_NHAN' || $trangThaiHienTai === 'DANG_GIAO' || $trangThaiHienTai === 'DA_GIAO')) {
            foreach ($chiTietDon as $item) {
                $phienBanId = (int)$item['phien_ban_id'];
                $soLuong = (int)$item['so_luong'];
                
                // Get current stock
                $phienBan = $phienBanModel->getById($phienBanId);
                if ($phienBan) {
                    $soLuongTonHienTai = (int)$phienBan['so_luong_ton'];
                    
                    // Restore stock
                    $soLuongTonMoi = $soLuongTonHienTai + $soLuong;
                    $phienBanModel->capNhatTonKho($phienBanId, $soLuongTonMoi);
                }
            }
        }

        // Update order status
        $this->donHangModel->capNhatTrangThai($id, $trangThaiMoi);
        header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&success=status_updated');
        exit;
    }
}
