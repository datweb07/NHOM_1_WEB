<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/GioHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/ChiTietGio.php';
require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';

use GioHang;
use ChiTietGio;
use PhienBanSanPham;
use App\Core\Session;

class GioHangController
{
    private GioHang $gioHangModel;
    private ChiTietGio $chiTietGioModel;
    private PhienBanSanPham $phienBanModel;

    public function __construct()
    {
        $this->gioHangModel = new GioHang();
        $this->chiTietGioModel = new ChiTietGio();
        $this->phienBanModel = new PhienBanSanPham();
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

    public function index(): void
    {
        $gioHang = $this->layGioHangHienTai();
        $chiTietGioList = $this->chiTietGioModel->layChiTietGioHang($gioHang['id']);
        $tongTien = $this->chiTietGioModel->tinhTongTien($gioHang['id']);
        
        require_once dirname(__DIR__, 2) . '/views/client/gio_hang/index.php';
    }

    public function them(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $phienBanId = isset($_POST['phien_ban_id']) ? (int)$_POST['phien_ban_id'] : 0;
        $soLuong = isset($_POST['so_luong']) ? max(1, (int)$_POST['so_luong']) : 1;

        if ($phienBanId <= 0) {
            Session::flash('error', 'Vui lòng chọn phiên bản sản phẩm');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        if (!$this->phienBanModel->kiemTraTonKho($phienBanId, $soLuong)) {
            Session::flash('error', 'Sản phẩm không đủ số lượng trong kho');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        $gioHang = $this->layGioHangHienTai();
        $success = $this->chiTietGioModel->themVaoGio($gioHang['id'], $phienBanId, $soLuong);

        if ($success) {
            Session::flash('success', 'Đã thêm sản phẩm vào giỏ hàng');
        } else {
            Session::flash('error', 'Không thể thêm sản phẩm vào giỏ hàng');
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }

    public function capNhat(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /gio-hang');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $soLuong = isset($_POST['so_luong']) ? max(1, (int)$_POST['so_luong']) : 1;

        if ($id > 0) {
            $this->chiTietGioModel->capNhatSoLuong($id, $soLuong);
            Session::flash('success', 'Đã cập nhật số lượng');
        }

        header('Location: /gio-hang');
        exit;
    }

    public function xoa(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /gio-hang');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            $this->chiTietGioModel->xoaKhoiGio($id);
            Session::flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
        }

        header('Location: /gio-hang');
        exit;
    }

    public function xoaTatCa(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /gio-hang');
            exit;
        }

        $gioHang = $this->layGioHangHienTai();
        $this->chiTietGioModel->xoaTatCa($gioHang['id']);
        Session::flash('success', 'Đã xóa tất cả sản phẩm khỏi giỏ hàng');

        header('Location: /gio-hang');
        exit;
    }

    public function demSanPham(): void
    {
        header('Content-Type: application/json');
        
        $gioHang = $this->layGioHangHienTai();
        $soLuong = $this->chiTietGioModel->demSanPham($gioHang['id']);
        
        echo json_encode(['count' => $soLuong]);
        exit;
    }
}
