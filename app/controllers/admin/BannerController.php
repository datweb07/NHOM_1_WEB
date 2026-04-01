<?php

class BannerController
{
    private $bannerModel;
    private $baseModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/BannerQuangCao.php';
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $this->bannerModel = new BannerQuangCao();
        $this->baseModel = new BaseModel('banner_quang_cao');
    }

    public function index(): void
    {
        $viTri = isset($_GET['vi_tri']) ? trim($_GET['vi_tri']) : '';
        $trangThai = isset($_GET['trang_thai']) ? (int)$_GET['trang_thai'] : -1;
        
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $danhSachBanner = $this->bannerModel->layDanhSach($viTri, $trangThai, $limit, $offset);
        $totalBanner = $this->bannerModel->demBanner($viTri, $trangThai);
        $totalPages = ceil($totalBanner / $limit);

        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';

        $data = [
            'viTri' => $viTri,
            'trangThai' => $trangThai,
            'danhSachBanner' => $danhSachBanner,
            'totalBanner' => $totalBanner,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'success' => $success,
            'error' => $error,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/banner/index.php';
    }

    public function create(array $old = [], array $errors = []): void
    {
        $data = [
            'old' => $old,
            'errors' => $errors,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/banner/create.php';
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/banner/them');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST);

        if (!empty($errors)) {
            $this->create($old, $errors);
            return;
        }

        $this->baseModel->create($payload);
        header('Location: /admin/banner?success=created');
        exit;
    }

    public function edit($id, array $old = [], array $errors = []): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/banner?error=invalid_id');
            exit;
        }

        $banner = $this->baseModel->getById($id);
        if (!$banner) {
            header('Location: /admin/banner?error=not_found');
            exit;
        }

        $data = [
            'banner' => $banner,
            'old' => $old,
            'errors' => $errors,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/banner/edit.php';
    }

    public function update($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/banner');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/banner?error=invalid_id');
            exit;
        }

        $banner = $this->baseModel->getById($id);
        if (!$banner) {
            header('Location: /admin/banner?error=not_found');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST, $id);

        if (!empty($errors)) {
            $this->edit($id, $old, $errors);
            return;
        }

        $this->baseModel->update($id, $payload);
        header('Location: /admin/banner?success=updated');
        exit;
    }

    public function delete($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/banner?error=invalid_id');
            exit;
        }

        $banner = $this->baseModel->getById($id);
        if (!$banner) {
            header('Location: /admin/banner?error=not_found');
            exit;
        }

        $this->baseModel->delete($id);
        
        header('Location: /admin/banner?success=deleted');
        exit;
    }

    private function validatePayload(array $input, int $editingId = 0): array
    {
        $errors = [];

        $tieuDe = trim((string)($input['tieu_de'] ?? ''));
        $hinhAnhDesktop = trim((string)($input['hinh_anh_desktop'] ?? ''));
        $hinhAnhMobile = trim((string)($input['hinh_anh_mobile'] ?? ''));
        $linkDich = trim((string)($input['link_dich'] ?? ''));
        $viTri = trim((string)($input['vi_tri'] ?? ''));
        $thuTu = trim((string)($input['thu_tu'] ?? '0'));
        $ngayBatDau = trim((string)($input['ngay_bat_dau'] ?? ''));
        $ngayKetThuc = trim((string)($input['ngay_ket_thuc'] ?? ''));
        $trangThai = isset($input['trang_thai']) ? (int)$input['trang_thai'] : 1;

        // Validate tieu_de
        if ($tieuDe === '') {
            $errors['tieu_de'] = 'Tiêu đề không được để trống.';
        } elseif (mb_strlen($tieuDe) > 255) {
            $errors['tieu_de'] = 'Tiêu đề không được vượt quá 255 ký tự.';
        }

        // Validate hinh_anh_desktop
        if ($hinhAnhDesktop === '') {
            $errors['hinh_anh_desktop'] = 'Hình ảnh desktop không được để trống.';
        } elseif (mb_strlen($hinhAnhDesktop) > 500) {
            $errors['hinh_anh_desktop'] = 'Link hình ảnh desktop không được vượt quá 500 ký tự.';
        }

        // Validate hinh_anh_mobile (optional)
        if ($hinhAnhMobile !== '' && mb_strlen($hinhAnhMobile) > 500) {
            $errors['hinh_anh_mobile'] = 'Link hình ảnh mobile không được vượt quá 500 ký tự.';
        }

        // Validate link_dich
        if ($linkDich === '') {
            $errors['link_dich'] = 'Link đích không được để trống.';
        } elseif (mb_strlen($linkDich) > 500) {
            $errors['link_dich'] = 'Link đích không được vượt quá 500 ký tự.';
        }

        // Validate vi_tri
        $validViTri = ['HOME_HERO', 'HOME_SIDE', 'FLOATING_BOTTOM_LEFT', 'POPUP', 'CATEGORY_TOP'];
        if ($viTri === '') {
            $errors['vi_tri'] = 'Vị trí không được để trống.';
        } elseif (!in_array($viTri, $validViTri, true)) {
            $errors['vi_tri'] = 'Vị trí không hợp lệ.';
        }

        // Validate thu_tu
        if (!is_numeric($thuTu)) {
            $errors['thu_tu'] = 'Thứ tự phải là số.';
        }

        // Validate date range
        if ($ngayBatDau !== '' && $ngayKetThuc !== '') {
            if (strtotime($ngayBatDau) >= strtotime($ngayKetThuc)) {
                $errors['ngay_ket_thuc'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
            }
        }

        $payload = [
            'tieu_de' => addslashes($tieuDe),
            'hinh_anh_desktop' => addslashes($hinhAnhDesktop),
            'hinh_anh_mobile' => $hinhAnhMobile !== '' ? addslashes($hinhAnhMobile) : null,
            'link_dich' => addslashes($linkDich),
            'vi_tri' => $viTri,
            'thu_tu' => (int)$thuTu,
            'ngay_bat_dau' => $ngayBatDau !== '' ? $ngayBatDau : null,
            'ngay_ket_thuc' => $ngayKetThuc !== '' ? $ngayKetThuc : null,
            'trang_thai' => $trangThai,
        ];

        $old = [
            'tieu_de' => $tieuDe,
            'hinh_anh_desktop' => $hinhAnhDesktop,
            'hinh_anh_mobile' => $hinhAnhMobile,
            'link_dich' => $linkDich,
            'vi_tri' => $viTri,
            'thu_tu' => $thuTu,
            'ngay_bat_dau' => $ngayBatDau,
            'ngay_ket_thuc' => $ngayKetThuc,
            'trang_thai' => $trangThai,
        ];

        return [$payload, $errors, $old];
    }
}
