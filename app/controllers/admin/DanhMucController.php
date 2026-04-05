<?php

class DanhMucController
{
    private $danhMucModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/DanhMuc.php';
        $this->danhMucModel = new DanhMuc();
    }

    public function index(): void
    {
        $keyword = trim((string)($_GET['keyword'] ?? ''));
        $statusFilter = $_GET['trang_thai'] ?? 'all';

        $trangThai = $this->danhMucModel->buildFilter($statusFilter === 'all' ? -1 : (int)$statusFilter);

        $danhSachDanhMuc = $this->danhMucModel->layDanhSach($keyword, $trangThai);

        $data = [
            'danhSachDanhMuc' => $danhSachDanhMuc,
            'keyword' => $keyword,
            'statusFilter' => $statusFilter,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/danh_muc/index.php';
    }

    public function create(array $old = [], array $errors = []): void
    {
        $danhMucChaOptions = $this->danhMucModel->layDanhMucCha();

        $data = [
            'old' => $old,
            'errors' => $errors,
            'danhMucChaOptions' => $danhMucChaOptions,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/danh_muc/create.php';
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/danh-muc/them');
            exit;
        }

        $input = $_POST;

        // Xử lý upload ảnh Icon lên Cloudinary (Thêm mới)
        if (isset($_FILES['icon_url']) && $_FILES['icon_url']['error'] === UPLOAD_ERR_OK) {
            try {
                require_once dirname(__DIR__, 2) . '/services/cloudinary/CloudinaryService.php';
                $cloudinary = CloudinaryService::getInstance();

                // Tạo tên ngẫu nhiên bằng Timestamp vì chưa có ID
                $uniqueCode = time();
                $publicId = 'category_icon_' . $uniqueCode;

                $uploadResult = $cloudinary->uploadApi()->upload($_FILES['icon_url']['tmp_name'], [
                    'folder'    => 'categories',
                    'public_id' => $publicId
                ]);
                $input['icon_url'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                // Có thể log lỗi ở đây nếu cần
            }
        }

        [$payload, $errors, $old] = $this->validatePayload($input);

        if (!empty($errors)) {
            $this->create($old, $errors);
            return;
        }

        $this->danhMucModel->create($payload);
        header('Location: /admin/danh-muc?success=created');
        exit;
    }

    public function edit($id, array $old = [], array $errors = []): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/danh-muc?error=invalid_id');
            exit;
        }

        $danhMuc = $this->danhMucModel->getById($id);
        if (!$danhMuc) {
            header('Location: /admin/danh-muc?error=not_found');
            exit;
        }

        $danhMucChaOptions = $this->danhMucModel->layDanhMucCha($id);

        $data = [
            'danhMuc' => $danhMuc,
            'old' => $old,
            'errors' => $errors,
            'danhMucChaOptions' => $danhMucChaOptions,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/danh_muc/edit.php';
    }

    public function update($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/danh-muc');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/danh-muc?error=invalid_id');
            exit;
        }

        $danhMuc = $this->danhMucModel->getById($id);
        if (!$danhMuc) {
            header('Location: /admin/danh-muc?error=not_found');
            exit;
        }

        $input = $_POST;
        
        // Mặc định giữ lại ảnh cũ nếu user không upload file mới
        $input['icon_url'] = $input['icon_url'] ?? $danhMuc['icon_url'];

        // Xử lý cập nhật ảnh lên Cloudinary
        if (isset($_FILES['icon_url']) && $_FILES['icon_url']['error'] === UPLOAD_ERR_OK) {
            try {
                require_once dirname(__DIR__, 2) . '/services/cloudinary/CloudinaryService.php';
                $cloudinary = CloudinaryService::getInstance();

                $publicId = 'category_icon_' . $id;

                $uploadResult = $cloudinary->uploadApi()->upload($_FILES['icon_url']['tmp_name'], [
                    'folder'     => 'categories',
                    'public_id'  => $publicId,
                    'overwrite'  => true,       // Ghi đè file nếu đã tồn tại tên này
                    'invalidate' => true        // Xóa cache trình duyệt
                ]);
                $input['icon_url'] = $uploadResult['secure_url'];

                // DỌN RÁC: Xóa ảnh cũ nếu tên của nó là Timestamp (không khớp với ID chuẩn)
                if (!empty($danhMuc['icon_url']) && strpos($danhMuc['icon_url'], $publicId) === false) {
                    $this->deleteCloudinaryImage($danhMuc['icon_url']);
                }
            } catch (\Exception $e) {}
        }

        [$payload, $errors, $old] = $this->validatePayload($input, $id);

        if (!empty($errors)) {
            $this->edit($id, $old, $errors);
            return;
        }

        $this->danhMucModel->update($id, $payload);
        header('Location: /admin/danh-muc?success=updated');
        exit;
    }

    public function xoa($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/danh-muc?error=invalid_id');
            exit;
        }

        if (!$this->danhMucModel->tonTaiDanhMuc($id)) {
            header('Location: /admin/danh-muc?error=not_found');
            exit;
        }

        // Check if category has products
        if ($this->danhMucModel->kiemTraCoSanPham($id)) {
            header('Location: /admin/danh-muc?error=has_products');
            exit;
        }

        // Lưu ý: Do logic xóa của bạn đang là "ẩn danh mục" (soft-delete) thông qua hàm anDanhMuc() 
        // Nên ta sẽ KHÔNG tự động xóa ảnh trên Cloudinary tại đây để đề phòng user khôi phục lại (hienDanhMuc).
        $this->danhMucModel->anDanhMuc($id);
        header('Location: /admin/danh-muc?success=hidden');
        exit;
    }

    public function hien($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/danh-muc?error=invalid_id');
            exit;
        }

        if (!$this->danhMucModel->tonTaiDanhMuc($id)) {
            header('Location: /admin/danh-muc?error=not_found');
            exit;
        }

        $this->danhMucModel->hienDanhMuc($id);
        header('Location: /admin/danh-muc?success=shown');
        exit;
    }

    public function bulkUpdateStatus(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/danh-muc');
            exit;
        }

        $ids = $_POST['ids'] ?? [];
        $action = $_POST['action'] ?? '';

        if (empty($ids) || !is_array($ids)) {
            header('Location: /admin/danh-muc?error=no_selection');
            exit;
        }

        if (!in_array($action, ['hide', 'show'], true)) {
            header('Location: /admin/danh-muc?error=invalid_action');
            exit;
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($ids as $id) {
            $id = (int)$id;
            if ($id <= 0 || !$this->danhMucModel->tonTaiDanhMuc($id)) {
                $failedCount++;
                continue;
            }

            if ($action === 'hide') {
                $this->danhMucModel->anDanhMuc($id);
            } else {
                $this->danhMucModel->hienDanhMuc($id);
            }
            $successCount++;
        }

        $message = "Đã cập nhật $successCount danh mục";
        if ($failedCount > 0) {
            $message .= ", $failedCount thất bại";
        }

        header("Location: /admin/danh-muc?success=bulk_updated&message=" . urlencode($message));
        exit;
    }

    /**
     * Hàm hỗ trợ tự động xóa ảnh trên Cloudinary
     */
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
            } catch (\Exception $e) { }
        }
    }

    private function validatePayload(array $input, int $editingId = 0): array
    {
        $errors = [];

        $ten = trim((string)($input['ten'] ?? ''));
        $slugInput = trim((string)($input['slug'] ?? ''));
        $iconUrl = trim((string)($input['icon_url'] ?? ''));
        $thuTuRaw = trim((string)($input['thu_tu'] ?? '0'));
        $trangThaiRaw = (string)($input['trang_thai'] ?? '1');
        $danhMucChaRaw = (string)($input['danh_muc_cha_id'] ?? '');

        if ($ten === '') {
            $errors['ten'] = 'Tên danh mục không được để trống.';
        }

        $slug = $slugInput !== '' ? $slugInput : $this->slugify($ten);
        if ($slug === '') {
            $errors['slug'] = 'Slug không hợp lệ.';
        }

        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $errors['slug'] = 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.';
        }

        if ($this->danhMucModel->tonTaiSlug($slug, $editingId)) {
            $errors['slug'] = 'Slug đã tồn tại, vui lòng dùng slug khác.';
        }

        $thuTu = 0;
        if ($thuTuRaw !== '') {
            if (!preg_match('/^\d+$/', $thuTuRaw)) {
                $errors['thu_tu'] = 'Thứ tự phải là số nguyên không âm.';
            } else {
                $thuTu = (int)$thuTuRaw;
            }
        }

        $trangThai = ($trangThaiRaw === '0') ? 0 : 1;

        $danhMucChaId = null;
        if ($danhMucChaRaw !== '') {
            if (!ctype_digit($danhMucChaRaw)) {
                $errors['danh_muc_cha_id'] = 'Danh mục cha không hợp lệ.';
            } else {
                $danhMucChaId = (int)$danhMucChaRaw;

                if ($editingId > 0 && $danhMucChaId === $editingId) {
                    $errors['danh_muc_cha_id'] = 'Danh mục cha không thể là chính nó.';
                } elseif (!$this->danhMucModel->tonTaiDanhMuc($danhMucChaId)) {
                    $errors['danh_muc_cha_id'] = 'Danh mục cha không tồn tại.';
                }
            }
        }

        $payload = [
            'ten' => addslashes($ten),
            'slug' => addslashes($slug),
            'icon_url' => addslashes($iconUrl),
            'danh_muc_cha_id' => $danhMucChaId,
            'thu_tu' => $thuTu,
            'trang_thai' => $trangThai,
        ];

        $old = [
            'ten' => $ten,
            'slug' => $slugInput,
            'icon_url' => $iconUrl,
            'danh_muc_cha_id' => $danhMucChaRaw,
            'thu_tu' => $thuTuRaw,
            'trang_thai' => $trangThaiRaw,
        ];

        return [$payload, $errors, $old];
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }

        $text = preg_replace('/[^a-z0-9\s-]/', '', $text) ?? '';
        $text = preg_replace('/[\s-]+/', '-', $text) ?? '';
        return trim($text, '-');
    }
}