<?php

class SanPhamController
{
    private $sanPhamModel;

    public function __construct()
    {
        
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $this->sanPhamModel = new BaseModel('san_pham');
    }

    public function index()
    {
        
        $sql = "SELECT * FROM san_pham WHERE trang_thai = 'CON_BAN'";

        $keyword = trim($_GET['keyword'] ?? '');
        if (!empty($keyword)) {
            $safeKeyword = addslashes($keyword);
            $sql .= " AND ten_san_pham LIKE '%$safeKeyword%'";
        }

        $danhMucId = isset($_GET['danh_muc_id']) ? (int)$_GET['danh_muc_id'] : 0;
        if ($danhMucId > 0) {
            $sql .= " AND danh_muc_id = $danhMucId";
        }

       $hangSanXuat = $_GET['hang_san_xuat'] ?? [];
       if (!is_array($hangSanXuat) && !empty($hangSanXuat)) {
        $hangSanXuat = [$hangSanXuat];
       }
       if (!empty($hangSanXuat)) {
        $hangSafe = array_map('addslashes', $hangSanXuat);
        $hangList = "'" .implode("','", $hangSafe) . "'";
        $sql .= " AND hang_san_xuat IN ($hangList)"; 
        }

        $mucGia = $_GET['muc_gia'] ?? [];
        if (!is_array($mucGia) && !empty($mucGia)) {
            $mucGia = [$mucGia];
        }
        if (!empty($mucGia)) {
            $priceConditions = [];
            foreach ($mucGia as $gia) {
                if ($gia == 'duoi-2') $priceConditions[] = "gia_hien_thi < 2000000";
                if ($gia == '2-4') $priceConditions[] = "gia_hien_thi >= 2000000 AND gia_hien_thi <= 4000000";
                if ($gia == '4-7') $priceConditions[] = "gia_hien_thi >= 4000000 AND gia_hien_thi <= 7000000";
                if ($gia == '7-10') $priceConditions[] = "gia_hien_thi >= 7000000 AND gia_hien_thi <= 10000000";
                if ($gia == 'tren-10') $priceConditions[] = "gia_hien_thi >10000000";
            }
            if (!empty($priceConditions)) {
                $sql .= " AND (" . implode(" OR ", $priceConditions) . ")";
            }
        }

        $tinhNang = $_GET['tinh_nang'] ?? [];
        if (!is_array($tinhNang) && !empty($tinhNang)) {
            $tinhNang = [$tinhNang];
        }
        if (!empty($tinhNang)) {
            $tnConditions = [];
            foreach ($tinhNang as $tn) {
                if ($tn == 'bao_mat_van_tay') $tnConditions[] = "ts.gia_tri LIKE '%vân tay%'";
                if ($tn == 'nhan_dien_khuon_mat') $tnConditions[] = "ts.gia_tri LIKE '%khuôn mặt%'";
                if ($tn == 'sac_nhanh') $tnConditions[] = "ts.gia_tri LIKE '%sạc nhanh%'";
                if ($tn == 'chong_nuoc_bui') $tnConditions[] = "(ts.gia_tri LIKE '%chống nước%' OR ts.gia_tri LIKE '%chống bụi%')";
            }
            if (!empty($tnConditions)) {
                $sql .= " AND EXISTS (SELECT 1 FROM thong_so_ky_thuat ts WHERE ts.san_pham_id = san_pham.id AND (" . implode(" OR ", $tnConditions) . "))";
            }
        }

        $pin = $_GET['pin'] ?? [];
        if (!is_array($pin) && !empty($pin)) {
            $pin = [$pin];
        }
        if (!empty($pin)) {
            $pinConditions = [];
            foreach ($pin as $p) {
                if ($p == 'tren-2000') $pinConditions[] = "ts.gia_tri LIKE '%2%mAh%' OR ts.gia_tri LIKE '%3%mAh%' OR ts.gia_tri LIKE '%4%mAh%' OR ts.gia_tri LIKE '%5%mAh%' OR ts.gia_tri LIKE '%6%mAh%'";
                if ($p == 'tren-3000') $pinConditions[] = "ts.gia_tri LIKE '%3%mAh%' OR ts.gia_tri LIKE '%4%mAh%' OR ts.gia_tri LIKE '%5%mAh%' OR ts.gia_tri LIKE '%6%mAh%'";
                if ($p == 'tren-4000') $pinConditions[] = "ts.gia_tri LIKE '%4%mAh%' OR ts.gia_tri LIKE '%5%mAh%' OR ts.gia_tri LIKE '%6%mAh%'";
                if ($p == 'tren-5000') $pinConditions[] = "ts.gia_tri LIKE '%5%mAh%' OR ts.gia_tri LIKE '%6%mAh%' OR ts.gia_tri LIKE '%7%mAh%'";
            }
            if (!empty($pinConditions)) {
                $sql .= " AND EXISTS (SELECT 1 FROM thong_so_ky_thuat ts WHERE ts.san_pham_id = san_pham.id AND ts.ten_thong_so LIKE '%Pin%' AND (" . implode(" OR ", $pinConditions) . "))";
            }
        }

        $traGop = $_GET['tra_gop'] ?? [];
        if (!is_array($traGop) && !empty($traGop)) {
            $traGop = [$traGop];
        }
        if (!empty($traGop)) {
            $tgConditions = [];
            foreach ($traGop as $tg) {
                if ($tg == '0d') $tgConditions[] = "mo_ta LIKE '%trả góp 0đ%'";
                if ($tg == '0-phan-tram') $tgConditions[] = "mo_ta LIKE '%trả góp 0%%'";
                if ($tg == '0d-0-phan-tram') $tgConditions[] = "(mo_ta LIKE '%trả góp 0đ%' AND mo_ta LIKE '%0%%')";
            }
            if (!empty($tgConditions)) {
                $sql .= " AND (" . implode(" OR ", $tgConditions) . ")";
            }
        }

        $sql .= " ORDER BY noi_bat DESC, id DESC";

        $danhSachSanPham = $this->sanPhamModel->query($sql);

        $data = [
            'danhSachSanPham' => $danhSachSanPham,
            'keyword'         => $keyword,
            'danhMucId'       => $danhMucId,
            'mucGia'          => $mucGia,
            'hangSanXuat'     => $hangSanXuat,
            'tinhNang'        => $tinhNang,
            'pin'             => $pin,
            'traGop'          => $traGop
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/client/san_pham/list.php';
    }
}
