<?php
require_once dirname(__DIR__, 2) . '/core/Session.php';
require_once dirname(__DIR__, 2) . '/models/BaseModel.php';

class KhachHangController
{
    private $donHangModel;

    public function __construct()
    {
        $this->donHangModel = new BaseModel('don_hang');
    }

    public function danhSachDonHangCuaToi()
    {
        \App\Core\Session::start();
       
        $userId = $_SESSION['user_id'] ?? null; 

        if (!$userId) {
            header("Location: /login.php");
            exit();
        }

        $safeUserId = (int)$userId;
        $sql = "SELECT id, ma_don_hang, tong_thanh_toan, trang_thai, ngay_tao 
                FROM don_hang 
                WHERE nguoi_dung_id = $safeUserId 
                ORDER BY id DESC";

        $danhSachDonHang = $this->donHangModel->query($sql);

        $data = [
            'danhSachDonHang' => $danhSachDonHang
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/client/don_hang/list.php';
    }
}
