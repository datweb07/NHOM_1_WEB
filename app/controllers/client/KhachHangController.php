<?php

require_once dirname(__DIR__, 2) . '/core/Session.php';
require_once dirname(__DIR__, 2) . '/models/roles/KhachHang.php';
require_once dirname(__DIR__, 2) . '/enums/TrangThaiDon.php';

class KhachHangController
{
    private KhachHang $khachHangModel;

    public function __construct()
    {
        \App\Core\Session::start();
        $this->khachHangModel = new KhachHang();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /dang-nhap');
            exit;
        }
        
        if (method_exists($this->khachHangModel, 'setId')) {
            $this->khachHangModel->setId($_SESSION['user_id']);
        } else {
            $this->khachHangModel->id = $_SESSION['user_id'];
        }
    }

    public function lichSuDonHang(): void
    {
        // Lấy danh sách 50 đơn hàng gần nhất
        $danhSachDonHang = $this->khachHangModel->xem_lich_su_don(50);

        $data = [
            'danhSachDonHang' => $danhSachDonHang,
            'title'           => 'Lịch sử mua hàng'
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/client/khach_hang/lich_su_don_hang.php';
    }

    public function chiTietDonHang($donHangId): void
    {
        $donHangId = (int)$donHangId;
        if ($donHangId <= 0) {
            header('Location: /khach-hang/lich-su-don?error=invalid_id');
            exit;
        }

        $userId = $_SESSION['user_id'];

        $sql = "CALL DH_XemDonHang($donHangId, $userId)";
        $link = $this->khachHangModel->link; // Lấy connection từ BaseModel
        
        $donHang = null;
        $chiTietDon = [];

        if (mysqli_multi_query($link, $sql)) {
            if ($result = mysqli_store_result($link)) {
                $donHang = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
            }

            if (mysqli_more_results($link) && mysqli_next_result($link)) {
                if ($result = mysqli_store_result($link)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $chiTietDon[] = $row;
                    }
                    mysqli_free_result($result);
                }
            }
            
            while (mysqli_more_results($link) && mysqli_next_result($link)) {
                if ($res = mysqli_store_result($link)) {
                    mysqli_free_result($res);
                }
            }
        }

        if (!$donHang) {
            header('Location: /khach-hang/lich-su-don?error=not_found_or_unauthorized');
            exit;
        }

        $data = [
            'donHang'    => $donHang,
            'chiTietDon' => $chiTietDon,
            'title'      => 'Chi tiết đơn hàng #' . $donHang['ma_don_hang']
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/client/khach_hang/chi_tiet_don_hang.php';
    }
}
?>