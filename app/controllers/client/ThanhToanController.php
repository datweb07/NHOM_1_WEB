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
    }

    /**
     * Trang thanh toán
     */
    public function index(): void
    {
        // Kiểm tra giỏ hàng
        $gioHang = $this->layGioHangHienTai();
        $chiTietGioList = $this->chiTietGioModel->layChiTietGioHang($gioHang['id']);
        
        if (empty($chiTietGioList)) {
            Session::flash('error', 'Giỏ hàng trống');
            header('Location: /gio-hang');
            exit;
        }

        // Lấy danh sách địa chỉ nếu user đã đăng nhập
        $diaChiList = [];
        $diaChiMacDinh = null;
        
        if (Session::has('user_id')) {
            $diaChiList = $this->diaChiModel->layDanhSachTheoUser(Session::get('user_id'));
            $diaChiMacDinh = $this->diaChiModel->layDiaChiMacDinh(Session::get('user_id'));
        }

        $tongTien = $this->chiTietGioModel->tinhTongTien($gioHang['id']);
        $phiVanChuyen = 30000; // Phí cố định

        require_once dirname(__DIR__, 2) . '/views/client/thanh_toan/index.php';
    }

    /**
     * Xử lý đặt hàng
     */
    public function datHang(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /thanh-toan');
            exit;
        }

        // Lấy giỏ hàng
        $gioHang = $this->layGioHangHienTai();
        $chiTietGioList = $this->chiTietGioModel->layChiTietGioHang($gioHang['id']);
        
        if (empty($chiTietGioList)) {
            Session::flash('error', 'Giỏ hàng trống');
            header('Location: /gio-hang');
            exit;
        }

        // Kiểm tra tồn kho
        foreach ($chiTietGioList as $item) {
            if (!$this->phienBanModel->kiemTraTonKho($item['phien_ban_id'], $item['so_luong'])) {
                Session::flash('error', 'Sản phẩm "' . $item['ten_san_pham'] . '" không đủ số lượng trong kho');
                header('Location: /gio-hang');
                exit;
            }
        }

        // Tính toán
        $tongTien = $this->chiTietGioModel->tinhTongTien($gioHang['id']);
        $phiVanChuyen = 30000;
        $tienGiamGia = 0;
        $maGiamGiaId = null;

        // Áp dụng mã giảm giá
        if (!empty($_POST['ma_giam_gia'])) {
            $maGiamGia = $this->maGiamGiaModel->kiemTraMaGiamGia($_POST['ma_giam_gia'], $tongTien);
            if ($maGiamGia) {
                $tienGiamGia = $this->maGiamGiaModel->tinhTienGiam($maGiamGia, $tongTien);
                $maGiamGiaId = $maGiamGia['id'];
            }
        }

        $tongThanhToan = $tongTien + $phiVanChuyen - $tienGiamGia;

        // Xử lý địa chỉ
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
            // Khách vãng lai
            $thongTinGuest = json_encode([
                'ten' => $_POST['ten_nguoi_nhan'] ?? '',
                'sdt' => $_POST['sdt_nhan'] ?? '',
                'dia_chi' => $_POST['dia_chi'] ?? ''
            ]);
        }

        // Tạo đơn hàng
        $maDonHang = 'DH' . date('YmdHis');
        $phuongThucThanhToan = $_POST['phuong_thuc_thanh_toan'] ?? 'COD';
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

        // Tạo chi tiết đơn hàng
        foreach ($chiTietGioList as $item) {
            $this->chiTietDonModel->themChiTiet(
                $donHangId,
                $item['phien_ban_id'],
                $item['so_luong'],
                $item['gia_ban']
            );

            // Giảm tồn kho
            $this->phienBanModel->giamTonKho($item['phien_ban_id'], $item['so_luong']);
        }

        // Tạo thông tin thanh toán
        $this->thanhToanModel->taoThanhToan($donHangId, $phuongThucThanhToan, $tongThanhToan);

        // Cập nhật số lượt dùng mã giảm giá
        if ($maGiamGiaId) {
            $this->maGiamGiaModel->tangSoLuotDung($maGiamGiaId);
        }

        // Xóa giỏ hàng
        $this->chiTietGioModel->xoaTatCa($gioHang['id']);

        Session::flash('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $maDonHang);
        header('Location: /don-hang/' . $donHangId);
        exit;
    }

    /**
     * Kiểm tra mã giảm giá (AJAX)
     */
    public function kiemTraMaGiamGia(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $maCode = $_POST['ma_code'] ?? '';
        $tongTien = isset($_POST['tong_tien']) ? (float)$_POST['tong_tien'] : 0;

        $maGiamGia = $this->maGiamGiaModel->kiemTraMaGiamGia($maCode, $tongTien);

        if (!$maGiamGia) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ']);
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

    /**
     * Lấy giỏ hàng hiện tại
     */
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
}
