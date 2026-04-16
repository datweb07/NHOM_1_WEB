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
require_once dirname(__DIR__, 2) . '/core/Functions.php';
require_once dirname(__DIR__, 2) . '/services/payment/PaymentService.php';
require_once dirname(__DIR__, 2) . '/services/payment/CallbackHandler.php';
require_once dirname(__DIR__, 2) . '/services/payment/VNPayGateway.php';
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
            $suDungDiaChiKhac = !empty($_POST['su_dung_dia_chi_khac']);

            if ($suDungDiaChiKhac) {
                $tenNguoiNhan = trim((string)($_POST['ten_nguoi_nhan'] ?? ''));
                $sdtNhan = trim((string)($_POST['sdt_nhan'] ?? ''));
                $diaChiDuong = trim((string)($_POST['dia_chi_duong'] ?? ''));
                $phuongXa = trim((string)($_POST['phuong_xa'] ?? ''));
                $quanHuyen = trim((string)($_POST['quan_huyen'] ?? ''));
                $tinhThanh = trim((string)($_POST['tinh_thanh'] ?? ''));

                if ($tenNguoiNhan === '' || $sdtNhan === '' || $diaChiDuong === '' || $phuongXa === '' || $quanHuyen === '' || $tinhThanh === '') {
                    Session::flash('error', 'Vui lòng nhập đầy đủ thông tin địa chỉ giao hàng mới');
                    header('Location: /thanh-toan');
                    exit;
                }

                $diaChiDayDu = implode(', ', array_filter([
                    $diaChiDuong,
                    $phuongXa,
                    $quanHuyen,
                    $tinhThanh,
                ]));

                $thongTinGuest = json_encode([
                    'ten' => $tenNguoiNhan,
                    'sdt' => $sdtNhan,
                    'dia_chi_duong' => $diaChiDuong,
                    'phuong_xa' => $phuongXa,
                    'quan_huyen' => $quanHuyen,
                    'tinh_thanh' => $tinhThanh,
                    'dia_chi_day_du' => $diaChiDayDu,
                    'loai_dia_chi' => 'KHAC_DA_DANG_NHAP',
                ], JSON_UNESCAPED_UNICODE);
                $diaChiId = null;
            } else {
                $diaChiId = isset($_POST['dia_chi_id']) ? (int)$_POST['dia_chi_id'] : null;
                if (!$diaChiId) {
                    Session::flash('error', 'Vui lòng chọn địa chỉ giao hàng');
                    header('Location: /thanh-toan');
                    exit;
                }
            }
        } else {
            $tenNguoiNhan = trim((string)($_POST['ten_nguoi_nhan'] ?? ''));
            $sdtNhan = trim((string)($_POST['sdt_nhan'] ?? ''));
            $emailNhan = trim((string)($_POST['email_nhan'] ?? ''));
            $diaChiDuong = trim((string)($_POST['dia_chi_duong'] ?? $_POST['dia_chi'] ?? ''));
            $phuongXa = trim((string)($_POST['phuong_xa'] ?? ''));
            $quanHuyen = trim((string)($_POST['quan_huyen'] ?? ''));
            $tinhThanh = trim((string)($_POST['tinh_thanh'] ?? ''));

            if ($tenNguoiNhan === '' || $sdtNhan === '' || $emailNhan === '' || $diaChiDuong === '' || $phuongXa === '' || $quanHuyen === '' || $tinhThanh === '') {
                Session::flash('error', 'Vui lòng nhập đầy đủ thông tin và địa chỉ nhận hàng');
                header('Location: /thanh-toan');
                exit;
            }

            if (!filter_var($emailNhan, FILTER_VALIDATE_EMAIL)) {
                Session::flash('error', 'Email nhận hàng không hợp lệ');
                header('Location: /thanh-toan');
                exit;
            }

            $diaChiDayDu = implode(', ', array_filter([
                $diaChiDuong,
                $phuongXa,
                $quanHuyen,
                $tinhThanh,
            ]));

            $thongTinGuest = json_encode([
                'ten' => $tenNguoiNhan,
                'sdt' => $sdtNhan,
                'email' => $emailNhan,
                'dia_chi_duong' => $diaChiDuong,
                'phuong_xa' => $phuongXa,
                'quan_huyen' => $quanHuyen,
                'tinh_thanh' => $tinhThanh,
                'dia_chi_day_du' => $diaChiDayDu,
            ], JSON_UNESCAPED_UNICODE);
        }

        if (!Session::has('user_id') && empty($_POST['xac_nhan_don'])) {
            Session::flash('error', 'Vui lòng xác nhận đơn hàng trước khi đặt');
            header('Location: /thanh-toan');
            exit;
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
            // Gửi email xác nhận đơn hàng COD
            $emailNguoiNhan = '';
            $tenNguoiNhan = '';

            if (Session::has('user_id')) {
                // Trường hợp 1: Người dùng đã đăng nhập
                // Lấy email trực tiếp từ DB
                $userId = (int)Session::get('user_id');
                $sql = "SELECT email, ho_ten FROM nguoi_dung WHERE id = $userId LIMIT 1";
                $userInfo = $this->diaChiModel->query($sql);

                if (!empty($userInfo)) {
                    $emailNguoiNhan = $userInfo[0]['email'] ?? '';

                    // Lấy tên người nhận từ địa chỉ mà khách đã chọn
                    if ($diaChiId) {
                        $diaChiChon = $this->diaChiModel->getById($diaChiId);
                        if ($diaChiChon) {
                            $tenNguoiNhan = $diaChiChon['ten_nguoi_nhan'] ?? $userInfo[0]['ho_ten'] ?? 'Quý khách';
                        } else {
                            $tenNguoiNhan = $userInfo[0]['ho_ten'] ?? 'Quý khách';
                        }
                    } else {
                        $tenNguoiNhan = $userInfo[0]['ho_ten'] ?? 'Quý khách';
                    }
                }

                error_log("COD Email - Logged in user. Email: $emailNguoiNhan, Name: $tenNguoiNhan");
            } else {
                // Trường hợp 2: Khách vãng lai
                $emailNguoiNhan = trim($_POST['email_nhan'] ?? '');
                $tenNguoiNhan = trim($_POST['ten_nguoi_nhan'] ?? 'Quý khách');

                error_log("COD Email - Guest user. Email: $emailNguoiNhan, Name: $tenNguoiNhan");
            }

            // Gửi email nếu có địa chỉ email hợp lệ
            if (!empty($emailNguoiNhan) && filter_var($emailNguoiNhan, FILTER_VALIDATE_EMAIL)) {
                try {
                    error_log("COD Email - Preparing to send email to: $emailNguoiNhan for order: $maDonHang");

                    // Tạo nội dung email
                    $emailContent = $this->generateOrderConfirmationEmail(
                        $maDonHang,
                        $tenNguoiNhan,
                        $chiTietGioList,
                        $tongTien,
                        $phiVanChuyen,
                        $tienGiamGia,
                        $tongThanhToan
                    );

                    // Gửi email
                    $mailSent = sendMail(
                        $emailNguoiNhan,
                        'Xác nhận đơn hàng #' . $maDonHang . ' từ FPT Shop',
                        $emailContent
                    );

                    if ($mailSent) {
                        error_log("COD Email - Email sent successfully to: $emailNguoiNhan");
                    } else {
                        error_log("COD Email - Failed to send email to: $emailNguoiNhan");
                    }
                } catch (\Exception $e) {
                    error_log("COD Email - Exception occurred: " . $e->getMessage());
                }
            } else {
                error_log("COD Email - Email validation failed or empty. Email: '$emailNguoiNhan'. POST data: " . print_r($_POST, true));
            }

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

        return $warnings;
    }

    /**
     * Tạo nội dung HTML cho email xác nhận đơn hàng
     */
    private function generateOrderConfirmationEmail(
        string $maDonHang,
        string $tenKhachHang,
        array $chiTietDon,
        float $tongTienHang,
        float $phiVanChuyen,
        float $tienGiamGia,
        float $tongThanhToan
    ): string {
        $tenSafe = htmlspecialchars($tenKhachHang);
        $maDonSafe = htmlspecialchars($maDonHang);

        $tongTienHangFormat = number_format($tongTienHang, 0, ',', '.') . 'đ';
        $phiVanChuyenFormat = number_format($phiVanChuyen, 0, ',', '.') . 'đ';
        $tienGiamGiaFormat = number_format($tienGiamGia, 0, ',', '.') . 'đ';
        $tongThanhToanFormat = number_format($tongThanhToan, 0, ',', '.') . 'đ';

        // Tạo chuỗi HTML cho danh sách sản phẩm
        $productListHtml = '';
        if (!empty($chiTietDon)) {
            foreach ($chiTietDon as $item) {
                $tenSp = htmlspecialchars($item['ten_san_pham'] ?? '');
                $tenPhienBan = htmlspecialchars($item['ten_phien_ban'] ?? '');
                $sl = (int)($item['so_luong'] ?? 0);
                $gia = number_format(($item['gia_ban'] ?? 0) * $sl, 0, ',', '.') . 'đ';

                $productListHtml .= "
                <tr>
                    <td style=\"padding: 10px; border-bottom: 1px solid #eeeeee;\">
                        <strong>{$tenSp}</strong>";

                if (!empty($tenPhienBan)) {
                    $productListHtml .= "<br><small style=\"color: #777;\">{$tenPhienBan}</small>";
                }

                $productListHtml .= "<br><small style=\"color: #777;\">Số lượng: {$sl}</small>
                    </td>
                    <td align=\"right\" style=\"padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;\">{$gia}</td>
                </tr>";
            }
        }

        // Trả về chuỗi HTML template
        return "<!doctype html>
<html lang=\"vi\">
<head>
    <meta charset=\"UTF-8\" />
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
    <title>Xác nhận đơn hàng - FPT Shop</title>
</head>
<body style=\"margin: 0; padding: 0; background-color: #f4f4f4; font-family: 'Roboto', Arial, sans-serif;\">
    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background-color: #f4f4f4; padding: 40px 0\">
        <tr>
            <td align=\"center\">
                <table width=\"600\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; width: 100%; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;\">
                    <tr>
                        <td bgcolor=\"#cb1c22\" style=\"padding: 20px 40px; text-align: center;\">
                            <h1 style=\"margin: 0; color: #ffffff; font-size: 24px;\">FPT Shop</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style=\"padding: 40px 40px 30px\">
                            <h2 style=\"margin: 0 0 20px; font-size: 20px; color: #333333; font-weight: 600;\">Đặt hàng thành công!</h2>
                            <p style=\"margin: 0 0 16px; font-size: 15px; color: #333333\">Xin chào <strong>{$tenSafe}</strong>,</p>
                            <p style=\"margin: 0 0 24px; font-size: 15px; color: #555555; line-height: 1.6;\">
                                Cảm ơn bạn đã mua sắm tại FPT Shop. Đơn hàng <strong>#{$maDonSafe}</strong> của bạn đã được ghi nhận và đang trong quá trình xử lý.
                                Bạn đã chọn phương thức <strong>Thanh toán khi nhận hàng (COD)</strong>. Vui lòng chuẩn bị tiền mặt khi nhân viên giao hàng liên hệ.
                            </p>
                            <div style=\"background-color: #f9f9f9; padding: 20px; border-radius: 6px; margin-bottom: 24px;\">
                                <h3 style=\"margin: 0 0 15px; font-size: 16px; border-bottom: 2px solid #cb1c22; padding-bottom: 5px; display: inline-block;\">Chi tiết đơn hàng</h3>
                                <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"font-size: 14px;\">
                                    {$productListHtml}
                                    <tr>
                                        <td style=\"padding: 10px; padding-top: 20px; color: #555;\">Tổng tiền hàng:</td>
                                        <td align=\"right\" style=\"padding: 10px; padding-top: 20px;\">{$tongTienHangFormat}</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding: 10px; color: #555;\">Phí vận chuyển:</td>
                                        <td align=\"right\" style=\"padding: 10px;\">{$phiVanChuyenFormat}</td>
                                    </tr>";

        if ($tienGiamGia > 0) {
            $html = "
                                    <tr>
                                        <td style=\"padding: 10px; color: #555;\">Giảm giá:</td>
                                        <td align=\"right\" style=\"padding: 10px; color: #28a745;\">-{$tienGiamGiaFormat}</td>
                                    </tr>";
        } else {
            $html = "";
        }

        return $html . "
                                    <tr>
                                        <td style=\"padding: 15px 10px; font-weight: bold; font-size: 16px; border-top: 1px solid #ddd;\">Tổng thanh toán:</td>
                                        <td align=\"right\" style=\"padding: 15px 10px; font-weight: bold; font-size: 18px; color: #cb1c22; border-top: 1px solid #ddd;\">{$tongThanhToanFormat}</td>
                                    </tr>
                                </table>
                            </div>
                            <p style=\"margin: 0 0 8px; font-size: 14px; color: #666666\">
                                Nhân viên tổng đài có thể sẽ liên hệ với bạn qua số điện thoại để xác nhận đơn hàng trước khi giao.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style=\"background-color: #f8f9fa; border-top: 1px solid #eeeeee; padding: 20px 40px; text-align: center;\">
                            <p style=\"margin: 0 0 8px; font-size: 12px; color: #888888\">© 2024 FPT Shop. Tất cả quyền được bảo lưu.</p>
                            <p style=\"margin: 0; font-size: 12px; color: #aaaaaa\">
                                Đây là email tự động, vui lòng không trả lời. Nếu cần hỗ trợ, gọi hotline: 1800 6601.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";
    }
}
