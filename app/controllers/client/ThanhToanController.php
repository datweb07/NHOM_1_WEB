<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/GioHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/ChiTietGio.php';
require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/ChiTietDon.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
require_once dirname(__DIR__, 2) . '/models/entities/DiaChi.php';
require_once dirname(__DIR__, 2) . '/models/entities/MaGiamGia.php';
require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';
require_once dirname(__DIR__, 2) . '/services/payment/PaymentService.php';
require_once dirname(__DIR__, 2) . '/services/payment/CallbackHandler.php';
require_once dirname(__DIR__, 2) . '/services/payment/VNPayGateway.php';
require_once dirname(__DIR__, 2) . '/services/payment/MomoGateway.php';
require_once dirname(__DIR__, 2) . '/enums/PhuongThucThanhToan.php';

use GioHang;
use ChiTietGio;
use DonHang;
use ChiTietDon;
use ThanhToan;
use DiaChi;
use MaGiamGia;
use PhienBanSanPham;
use \App\Core\Session;

class ThanhToanController
{
    private GioHang $gioHangModel;
    private ChiTietGio $chiTietGioModel;
    private DonHang $donHangModel;
    private ChiTietDon $chiTietDonModel;
    private ThanhToan $thanhToanModel;
    private DiaChi $diaChiModel;
    private MaGiamGia $maGiamGiaModel;
    private PhienBanSanPham $phienBanModel;
    private \PaymentService $paymentService;
    private \CallbackHandler $callbackHandler;

    public function __construct()
    {
        $this->gioHangModel = new GioHang();
        $this->chiTietGioModel = new ChiTietGio();
        $this->donHangModel = new DonHang();
        $this->chiTietDonModel = new ChiTietDon();
        $this->thanhToanModel = new ThanhToan();
        $this->diaChiModel = new DiaChi();
        $this->maGiamGiaModel = new MaGiamGia();
        $this->phienBanModel = new PhienBanSanPham();
        $this->paymentService = new \PaymentService();
        $this->callbackHandler = new \CallbackHandler();
    }

    public function index(): void
    {

        $gioHang = $this->layGioHangHienTai();
        $chiTietGioList = $this->chiTietGioModel->layChiTietGioHang($gioHang['id']);

        if (empty($chiTietGioList)) {
            Session::flash('error', 'Giỏ hàng trống');
            header('Location: /gio-hang');
            exit;
        }


        $diaChiList = [];
        $diaChiMacDinh = null;

        if (Session::has('user_id')) {
            $diaChiList = $this->diaChiModel->layDanhSachTheoUser(Session::get('user_id'));
            $diaChiMacDinh = $this->diaChiModel->layDiaChiMacDinh(Session::get('user_id'));
        }

        $tongTien = $this->chiTietGioModel->tinhTongTien($gioHang['id']);
        $phiVanChuyen = 30000;


        $vnpayEnabled = (new \VNPayGateway())->isConfigured();
        $momoEnabled = (new \MomoGateway())->isConfigured();

        require_once dirname(__DIR__, 2) . '/services/payment/ZaloPayGateway.php';
        $zalopayEnabled = (new \ZaloPayGateway())->isConfigured();


        $gatewayWarnings = $this->checkGatewayHealth();

        require_once dirname(__DIR__, 2) . '/views/client/thanh_toan/index.php';
    }

    public function datHang(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /thanh-toan');
            exit;
        }


        $gioHang = $this->layGioHangHienTai();
        $chiTietGioList = $this->chiTietGioModel->layChiTietGioHang($gioHang['id']);

        if (empty($chiTietGioList)) {
            Session::flash('error', 'Giỏ hàng trống');
            header('Location: /gio-hang');
            exit;
        }


        foreach ($chiTietGioList as $item) {
            if (!$this->phienBanModel->kiemTraTonKho($item['phien_ban_id'], $item['so_luong'])) {
                Session::flash('error', 'Sản phẩm "' . $item['ten_san_pham'] . '" không đủ số lượng trong kho');
                header('Location: /gio-hang');
                exit;
            }
        }


        $tongTien = $this->chiTietGioModel->tinhTongTien($gioHang['id']);
        $phiVanChuyen = 30000;
        $tienGiamGia = 0;
        $maGiamGiaId = null;


        if (!empty($_POST['ma_giam_gia'])) {
            $maCode = trim((string)$_POST['ma_giam_gia']);
            $maGiamGia = $this->maGiamGiaModel->kiemTraMaGiamGia($maCode, $tongTien);
            if ($maGiamGia) {
                $tienGiamGia = $this->maGiamGiaModel->tinhTienGiam($maGiamGia, $tongTien);
                $maGiamGiaId = $maGiamGia['id'];
            } else {
                $lyDoLoi = $this->maGiamGiaModel->layThongBaoLoiMaGiamGia($maCode, $tongTien) ?? 'Mã giảm giá không hợp lệ';
                Session::flash('error', $lyDoLoi);
                header('Location: /thanh-toan');
                exit;
            }
        }

        $tongThanhToan = max(0, $tongTien + $phiVanChuyen - $tienGiamGia);


        $diaChiId = null;
        $thongTinGuest = null;

        if (Session::has('user_id')) {
            $diaChiId = isset($_POST['dia_chi_id']) ? (int)$_POST['dia_chi_id'] : null;
            if (!$diaChiId) {
                Session::flash('error', 'Vui lòng chọn địa chỉ giao hàng');
                header('Location: /thanh-toan');
                exit;
            }
        } else {

            $thongTinGuest = json_encode([
                'ten' => $_POST['ten_nguoi_nhan'] ?? '',
                'sdt' => $_POST['sdt_nhan'] ?? '',
                'dia_chi' => $_POST['dia_chi'] ?? ''
            ]);
        }


        $phuongThucThanhToan = $_POST['phuong_thuc_thanh_toan'] ?? 'COD';

        if (!\PhuongThucThanhToan::isValid($phuongThucThanhToan)) {
            Session::flash('error', 'Phương thức thanh toán không hợp lệ');
            header('Location: /thanh-toan');
            exit;
        }


        if ($phuongThucThanhToan === 'CHUYEN_KHOAN') {
            $vnpayGateway = new \VNPayGateway();
            if (!$vnpayGateway->isConfigured()) {
                Session::flash('error', 'Phương thức thanh toán VNPay hiện không khả dụng');
                header('Location: /thanh-toan');
                exit;
            }
        } elseif ($phuongThucThanhToan === 'VI_DIEN_TU') {
            $momoGateway = new \MomoGateway();
            if (!$momoGateway->isConfigured()) {
                Session::flash('error', 'Phương thức thanh toán Momo hiện không khả dụng');
                header('Location: /thanh-toan');
                exit;
            }
        } elseif ($phuongThucThanhToan === 'ZALOPAY') {
            require_once dirname(__DIR__, 2) . '/services/payment/ZaloPayGateway.php';
            $zalopayGateway = new \ZaloPayGateway();
            if (!$zalopayGateway->isConfigured()) {
                Session::flash('error', 'Phương thức thanh toán ZaloPay hiện không khả dụng');
                header('Location: /thanh-toan');
                exit;
            }
        }


        $maDonHang = 'DH' . date('YmdHis');
        $ghiChu = $_POST['ghi_chu'] ?? '';

        $donHangId = $this->donHangModel->create([
            'ma_don_hang' => $maDonHang,
            'nguoi_dung_id' => Session::has('user_id') ? Session::get('user_id') : null,
            'dia_chi_id' => $diaChiId,
            'ma_giam_gia_id' => $maGiamGiaId,
            'trang_thai' => 'CHO_DUYET',
            'tong_tien' => $tongTien,
            'phi_van_chuyen' => $phiVanChuyen,
            'tien_giam_gia' => $tienGiamGia,
            'tong_thanh_toan' => $tongThanhToan,
            'thong_tin_guest' => $thongTinGuest,
            'ghi_chu' => $ghiChu
        ]);


        foreach ($chiTietGioList as $item) {
            $this->chiTietDonModel->themChiTiet(
                $donHangId,
                $item['phien_ban_id'],
                $item['so_luong'],
                $item['gia_ban']
            );


            $this->phienBanModel->giamTonKho($item['phien_ban_id'], $item['so_luong']);
        }


        $transactionId = $this->paymentService->createTransaction($donHangId, $phuongThucThanhToan, $tongThanhToan);


        $paymentResult = $this->paymentService->processPayment($transactionId, $phuongThucThanhToan);

        if (!$paymentResult['success']) {
            Session::flash('error', $paymentResult['message']);
            header('Location: /thanh-toan');
            exit;
        }


        if ($maGiamGiaId) {
            $this->maGiamGiaModel->tangSoLuotDung($maGiamGiaId);
        }


        $this->chiTietGioModel->xoaTatCa($gioHang['id']);


        if ($phuongThucThanhToan === 'COD') {

            Session::flash('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $maDonHang);
            header('Location: /don-hang/' . $donHangId);
            exit;
        } else {

            if (!empty($paymentResult['payment_url'])) {
                header('Location: ' . $paymentResult['payment_url']);
                exit;
            } else {
                Session::flash('error', 'Không thể tạo liên kết thanh toán. Vui lòng thử lại.');
                header('Location: /thanh-toan');
                exit;
            }
        }
    }

    public function kiemTraMaGiamGia(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $maCode = trim((string)($_POST['ma_code'] ?? ''));
        $tongTien = isset($_POST['tong_tien']) ? (float)$_POST['tong_tien'] : 0;

        if ($maCode === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá']);
            exit;
        }

        $maGiamGia = $this->maGiamGiaModel->kiemTraMaGiamGia($maCode, $tongTien);

        if (!$maGiamGia) {
            $lyDoLoi = $this->maGiamGiaModel->layThongBaoLoiMaGiamGia($maCode, $tongTien) ?? 'Mã giảm giá không hợp lệ';
            echo json_encode(['success' => false, 'message' => $lyDoLoi]);
            exit;
        }

        $tienGiam = $this->maGiamGiaModel->tinhTienGiam($maGiamGia, $tongTien);

        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'tien_giam' => $tienGiam,
            'mo_ta' => $maGiamGia['mo_ta']
        ]);
        exit;
    }

    private function layGioHangHienTai(): array
    {
        if (Session::has('user_id')) {
            return $this->gioHangModel->layHoacTaoGioHangUser(Session::get('user_id'));
        }

        if (!Session::has('cart_session_id')) {
            Session::set('cart_session_id', session_id());
        }

        return $this->gioHangModel->layHoacTaoGioHangGuest(Session::get('cart_session_id'));
    }

    public function callbackVNPay(): void
    {
        header('Content-Type: application/json');

        $data = $_GET;
        $result = $this->callbackHandler->handleVNPayCallback($data);

        echo json_encode($result);
        exit;
    }

    public function callbackMomo(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $result = $this->callbackHandler->handleMomoCallback($data);

        echo json_encode($result);
        exit;
    }

    public function returnVNPay(): void
    {
        $data = $_GET;


        $gateway = new \VNPayGateway();
        $isValidSignature = $gateway->verifyReturnUrl($data);

        if (!$isValidSignature) {
            Session::flash('error', 'Xác thực thanh toán thất bại. Vui lòng liên hệ hỗ trợ.');
            header('Location: /');
            exit;
        }


        $transactionId = $data['vnp_TxnRef'] ?? null;

        if (!$transactionId) {
            Session::flash('error', 'Không tìm thấy thông tin giao dịch.');
            header('Location: /');
            exit;
        }

        $transaction = $this->paymentService->getTransaction($transactionId);

        if (!$transaction) {
            Session::flash('error', 'Không tìm thấy thông tin giao dịch.');
            header('Location: /');
            exit;
        }

        $donHangId = $transaction['don_hang_id'];
        $status = $transaction['trang_thai_duyet'];

        if ($status === 'THANH_CONG') {
            Session::flash('success', 'Thanh toán thành công!');
            header('Location: /don-hang/' . $donHangId);
        } elseif ($status === 'THAT_BAI') {
            $errorMessage = $transaction['error_message'] ?? 'Thanh toán thất bại';
            Session::flash('error', $errorMessage);
            header('Location: /don-hang/' . $donHangId);
        } else {
            Session::flash('info', 'Giao dịch đang được xử lý. Vui lòng kiểm tra lại sau.');
            header('Location: /don-hang/' . $donHangId);
        }

        exit;
    }

    public function returnMomo(): void
    {
        $data = $_GET;


        $gateway = new \MomoGateway();
        $isValidSignature = $gateway->verifyReturnUrl($data);

        if (!$isValidSignature) {
            Session::flash('error', 'Xác thực thanh toán thất bại. Vui lòng liên hệ hỗ trợ.');
            header('Location: /');
            exit;
        }


        $transactionId = $data['orderId'] ?? null;

        if (!$transactionId) {
            Session::flash('error', 'Không tìm thấy thông tin giao dịch.');
            header('Location: /');
            exit;
        }

        $transaction = $this->paymentService->getTransaction($transactionId);

        if (!$transaction) {
            Session::flash('error', 'Không tìm thấy thông tin giao dịch.');
            header('Location: /');
            exit;
        }

        $donHangId = $transaction['don_hang_id'];
        $status = $transaction['trang_thai_duyet'];


        if ($status === 'THANH_CONG') {
            Session::flash('success', 'Thanh toán thành công!');
            header('Location: /don-hang/' . $donHangId);
        } elseif ($status === 'THAT_BAI') {
            $errorMessage = $transaction['error_message'] ?? 'Thanh toán thất bại';
            Session::flash('error', $errorMessage);
            header('Location: /don-hang/' . $donHangId);
        } else {
            Session::flash('info', 'Giao dịch đang được xử lý. Vui lòng kiểm tra lại sau.');
            header('Location: /don-hang/' . $donHangId);
        }

        exit;
    }

    public function callbackZaloPay(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $result = $this->callbackHandler->handleZaloPayCallback($data);

        echo json_encode($result);
        exit;
    }

    public function returnZaloPay(): void
    {
        $data = $_GET;

        require_once dirname(__DIR__, 2) . '/services/payment/ZaloPayGateway.php';
        $gateway = new \ZaloPayGateway();
        $isValidSignature = $gateway->verifyReturnUrl($data);

        if (!$isValidSignature) {
            Session::flash('error', 'Xác thực thanh toán thất bại. Vui lòng liên hệ hỗ trợ.');
            header('Location: /');
            exit;
        }

        $transactionId = $data['apptransid'] ?? null;

        if (!$transactionId) {
            Session::flash('error', 'Không tìm thấy thông tin giao dịch.');
            header('Location: /');
            exit;
        }

        $transaction = $this->paymentService->getTransaction($transactionId);

        if (!$transaction) {
            Session::flash('error', 'Không tìm thấy thông tin giao dịch.');
            header('Location: /');
            exit;
        }

        $donHangId = $transaction['don_hang_id'];
        $status = $transaction['trang_thai_duyet'];

        if ($status === 'THANH_CONG') {
            Session::flash('success', 'Thanh toán thành công!');
            header('Location: /don-hang/' . $donHangId);
        } elseif ($status === 'THAT_BAI') {
            $errorMessage = $transaction['error_message'] ?? 'Thanh toán thất bại';
            Session::flash('error', $errorMessage);
            header('Location: /don-hang/' . $donHangId);
        } else {
            Session::flash('info', 'Giao dịch đang được xử lý. Vui lòng kiểm tra lại sau.');
            header('Location: /don-hang/' . $donHangId);
        }

        exit;
    }

    private function checkGatewayHealth(): array
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        $healthModel = new \GatewayHealth();

        $warnings = [];


        $vnpayHealth = $healthModel->getByGatewayName('VNPay');
        if ($vnpayHealth) {
            $successRate = $healthModel->getSuccessRate('VNPay', 24);
            if ($successRate < 50 && ($vnpayHealth['success_count'] + $vnpayHealth['failure_count']) >= 10) {
                $warnings['vnpay'] = [
                    'gateway' => 'VNPay',
                    'message' => 'Cổng thanh toán VNPay đang gặp sự cố. Vui lòng chọn phương thức thanh toán khác.',
                    'success_rate' => $successRate
                ];
            }
        }


        $momoHealth = $healthModel->getByGatewayName('Momo');
        if ($momoHealth) {
            $successRate = $healthModel->getSuccessRate('Momo', 24);
            if ($successRate < 50 && ($momoHealth['success_count'] + $momoHealth['failure_count']) >= 10) {
                $warnings['momo'] = [
                    'gateway' => 'Momo',
                    'message' => 'Cổng thanh toán Momo đang gặp sự cố. Vui lòng chọn phương thức thanh toán khác.',
                    'success_rate' => $successRate
                ];
            }
        }

        $zalopayHealth = $healthModel->getByGatewayName('ZaloPay');
        if ($zalopayHealth) {
            $successRate = $healthModel->getSuccessRate('ZaloPay', 24);
            if ($successRate < 50 && ($zalopayHealth['success_count'] + $zalopayHealth['failure_count']) >= 10) {
                $warnings['zalopay'] = [
                    'gateway' => 'ZaloPay',
                    'message' => 'Cổng thanh toán ZaloPay đang gặp sự cố. Vui lòng chọn phương thức thanh toán khác.',
                    'success_rate' => $successRate
                ];
            }
        }

        return $warnings;
    }
}
