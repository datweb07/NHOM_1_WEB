<?php

class KhuyenMaiController
{
    private $khuyenMaiModel;
    private $baseModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/KhuyenMai.php';
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $this->khuyenMaiModel = new KhuyenMai();
        $this->baseModel = new BaseModel('khuyen_mai');
    }

    public function index(): void
    {
        $trangThai = isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : '';
        
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $danhSachKhuyenMai = $this->khuyenMaiModel->layDanhSach($trangThai, $limit, $offset);
        $totalKhuyenMai = $this->khuyenMaiModel->demKhuyenMai($trangThai);
        $totalPages = ceil($totalKhuyenMai / $limit);

        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';

        $data = [
            'trangThai' => $trangThai,
            'danhSachKhuyenMai' => $danhSachKhuyenMai,
            'totalKhuyenMai' => $totalKhuyenMai,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'success' => $success,
            'error' => $error,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/khuyen_mai/index.php';
    }

    public function create(array $old = [], array $errors = []): void
    {
        $data = [
            'old' => $old,
            'errors' => $errors,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/khuyen_mai/create.php';
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/khuyen-mai/them');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST);

        if (!empty($errors)) {
            $this->create($old, $errors);
            return;
        }

        $this->baseModel->create($payload);
        header('Location: /admin/khuyen-mai?success=created');
        exit;
    }

    public function edit($id, array $old = [], array $errors = []): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/khuyen-mai?error=invalid_id');
            exit;
        }

        $khuyenMai = $this->baseModel->getById($id);
        if (!$khuyenMai) {
            header('Location: /admin/khuyen-mai?error=not_found');
            exit;
        }

        $data = [
            'khuyenMai' => $khuyenMai,
            'old' => $old,
            'errors' => $errors,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/khuyen_mai/edit.php';
    }

    public function update($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/khuyen-mai');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/khuyen-mai?error=invalid_id');
            exit;
        }

        $khuyenMai = $this->baseModel->getById($id);
        if (!$khuyenMai) {
            header('Location: /admin/khuyen-mai?error=not_found');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST, $id);

        if (!empty($errors)) {
            $this->edit($id, $old, $errors);
            return;
        }

        $this->baseModel->update($id, $payload);
        header('Location: /admin/khuyen-mai?success=updated');
        exit;
    }

    public function delete($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/khuyen-mai?error=invalid_id');
            exit;
        }

        $khuyenMai = $this->baseModel->getById($id);
        if (!$khuyenMai) {
            header('Location: /admin/khuyen-mai?error=not_found');
            exit;
        }

        $this->khuyenMaiModel->xoaLienKetSanPham($id);
        
        $this->baseModel->delete($id);
        
        header('Location: /admin/khuyen-mai?success=deleted');
        exit;
    }

    public function linkProducts($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/khuyen-mai?error=invalid_id');
            exit;
        }

        $khuyenMai = $this->baseModel->getById($id);
        if (!$khuyenMai) {
            header('Location: /admin/khuyen-mai?error=not_found');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $sanPhamModel = new SanPham();
        $allProducts = $sanPhamModel->layDanhSachPhanTrang(null, 0, null, null, 1000, 0);

        $linkedProducts = $this->khuyenMaiModel->layDanhSachSanPhamLienKet($id);
        $linkedProductIds = array_column($linkedProducts, 'id');

        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';

        $data = [
            'khuyenMai' => $khuyenMai,
            'allProducts' => $allProducts,
            'linkedProductIds' => $linkedProductIds,
            'success' => $success,
            'error' => $error,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/khuyen_mai/link_products.php';
    }

    public function saveProductLinks($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/khuyen-mai/lien-ket-san-pham?id=' . $id);
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/khuyen-mai?error=invalid_id');
            exit;
        }

        $khuyenMai = $this->baseModel->getById($id);
        if (!$khuyenMai) {
            header('Location: /admin/khuyen-mai?error=not_found');
            exit;
        }

        $sanPhamIds = isset($_POST['san_pham_ids']) && is_array($_POST['san_pham_ids']) 
            ? array_map('intval', $_POST['san_pham_ids']) 
            : [];

        $this->khuyenMaiModel->xoaLienKetSanPham($id);

        if (!empty($sanPhamIds)) {
            $this->khuyenMaiModel->themLienKetSanPham($id, $sanPhamIds);
        }

        header('Location: /admin/khuyen-mai/lien-ket-san-pham?id=' . $id . '&success=links_saved');
        exit;
    }

    private function validatePayload(array $input, int $editingId = 0): array
    {
        $errors = [];

        $tenChuongTrinh = trim((string)($input['ten_chuong_trinh'] ?? ''));
        $loaiGiam = trim((string)($input['loai_giam'] ?? ''));
        $giaTriGiamRaw = trim((string)($input['gia_tri_giam'] ?? ''));
        $giamToiDaRaw = trim((string)($input['giam_toi_da'] ?? ''));
        $ngayBatDau = trim((string)($input['ngay_bat_dau'] ?? ''));
        $ngayKetThuc = trim((string)($input['ngay_ket_thuc'] ?? ''));

        if ($tenChuongTrinh === '') {
            $errors['ten_chuong_trinh'] = 'Tên chương trình không được để trống.';
        } elseif (mb_strlen($tenChuongTrinh) > 255) {
            $errors['ten_chuong_trinh'] = 'Tên chương trình không được vượt quá 255 ký tự.';
        }

        if ($loaiGiam === '') {
            $errors['loai_giam'] = 'Loại giảm không được để trống.';
        } elseif (!in_array($loaiGiam, ['PHAN_TRAM', 'SO_TIEN'], true)) {
            $errors['loai_giam'] = 'Loại giảm phải là PHAN_TRAM hoặc SO_TIEN.';
        }

        $giaTriGiam = null;
        if ($giaTriGiamRaw === '') {
            $errors['gia_tri_giam'] = 'Giá trị giảm không được để trống.';
        } elseif (!is_numeric($giaTriGiamRaw)) {
            $errors['gia_tri_giam'] = 'Giá trị giảm phải là số.';
        } else {
            $giaTriGiam = (float)$giaTriGiamRaw;
            if ($giaTriGiam <= 0) {
                $errors['gia_tri_giam'] = 'Giá trị giảm phải lớn hơn 0.';
            }
            
            if ($loaiGiam === 'PHAN_TRAM' && ($giaTriGiam < 0 || $giaTriGiam > 100)) {
                $errors['gia_tri_giam'] = 'Giá trị giảm phần trăm phải từ 0 đến 100.';
            }
        }

        $giamToiDa = null;
        if ($loaiGiam === 'PHAN_TRAM') {
            if ($giamToiDaRaw === '') {
                $errors['giam_toi_da'] = 'Giảm tối đa không được để trống khi loại giảm là phần trăm.';
            } elseif (!is_numeric($giamToiDaRaw)) {
                $errors['giam_toi_da'] = 'Giảm tối đa phải là số.';
            } else {
                $giamToiDa = (float)$giamToiDaRaw;
                if ($giamToiDa <= 0) {
                    $errors['giam_toi_da'] = 'Giảm tối đa phải lớn hơn 0.';
                }
            }
        } elseif ($giamToiDaRaw !== '' && is_numeric($giamToiDaRaw)) {
            $giamToiDa = (float)$giamToiDaRaw;
        }

        if ($ngayBatDau === '') {
            $errors['ngay_bat_dau'] = 'Ngày bắt đầu không được để trống.';
        }

        if ($ngayKetThuc === '') {
            $errors['ngay_ket_thuc'] = 'Ngày kết thúc không được để trống.';
        }

        if ($ngayBatDau !== '' && $ngayKetThuc !== '') {
            if (strtotime($ngayBatDau) >= strtotime($ngayKetThuc)) {
                $errors['ngay_ket_thuc'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
            }
        }

        $payload = [
            'ten_chuong_trinh' => addslashes($tenChuongTrinh),
            'loai_giam' => $loaiGiam,
            'gia_tri_giam' => $giaTriGiam,
            'giam_toi_da' => $giamToiDa,
            'ngay_bat_dau' => $ngayBatDau,
            'ngay_ket_thuc' => $ngayKetThuc,
        ];

        $old = [
            'ten_chuong_trinh' => $tenChuongTrinh,
            'loai_giam' => $loaiGiam,
            'gia_tri_giam' => $giaTriGiamRaw,
            'giam_toi_da' => $giamToiDaRaw,
            'ngay_bat_dau' => $ngayBatDau,
            'ngay_ket_thuc' => $ngayKetThuc,
        ];

        return [$payload, $errors, $old];
    }
}
