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

        $input = $_POST;
        
        require_once dirname(__DIR__, 2) . '/services/cloudinary/CloudinaryService.php';
        $cloudinary = CloudinaryService::getInstance();

        $uniqueCode = time(); 
        $publicIdDesktop = 'banner_desktop_' . $uniqueCode;
        $publicIdMobile  = 'banner_mobile_' . $uniqueCode;

        if (isset($_FILES['hinh_anh_desktop']) && $_FILES['hinh_anh_desktop']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $cloudinary->uploadApi()->upload($_FILES['hinh_anh_desktop']['tmp_name'], [
                    'folder'    => 'banners',
                    'public_id' => $publicIdDesktop 
                ]);
                $input['hinh_anh_desktop'] = $uploadResult['secure_url']; 
            } catch (\Exception $e) { }
        }

        if (isset($_FILES['hinh_anh_mobile']) && $_FILES['hinh_anh_mobile']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $cloudinary->uploadApi()->upload($_FILES['hinh_anh_mobile']['tmp_name'], [
                    'folder'    => 'banners',
                    'public_id' => $publicIdMobile 
                ]);
                $input['hinh_anh_mobile'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {}
        }

        [$payload, $errors, $old] = $this->validatePayload($input);

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

        $input = $_POST;
        
        $input['hinh_anh_desktop'] = $input['hinh_anh_desktop'] ?? $banner['hinh_anh_desktop'];
        $input['hinh_anh_mobile']  = $input['hinh_anh_mobile']  ?? $banner['hinh_anh_mobile'];

        require_once dirname(__DIR__, 2) . '/services/cloudinary/CloudinaryService.php';
        $cloudinary = CloudinaryService::getInstance();

        $publicIdDesktop = 'banner_desktop_' . $id;
        $publicIdMobile  = 'banner_mobile_' . $id;

        if (isset($_FILES['hinh_anh_desktop']) && $_FILES['hinh_anh_desktop']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $cloudinary->uploadApi()->upload($_FILES['hinh_anh_desktop']['tmp_name'], [
                    'folder'     => 'banners',
                    'public_id'  => $publicIdDesktop,
                    'overwrite'  => true,       
                    'invalidate' => true       
                ]);
                $input['hinh_anh_desktop'] = $uploadResult['secure_url'];
                
                if (!empty($banner['hinh_anh_desktop']) && strpos($banner['hinh_anh_desktop'], $publicIdDesktop) === false) {
                    $this->deleteCloudinaryImage($banner['hinh_anh_desktop']);
                }
            } catch (\Exception $e) {}
        }

        if (isset($_FILES['hinh_anh_mobile']) && $_FILES['hinh_anh_mobile']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $cloudinary->uploadApi()->upload($_FILES['hinh_anh_mobile']['tmp_name'], [
                    'folder'     => 'banners',
                    'public_id'  => $publicIdMobile,
                    'overwrite'  => true,
                    'invalidate' => true
                ]);
                $input['hinh_anh_mobile'] = $uploadResult['secure_url'];
                
                if (!empty($banner['hinh_anh_mobile']) && strpos($banner['hinh_anh_mobile'], $publicIdMobile) === false) {
                    $this->deleteCloudinaryImage($banner['hinh_anh_mobile']);
                }
            } catch (\Exception $e) {}
        }

        [$payload, $errors, $old] = $this->validatePayload($input, $id);

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

        $this->deleteCloudinaryImage($banner['hinh_anh_desktop']);
        $this->deleteCloudinaryImage($banner['hinh_anh_mobile']);

        $this->baseModel->delete($id);
        
        header('Location: /admin/banner?success=deleted');
        exit;
    }

    private function deleteCloudinaryImage($url): void
    {
        if (empty($url) || strpos($url, 'cloudinary.com') === false) {
            return;
        }

        $urlPath = parse_url($url, PHP_URL_PATH);
        if (preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-zA-Z0-9]+$/', $urlPath, $matches)) {
            try {
                require_once dirname(__DIR__, 2) . '/services/cloudinary/CloudinaryService.php';
                $cloudinary = CloudinaryService::getInstance();
                $cloudinary->uploadApi()->destroy($matches[1], ['invalidate' => true]);
            } catch (\Exception $e) {

            }
        }
    }

    public function layDanhSachSanPham(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $sanPhamModel = new SanPham();
        
        $danhSachSanPham = $sanPhamModel->layTatCa();
        
        $result = array_map(function($sp) {
            return [
                'id' => (int)$sp['id'],
                'ten_san_pham' => $sp['ten_san_pham'],
                'slug' => $sp['slug']
            ];
        }, $danhSachSanPham);
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
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

        if ($tieuDe === '') {
            $errors['tieu_de'] = 'Tiêu đề không được để trống.';
        } elseif (mb_strlen($tieuDe) > 255) {
            $errors['tieu_de'] = 'Tiêu đề không được vượt quá 255 ký tự.';
        }

        if ($hinhAnhDesktop === '') {
            $errors['hinh_anh_desktop'] = 'Hình ảnh desktop không được để trống.';
        } elseif (mb_strlen($hinhAnhDesktop) > 500) {
            $errors['hinh_anh_desktop'] = 'Link hình ảnh desktop không được vượt quá 500 ký tự.';
        }

        if ($hinhAnhMobile !== '' && mb_strlen($hinhAnhMobile) > 500) {
            $errors['hinh_anh_mobile'] = 'Link hình ảnh mobile không được vượt quá 500 ký tự.';
        }

        if ($linkDich === '') {
            $errors['link_dich'] = 'Link đích không được để trống.';
        } elseif (mb_strlen($linkDich) > 500) {
            $errors['link_dich'] = 'Link đích không được vượt quá 500 ký tự.';
        }

        $validViTri = ['HOME_HERO', 'HOME_MID', 'HOME_SIDE', 'FLOATING_BOTTOM_LEFT', 'POPUP', 'CATEGORY_TOP'];
        if ($viTri === '') {
            $errors['vi_tri'] = 'Vị trí không được để trống.';
        } elseif (!in_array($viTri, $validViTri, true)) {
            $errors['vi_tri'] = 'Vị trí không hợp lệ.';
        }

        if (!is_numeric($thuTu)) {
            $errors['thu_tu'] = 'Thứ tự phải là số.';
        }

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