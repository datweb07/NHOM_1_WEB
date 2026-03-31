<?php

class MaGiamGiaController
{
    private $maGiamGiaModel;
    private $baseModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/MaGiamGia.php';
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $this->maGiamGiaModel = new MaGiamGia();
        $this->baseModel = new BaseModel('ma_giam_gia');
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

        $danhSachMaGiamGia = $this->maGiamGiaModel->layDanhSach($trangThai, $limit, $offset);
        $totalMaGiamGia = $this->maGiamGiaModel->demMaGiamGia($trangThai);
        $totalPages = ceil($totalMaGiamGia / $limit);

        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';

        $data = [
            'trangThai' => $trangThai,
            'danhSachMaGiamGia' => $danhSachMaGiamGia,
            'totalMaGiamGia' => $totalMaGiamGia,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'success' => $success,
            'error' => $error,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/ma_giam_gia/index.php';
    }

    public function create(array $old = [], array $errors = []): void
    {
        $data = [
            'old' => $old,
            'errors' => $errors,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/ma_giam_gia/create.php';
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/ma-giam-gia/them');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST);

        if (!empty($errors)) {
            $this->create($old, $errors);
            return;
        }

        $this->baseModel->create($payload);
        header('Location: /admin/ma-giam-gia?success=created');
        exit;
    }

    public function edit($id, array $old = [], array $errors = []): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/ma-giam-gia?error=invalid_id');
            exit;
        }

        $maGiamGia = $this->baseModel->getById($id);
        if (!$maGiamGia) {
            header('Location: /admin/ma-giam-gia?error=not_found');
            exit;
        }

        $data = [
            'maGiamGia' => $maGiamGia,
            'old' => $old,
            'errors' => $errors,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/ma_giam_gia/edit.php';
    }

    public function update($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/ma-giam-gia');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/ma-giam-gia?error=invalid_id');
            exit;
        }

        $maGiamGia = $this->baseModel->getById($id);
        if (!$maGiamGia) {
            header('Location: /admin/ma-giam-gia?error=not_found');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST, $id);

        if (!empty($errors)) {
            $this->edit($id, $old, $errors);
            return;
        }

        $this->baseModel->update($id, $payload);
        header('Location: /admin/ma-giam-gia?success=updated');
        exit;
    }

    public function delete($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/ma-giam-gia?error=invalid_id');
            exit;
        }

        $maGiamGia = $this->baseModel->getById($id);
        if (!$maGiamGia) {
            header('Location: /admin/ma-giam-gia?error=not_found');
            exit;
        }

        $this->baseModel->delete($id);
        
        header('Location: /admin/ma-giam-gia?success=deleted');
        exit;
    }

    private function validatePayload(array $input, int $editingId = 0): array
    {
        $errors = [];

        $maCode = trim((string)($input['ma_code'] ?? ''));
        $moTa = trim((string)($input['mo_ta'] ?? ''));
        $loaiGiam = trim((string)($input['loai_giam'] ?? ''));
        $giaTriGiamRaw = trim((string)($input['gia_tri_giam'] ?? ''));
        $giamToiDaRaw = trim((string)($input['giam_toi_da'] ?? ''));
        $donToiThieuRaw = trim((string)($input['don_toi_thieu'] ?? ''));
        $gioiHanSuDungRaw = trim((string)($input['gioi_han_su_dung'] ?? ''));
        $ngayBatDau = trim((string)($input['ngay_bat_dau'] ?? ''));
        $ngayKetThuc = trim((string)($input['ngay_ket_thuc'] ?? ''));

        // Validate ma_code
        if ($maCode === '') {
            $errors['ma_code'] = 'Mã code không được để trống.';
        } elseif (!preg_match('/^[A-Z0-9]+$/', $maCode)) {
            $errors['ma_code'] = 'Mã code chỉ được chứa chữ in hoa và số.';
        } elseif (mb_strlen($maCode) > 50) {
            $errors['ma_code'] = 'Mã code không được vượt quá 50 ký tự.';
        } elseif ($this->maGiamGiaModel->kiemTraMaCode($maCode, $editingId)) {
            $errors['ma_code'] = 'Mã code đã tồn tại.';
        }

        // Validate loai_giam
        if ($loaiGiam === '') {
            $errors['loai_giam'] = 'Loại giảm không được để trống.';
        } elseif (!in_array($loaiGiam, ['PHAN_TRAM', 'SO_TIEN'], true)) {
            $errors['loai_giam'] = 'Loại giảm phải là PHAN_TRAM hoặc SO_TIEN.';
        }

        // Validate gia_tri_giam
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
            
            // If percentage, must be 0-100
            if ($loaiGiam === 'PHAN_TRAM' && ($giaTriGiam < 0 || $giaTriGiam > 100)) {
                $errors['gia_tri_giam'] = 'Giá trị giảm phần trăm phải từ 0 đến 100.';
            }
        }

        // Validate giam_toi_da (optional for percentage)
        $giamToiDa = null;
        if ($giamToiDaRaw !== '') {
            if (!is_numeric($giamToiDaRaw)) {
                $errors['giam_toi_da'] = 'Giảm tối đa phải là số.';
            } else {
                $giamToiDa = (float)$giamToiDaRaw;
                if ($giamToiDa <= 0) {
                    $errors['giam_toi_da'] = 'Giảm tối đa phải lớn hơn 0.';
                }
            }
        }

        // Validate don_toi_thieu (optional)
        $donToiThieu = 0;
        if ($donToiThieuRaw !== '') {
            if (!is_numeric($donToiThieuRaw)) {
                $errors['don_toi_thieu'] = 'Đơn tối thiểu phải là số.';
            } else {
                $donToiThieu = (float)$donToiThieuRaw;
                if ($donToiThieu < 0) {
                    $errors['don_toi_thieu'] = 'Đơn tối thiểu phải lớn hơn hoặc bằng 0.';
                }
            }
        }

        // Validate gioi_han_su_dung (optional, NULL = unlimited)
        $gioiHanSuDung = null;
        if ($gioiHanSuDungRaw !== '') {
            if (!is_numeric($gioiHanSuDungRaw)) {
                $errors['gioi_han_su_dung'] = 'Giới hạn sử dụng phải là số.';
            } else {
                $gioiHanSuDung = (int)$gioiHanSuDungRaw;
                if ($gioiHanSuDung <= 0) {
                    $errors['gioi_han_su_dung'] = 'Giới hạn sử dụng phải lớn hơn 0.';
                }
            }
        }

        // Validate ngay_bat_dau
        if ($ngayBatDau === '') {
            $errors['ngay_bat_dau'] = 'Ngày bắt đầu không được để trống.';
        }

        // Validate ngay_ket_thuc
        if ($ngayKetThuc === '') {
            $errors['ngay_ket_thuc'] = 'Ngày kết thúc không được để trống.';
        }

        // Validate date range
        if ($ngayBatDau !== '' && $ngayKetThuc !== '') {
            if (strtotime($ngayBatDau) >= strtotime($ngayKetThuc)) {
                $errors['ngay_ket_thuc'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
            }
        }

        $payload = [
            'ma_code' => addslashes($maCode),
            'mo_ta' => addslashes($moTa),
            'loai_giam' => $loaiGiam,
            'gia_tri_giam' => $giaTriGiam,
            'giam_toi_da' => $giamToiDa,
            'don_toi_thieu' => $donToiThieu,
            'gioi_han_su_dung' => $gioiHanSuDung,
            'ngay_bat_dau' => $ngayBatDau,
            'ngay_ket_thuc' => $ngayKetThuc,
        ];

        $old = [
            'ma_code' => $maCode,
            'mo_ta' => $moTa,
            'loai_giam' => $loaiGiam,
            'gia_tri_giam' => $giaTriGiamRaw,
            'giam_toi_da' => $giamToiDaRaw,
            'don_toi_thieu' => $donToiThieuRaw,
            'gioi_han_su_dung' => $gioiHanSuDungRaw,
            'ngay_bat_dau' => $ngayBatDau,
            'ngay_ket_thuc' => $ngayKetThuc,
        ];

        return [$payload, $errors, $old];
    }
}
