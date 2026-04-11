<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/ChiTietDon.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';

use DonHang;
use ChiTietDon;
use ThanhToan;
use App\Core\Session;

class DonHangController
{
    private DonHang $donHangModel;
    private ChiTietDon $chiTietDonModel;
    private ThanhToan $thanhToanModel;

    public function __construct()
    {
        $this->donHangModel = new DonHang();
        $this->chiTietDonModel = new ChiTietDon();
        $this->thanhToanModel = new ThanhToan();
    }

    public function danhSach(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Vui lòng đăng nhập để xem đơn hàng');
            header('Location: /client/auth/login');
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $donHangList = $this->donHangModel->layDonHangTheoUser(Session::get('user_id'), $limit, $offset);
        $tongDonHang = $this->donHangModel->demDonHangTheoUser(Session::get('user_id'));
        $tongTrang = ceil($tongDonHang / $limit);

        require_once dirname(__DIR__, 2) . '/views/client/don_hang/index.php';
    }

    public function chiTiet(int $id): void
    {
        $donHang = $this->donHangModel->getById($id);

        if (!$donHang) {
            Session::flash('error', 'Đơn hàng không tồn tại');
            header('Location: /don-hang');
            exit;
        }

        if (Session::has('user_id') && $donHang['nguoi_dung_id'] != Session::get('user_id')) {
            Session::flash('error', 'Bạn không có quyền xem đơn hàng này');
            header('Location: /don-hang');
            exit;
        }

        $chiTietDonList = $this->chiTietDonModel->layChiTietDonHang($id);
        $thanhToan = $this->thanhToanModel->layTheoDonHang($id);

        $diaChiGiaoHang = null;
        if (!empty($donHang['dia_chi_id'])) {
            require_once dirname(__DIR__, 2) . '/models/entities/DiaChi.php';
            $diaChiModel = new \DiaChi();
            $diaChiGiaoHang = $diaChiModel->getById($donHang['dia_chi_id']);
        }

        require_once dirname(__DIR__, 2) . '/views/client/don_hang/detail.php';
    }

    public function huy(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /don-hang');
            exit;
        }

        if (!Session::has('user_id')) {
            Session::flash('error', 'Vui lòng đăng nhập');
            header('Location: /client/auth/login');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $donHang = $this->donHangModel->getById($id);

        if (!$donHang) {
            Session::flash('error', 'Đơn hàng không tồn tại');
            header('Location: /don-hang');
            exit;
        }


        if ($donHang['nguoi_dung_id'] != Session::get('user_id')) {
            Session::flash('error', 'Bạn không có quyền hủy đơn hàng này');
            header('Location: /don-hang');
            exit;
        }


        if ($donHang['trang_thai'] !== 'CHO_DUYET') {
            Session::flash('error', 'Không thể hủy đơn hàng đã được xác nhận');
            header('Location: /don-hang/' . $id);
            exit;
        }


        $this->donHangModel->update($id, ['trang_thai' => 'DA_HUY']);


        $chiTietDonList = $this->chiTietDonModel->layChiTietDonHang($id);
        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        $phienBanModel = new \PhienBanSanPham();
        
        foreach ($chiTietDonList as $item) {
            $phienBanModel->tangTonKho($item['phien_ban_id'], $item['so_luong']);
        }

        Session::flash('success', 'Đã hủy đơn hàng thành công');
        header('Location: /don-hang/' . $id);
        exit;
    }
}
