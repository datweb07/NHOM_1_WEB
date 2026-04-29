<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/KhuyenMai.php';
require_once dirname(__DIR__, 2) . '/models/entities/MaGiamGia.php';

class KhuyenMaiController
{
    private $khuyenMaiModel;
    private $maGiamGiaModel;

    public function __construct()
    {
        $this->khuyenMaiModel = new \KhuyenMai();
        $this->maGiamGiaModel = new \MaGiamGia();
    }

    public function danhSachKhuyenMai(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $khuyenMais = $this->layKhuyenMaiHoatDong($limit, $offset);
        $tongKhuyenMai = $this->demKhuyenMaiHoatDong();
        $tongTrang = ceil($tongKhuyenMai / $limit);

        require_once dirname(__DIR__, 2) . '/views/client/khuyen_mai/index.php';
    }

    public function chiTietKhuyenMai(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header('Location: /khuyen-mai');
            exit;
        }

        $khuyenMai = $this->khuyenMaiModel->getById($id);

        if (!$khuyenMai || $khuyenMai['trang_thai'] !== 'HOAT_DONG') {
            header('Location: /khuyen-mai');
            exit;
        }

        $sanPhams = $this->laySanPhamKhuyenMai($id);

        require_once dirname(__DIR__, 2) . '/views/client/khuyen_mai/detail.php';
    }

    public function danhSachMaGiamGia(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $maGiamGias = $this->layMaGiamGiaHoatDong($limit, $offset);
        $tongMaGiamGia = $this->demMaGiamGiaHoatDong();
        $tongTrang = ceil($tongMaGiamGia / $limit);

        require_once dirname(__DIR__, 2) . '/views/client/ma_giam_gia/index.php';
    }

    private function layKhuyenMaiHoatDong(int $limit, int $offset): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT * FROM khuyen_mai
                WHERE trang_thai = 'HOAT_DONG'
                  AND (ngay_bat_dau IS NULL OR ngay_bat_dau <= NOW())
                  AND (ngay_ket_thuc IS NULL OR ngay_ket_thuc >= NOW())
                                ORDER BY ngay_bat_dau DESC, id DESC
                LIMIT $limit OFFSET $offset";

        return $this->khuyenMaiModel->query($sql);
    }

    private function demKhuyenMaiHoatDong(): int
    {
        $sql = "SELECT COUNT(*) as total FROM khuyen_mai
                WHERE trang_thai = 'HOAT_DONG'
                  AND (ngay_bat_dau IS NULL OR ngay_bat_dau <= NOW())
                  AND (ngay_ket_thuc IS NULL OR ngay_ket_thuc >= NOW())";

        $result = $this->khuyenMaiModel->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    private function laySanPhamKhuyenMai(int $khuyenMaiId): array
    {
        $khuyenMaiId = (int)$khuyenMaiId;

        $sql = "SELECT sp.*, 
                       (SELECT url_anh FROM hinh_anh_san_pham 
                        WHERE san_pham_id = sp.id AND la_anh_chinh = 1 
                        LIMIT 1) as anh_chinh
                FROM san_pham sp
                INNER JOIN san_pham_khuyen_mai spkm ON sp.id = spkm.san_pham_id
                WHERE spkm.khuyen_mai_id = $khuyenMaiId
                  AND sp.trang_thai = 'CON_BAN'
                ORDER BY sp.ten_san_pham ASC";

        return $this->khuyenMaiModel->query($sql);
    }

    private function layMaGiamGiaHoatDong(int $limit, int $offset): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT * FROM ma_giam_gia
                WHERE trang_thai = 'HOAT_DONG'
                  AND (ngay_bat_dau IS NULL OR ngay_bat_dau <= NOW())
                                    AND (ngay_ket_thuc IS NULL OR ngay_ket_thuc >= NOW())
                                    AND (gioi_han_su_dung IS NULL OR so_luot_da_dung < gioi_han_su_dung)
                                ORDER BY ngay_bat_dau DESC, id DESC
                LIMIT $limit OFFSET $offset";

        return $this->maGiamGiaModel->query($sql);
    }

    private function demMaGiamGiaHoatDong(): int
    {
        $sql = "SELECT COUNT(*) as total FROM ma_giam_gia
                WHERE trang_thai = 'HOAT_DONG'
                  AND (ngay_bat_dau IS NULL OR ngay_bat_dau <= NOW())
                                    AND (ngay_ket_thuc IS NULL OR ngay_ket_thuc >= NOW())
                                    AND (gioi_han_su_dung IS NULL OR so_luot_da_dung < gioi_han_su_dung)";

        $result = $this->maGiamGiaModel->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }
}
