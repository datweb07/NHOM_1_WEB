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
        $trangThai = trim((string)($_GET['trang_thai'] ?? ''));
        $search = trim((string)($_GET['search'] ?? ''));
        $phuongThuc = trim((string)($_GET['phuong_thuc'] ?? ''));
        $dateFrom = trim((string)($_GET['date_from'] ?? ''));
        $dateTo = trim((string)($_GET['date_to'] ?? ''));
        

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;


        $danhSachDonHang = [];
        $totalRecords = 0;

        if ($search !== '') {
            $danhSachDonHang = $this->donHangModel->timKiem(
                $search,
                $trangThai !== '' ? $trangThai : null,
                $limit,
                $offset
            );
            $allResults = $this->donHangModel->timKiem($search, $trangThai !== '' ? $trangThai : null, 999999, 0);
            $totalRecords = count($allResults);
        } elseif ($dateFrom !== '' && $dateTo !== '') {


            $allResults = $this->donHangModel->layTheoKhoangNgay(
                $dateFrom,
                $dateTo,
                $trangThai !== '' ? $trangThai : null
            );

            $totalRecords = count($allResults);
            $danhSachDonHang = array_slice($allResults, $offset, $limit);
        } elseif ($phuongThuc !== '') {

            $allResults = $this->donHangModel->layTheoPhuongThuc($phuongThuc);

            if ($trangThai !== '') {
                $allResults = array_filter($allResults, function($item) use ($trangThai) {
                    return ($item['trang_thai'] ?? '') === $trangThai;
                });
            }
            $totalRecords = count($allResults);
            $danhSachDonHang = array_slice($allResults, $offset, $limit);
        } else {

            $danhSachDonHang = $this->donHangModel->layDanhSach($trangThai !== '' ? $trangThai : null);
            $totalRecords = $this->donHangModel->demDonHang($trangThai !== '' ? $trangThai : null);
            $danhSachDonHang = array_slice($danhSachDonHang, $offset, $limit);
        }


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


        require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
        $thanhToanModel = new ThanhToan();
        $thanhToan = $thanhToanModel->layTheoDonHang($id);


        $refunds = [];
        if ($thanhToan !== null) {
            require_once dirname(__DIR__, 2) . '/models/entities/Refund.php';
            $refundModel = new Refund();
            $refunds = $refundModel->findByThanhToanId((int)$thanhToan['id']);
        }

        $data = [
            'donHang' => $donHang,
            'chiTietDon' => $chiTietDon,
            'trangThaiKeTiep' => $trangThaiKeTiep,
            'thanhToan' => $thanhToan,
            'refunds' => $refunds,
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


        if (!$this->donHangModel->trangThaiHopLe($trangThaiHienTai, $trangThaiMoi)) {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=invalid_transition');
            exit;
        }


        require_once dirname(__DIR__, 2) . '/models/entities/ChiTietDon.php';
        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        
        $chiTietDonModel = new ChiTietDon();
        $phienBanModel = new PhienBanSanPham();
        
        $chiTietDon = $chiTietDonModel->layTheoDonHang($id);


        if ($trangThaiMoi === 'DA_XAC_NHAN' && $trangThaiHienTai !== 'DA_XAC_NHAN') {
            foreach ($chiTietDon as $item) {
                $phienBanId = (int)$item['phien_ban_id'];
                $soLuong = (int)$item['so_luong'];
                

                $phienBan = $phienBanModel->getById($phienBanId);
                if ($phienBan) {
                    $soLuongTonHienTai = (int)$phienBan['so_luong_ton'];
                    

                    if ($soLuongTonHienTai < $soLuong) {
                        header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=insufficient_stock');
                        exit;
                    }
                    

                    $soLuongTonMoi = $soLuongTonHienTai - $soLuong;
                    $phienBanModel->capNhatTonKho($phienBanId, $soLuongTonMoi);
                }
            }
        }


        if (($trangThaiMoi === 'DA_HUY' || $trangThaiMoi === 'TRA_HANG') && 
            ($trangThaiHienTai === 'DA_XAC_NHAN' || $trangThaiHienTai === 'DANG_GIAO' || $trangThaiHienTai === 'DA_GIAO')) {
            foreach ($chiTietDon as $item) {
                $phienBanId = (int)$item['phien_ban_id'];
                $soLuong = (int)$item['so_luong'];
                

                $phienBan = $phienBanModel->getById($phienBanId);
                if ($phienBan) {
                    $soLuongTonHienTai = (int)$phienBan['so_luong_ton'];
                    

                    $soLuongTonMoi = $soLuongTonHienTai + $soLuong;
                    $phienBanModel->capNhatTonKho($phienBanId, $soLuongTonMoi);
                }
            }
        }


        $this->donHangModel->capNhatTrangThai($id, $trangThaiMoi);
        header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&success=status_updated');
        exit;
    }

    public function hoanTien($id): void
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


        require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
        $thanhToanModel = new ThanhToan();
        $thanhToan = $thanhToanModel->layTheoDonHang($id);

        if ($thanhToan === null) {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=no_payment');
            exit;
        }


        if ($thanhToan['trang_thai_duyet'] !== 'THANH_CONG') {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=refund_failed');
            exit;
        }


        require_once dirname(__DIR__, 2) . '/models/entities/Refund.php';
        $refundModel = new Refund();
        $existingRefunds = $refundModel->findByThanhToanId((int)$thanhToan['id']);
        
        if (!empty($existingRefunds)) {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=already_refunded');
            exit;
        }

        $phuongThuc = $thanhToan['phuong_thuc'] ?? '';
        $soTien = (float)($thanhToan['so_tien'] ?? 0);
        $gatewayTransactionId = $thanhToan['gateway_transaction_id'] ?? '';
        $reason = 'Hoan tien don hang #' . ($donHang['ma_don_hang'] ?? $id);


        if ($phuongThuc === 'COD') {
            $refundId = $refundModel->createRefund((int)$thanhToan['id'], $soTien, $reason);
            if ($refundId) {
                $refundModel->updateRefundStatus($refundId, 'COMPLETED', null);
                

                $this->donHangModel->capNhatTrangThai($id, 'DA_HUY');
                
                header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&success=refund_completed');
            } else {
                header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=refund_failed');
            }
            exit;
        }

        $gateway = null;
        
        if ($phuongThuc === 'VI_DIEN_TU') {
            $gatewayName = $thanhToan['gateway_name'] ?? '';
            
            if ($gatewayName === 'vnpay' || strpos($gatewayTransactionId, 'VNP') !== false) {
                require_once dirname(__DIR__, 2) . '/services/payment/VNPayGateway.php';
                $gateway = new VNPayGateway();
                $gatewayType = 'vnpay';
            } elseif ($gatewayName === 'momo' || strpos($gatewayTransactionId, 'MOMO') !== false) {
                require_once dirname(__DIR__, 2) . '/services/payment/MomoGateway.php';
                $gateway = new MomoGateway();
                $gatewayType = 'momo';
            }
        }

        if ($gateway === null || empty($gatewayTransactionId)) {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=refund_failed');
            exit;
        }

        $refundId = $refundModel->createRefund((int)$thanhToan['id'], $soTien, $reason);
        
        if (!$refundId) {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=refund_failed');
            exit;
        }

        $refundResult = $gateway->initiateRefund($gatewayTransactionId, $soTien, $reason);

        if ($refundResult['success']) {

            $refundModel->updateRefundStatus(
                $refundId,
                'COMPLETED',
                $refundResult['refund_id']
            );
            

            $thanhToanModel->update((int)$thanhToan['id'], [
                'trang_thai_duyet' => 'HOAN_TIEN'
            ]);
            

            $this->donHangModel->capNhatTrangThai($id, 'DA_HUY');
            
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&success=refund_completed');
        } else {

            $refundModel->updateRefundStatus($refundId, 'FAILED', null);
            
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=refund_failed');
        }
        
        exit;
    }
}
