<?php

require_once dirname(__DIR__, 2) . '/core/FileUpload.php';

use App\Core\FileUpload;

class SanPhamController 
{
    private $baseModel;
    private $sanPhamModel;

    public function __construct() 
    {
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $this->baseModel = new BaseModel('san_pham');
        $this->sanPhamModel = new SanPham();
    }

    public function index() 
    {
       
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $safeKeyword = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');

        
        $danhMucId = isset($_GET['danh_muc_id']) && is_numeric($_GET['danh_muc_id']) ? (int)$_GET['danh_muc_id'] : 0;
        $giaMin = isset($_GET['gia_min']) && is_numeric($_GET['gia_min']) ? (float)$_GET['gia_min'] : null;
        $giaMax = isset($_GET['gia_max']) && is_numeric($_GET['gia_max']) ? (float)$_GET['gia_max'] : null;

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $limit = 15; 
        $offset = ($page - 1) * $limit;

        $dbKeyword = addslashes($keyword);
        
        
        $whereConditions = [];
        if ($keyword !== '') {
            $whereConditions[] = "(sp.ten_san_pham LIKE '%$dbKeyword%' 
                                   OR sp.id = '$dbKeyword' 
                                   OR sp.hang_san_xuat LIKE '%$dbKeyword%')";
        }
        if ($danhMucId > 0) {
            $whereConditions[] = "sp.danh_muc_id = $danhMucId";
        }
        if ($giaMin !== null) {
            $whereConditions[] = "sp.gia_hien_thi >= $giaMin";
        }
        if ($giaMax !== null) {
            $whereConditions[] = "sp.gia_hien_thi <= $giaMax";
        }

        $whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";

        $sqlCount = "SELECT COUNT(*) as total FROM san_pham sp $whereClause";
        $resultCount = $this->baseModel->query($sqlCount);
        $totalProducts = !empty($resultCount) ? (int)$resultCount[0]['total'] : 0;
        $totalPages = ceil($totalProducts / $limit);

        $sqlSearch = "SELECT sp.*, dm.ten AS ten_danh_muc 
                      FROM san_pham sp
                      LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
                      $whereClause
                      ORDER BY sp.ngay_tao DESC
                      LIMIT $limit OFFSET $offset";
        
        $danhSachSanPham = $this->baseModel->query($sqlSearch);

        
        $sqlDanhMuc = "SELECT id, ten FROM danh_muc WHERE trang_thai = 1 ORDER BY thu_tu ASC, ten ASC";
        $danhSachDanhMuc = $this->baseModel->query($sqlDanhMuc);

        $data = [
            'keyword'         => $safeKeyword,
            'danhMucId'       => $danhMucId,
            'giaMin'          => $giaMin,
            'giaMax'          => $giaMax,
            'danhSachDanhMuc' => $danhSachDanhMuc,
            'danhSachSanPham' => $danhSachSanPham,
            'totalProducts'   => $totalProducts,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
            'limit'           => $limit
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/index.php';
    }

    
     
    public function xoa($id) 
    {
        $id = (int)$id; 
        if ($id <= 0) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }

        // Check if product has orders
        require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
        $sanPhamModel = new SanPham();
        if ($sanPhamModel->kiemTraCoDonHang($id)) {
            header("Location: /admin/san-pham?error=has_orders");
            exit;
        }
        
        $this->baseModel->update($id, ['trang_thai' => 'NGUNG_BAN']);
        $sqlPhienBan = "UPDATE phien_ban_san_pham SET trang_thai = 'NGUNG_BAN' WHERE san_pham_id = $id";
        $this->baseModel->query($sqlPhienBan);

        header("Location: /admin/san-pham?success=deleted");
        exit;
    }
    public function moBan($id) 
    {
        $id = (int)$id; 
        if ($id <= 0) {
            header("Location: /admin/san-pham?error=invalid_id");
            exit;
        }
        
        $this->baseModel->update($id, ['trang_thai' => 'CON_BAN']);
        $sqlPhienBan = "UPDATE phien_ban_san_pham 
                        SET trang_thai = CASE WHEN so_luong_ton > 0 THEN 'CON_HANG' ELSE 'HET_HANG' END 
                        WHERE san_pham_id = $id";
        $this->baseModel->query($sqlPhienBan);

        header("Location: /admin/san-pham?success=restored");
        exit;
    }

    public function bulkUpdateStatus(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/san-pham');
            exit;
        }

        $ids = $_POST['ids'] ?? [];
        $action = $_POST['action'] ?? '';

        if (empty($ids) || !is_array($ids)) {
            header('Location: /admin/san-pham?error=no_selection');
            exit;
        }

        if (!in_array($action, ['stop', 'resume'], true)) {
            header('Location: /admin/san-pham?error=invalid_action');
            exit;
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($ids as $id) {
            $id = (int)$id;
            if ($id <= 0) {
                $failedCount++;
                continue;
            }

            if ($action === 'stop') {
                $this->baseModel->update($id, ['trang_thai' => 'NGUNG_BAN']);
                $sqlPhienBan = "UPDATE phien_ban_san_pham SET trang_thai = 'NGUNG_BAN' WHERE san_pham_id = $id";
                $this->baseModel->query($sqlPhienBan);
            } else {
                $this->baseModel->update($id, ['trang_thai' => 'CON_BAN']);
                $sqlPhienBan = "UPDATE phien_ban_san_pham 
                                SET trang_thai = CASE WHEN so_luong_ton > 0 THEN 'CON_HANG' ELSE 'HET_HANG' END 
                                WHERE san_pham_id = $id";
                $this->baseModel->query($sqlPhienBan);
            }
            $successCount++;
        }

        $message = "Đã cập nhật $successCount sản phẩm";
        if ($failedCount > 0) {
            $message .= ", $failedCount thất bại";
        }

        header("Location: /admin/san-pham?success=bulk_updated&message=" . urlencode($message));
        exit;
    }

    public function create(array $old = [], array $errors = []): void
    {
        $danhSachDanhMuc = $this->sanPhamModel->layDanhSachDanhMucHoatDong();

        $data = [
            'old' => $old,
            'errors' => $errors,
            'danhSachDanhMuc' => $danhSachDanhMuc,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/create.php';
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/san-pham/them');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST);

        if (!empty($errors)) {
            $this->create($old, $errors);
            return;
        }

        $this->baseModel->create($payload);
        header('Location: /admin/san-pham?success=created');
        exit;
    }

    public function edit($id, array $old = [], array $errors = []): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        $sanPham = $this->baseModel->getById($id);
        if (!$sanPham) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        $danhSachDanhMuc = $this->sanPhamModel->layDanhSachDanhMucHoatDong();

        $data = [
            'sanPham' => $sanPham,
            'old' => $old,
            'errors' => $errors,
            'danhSachDanhMuc' => $danhSachDanhMuc,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/edit.php';
    }

    public function update($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/san-pham');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        $sanPham = $this->baseModel->getById($id);
        if (!$sanPham) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        [$payload, $errors, $old] = $this->validatePayload($_POST, $id);

        if (!empty($errors)) {
            $this->edit($id, $old, $errors);
            return;
        }

        $this->baseModel->update($id, $payload);

        // If status changed to NGUNG_BAN, update variants
        if (isset($payload['trang_thai']) && $payload['trang_thai'] === 'NGUNG_BAN') {
            $this->sanPhamModel->capNhatTrangThaiPhienBanKhiNgungBan($id);
        }

        header('Location: /admin/san-pham?success=updated');
        exit;
    }

    private function validatePayload(array $input, int $editingId = 0): array
    {
        $errors = [];

        $tenSanPham = trim((string)($input['ten_san_pham'] ?? ''));
        $slugInput = trim((string)($input['slug'] ?? ''));
        $hangSanXuat = trim((string)($input['hang_san_xuat'] ?? ''));
        $moTa = trim((string)($input['mo_ta'] ?? ''));
        $danhMucIdRaw = (string)($input['danh_muc_id'] ?? '');
        $trangThai = trim((string)($input['trang_thai'] ?? 'CON_BAN'));
        $noiBatRaw = (string)($input['noi_bat'] ?? '0');

        // Validate ten_san_pham
        if ($tenSanPham === '') {
            $errors['ten_san_pham'] = 'Tên sản phẩm không được để trống.';
        } elseif (mb_strlen($tenSanPham) > 255) {
            $errors['ten_san_pham'] = 'Tên sản phẩm không được vượt quá 255 ký tự.';
        }

        // Generate or validate slug
        $slug = $slugInput !== '' ? $slugInput : $this->slugify($tenSanPham);
        if ($slug === '') {
            $errors['slug'] = 'Slug không hợp lệ.';
        }

        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $errors['slug'] = 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.';
        }

        // Check slug uniqueness
        if ($this->slugExists($slug, $editingId)) {
            $errors['slug'] = 'Slug đã tồn tại, vui lòng dùng slug khác.';
        }

        // Validate hang_san_xuat
        if ($hangSanXuat !== '' && mb_strlen($hangSanXuat) > 100) {
            $errors['hang_san_xuat'] = 'Hãng sản xuất không được vượt quá 100 ký tự.';
        }

        // Validate danh_muc_id
        $danhMucId = null;
        if ($danhMucIdRaw !== '') {
            if (!ctype_digit($danhMucIdRaw)) {
                $errors['danh_muc_id'] = 'Danh mục không hợp lệ.';
            } else {
                $danhMucId = (int)$danhMucIdRaw;
                if (!$this->categoryExists($danhMucId)) {
                    $errors['danh_muc_id'] = 'Danh mục không tồn tại.';
                }
            }
        }

        // Validate trang_thai
        $validStatuses = ['CON_BAN', 'NGUNG_BAN', 'SAP_RA_MAT', 'HET_HANG'];
        if (!in_array($trangThai, $validStatuses, true)) {
            $errors['trang_thai'] = 'Trạng thái không hợp lệ.';
        }

        // Validate noi_bat
        $noiBat = ($noiBatRaw === '1') ? 1 : 0;

        $payload = [
            'ten_san_pham' => addslashes($tenSanPham),
            'slug' => addslashes($slug),
            'hang_san_xuat' => addslashes($hangSanXuat),
            'mo_ta' => addslashes($moTa),
            'danh_muc_id' => $danhMucId,
            'trang_thai' => $trangThai,
            'noi_bat' => $noiBat,
        ];

        $old = [
            'ten_san_pham' => $tenSanPham,
            'slug' => $slugInput,
            'hang_san_xuat' => $hangSanXuat,
            'mo_ta' => $moTa,
            'danh_muc_id' => $danhMucIdRaw,
            'trang_thai' => $trangThai,
            'noi_bat' => $noiBatRaw,
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

    private function slugExists(string $slug, int $excludeId = 0): bool
    {
        $slug = addslashes($slug);
        $sql = "SELECT id FROM san_pham WHERE slug = '$slug'";
        if ($excludeId > 0) {
            $sql .= " AND id != $excludeId";
        }
        $result = $this->baseModel->query($sql);
        return !empty($result);
    }

    private function categoryExists(int $id): bool
    {
        $sql = "SELECT id FROM danh_muc WHERE id = $id";
        $result = $this->baseModel->query($sql);
        return !empty($result);
    }

    // ===== VARIANT MANAGEMENT METHODS =====

    public function variants($sanPhamId): void
    {
        $sanPhamId = (int)$sanPhamId;
        if ($sanPhamId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        $sanPham = $this->baseModel->getById($sanPhamId);
        if (!$sanPham) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        $phienBanModel = new PhienBanSanPham();
        $variants = $phienBanModel->layPhienBanTheoSanPham($sanPhamId);

        $data = [
            'sanPham' => $sanPham,
            'variants' => $variants,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/variants.php';
    }

    public function createVariant($sanPhamId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/san-pham/phien-ban?id=' . $sanPhamId);
            exit;
        }

        $sanPhamId = (int)$sanPhamId;
        if ($sanPhamId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        $phienBanModel = new PhienBanSanPham();

        [$payload, $errors] = $this->validateVariantPayload($_POST, $sanPhamId);

        if (!empty($errors)) {
            $_SESSION['variant_errors'] = $errors;
            $_SESSION['variant_old'] = $_POST;
            header('Location: /admin/san-pham/phien-ban?id=' . $sanPhamId . '&error=validation');
            exit;
        }

        $phienBanModel->create($payload);
        header('Location: /admin/san-pham/phien-ban?id=' . $sanPhamId . '&success=variant_created');
        exit;
    }

    public function updateVariant($variantId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/san-pham');
            exit;
        }

        $variantId = (int)$variantId;
        if ($variantId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        $phienBanModel = new PhienBanSanPham();
        
        $variant = $phienBanModel->getById($variantId);
        if (!$variant) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        $sanPhamId = $variant['san_pham_id'];

        [$payload, $errors] = $this->validateVariantPayload($_POST, $sanPhamId, $variantId);

        if (!empty($errors)) {
            $_SESSION['variant_errors'] = $errors;
            $_SESSION['variant_old'] = $_POST;
            header('Location: /admin/san-pham/phien-ban?id=' . $sanPhamId . '&error=validation');
            exit;
        }

        $phienBanModel->update($variantId, $payload);
        header('Location: /admin/san-pham/phien-ban?id=' . $sanPhamId . '&success=variant_updated');
        exit;
    }

    public function deleteVariant($variantId): void
    {
        $variantId = (int)$variantId;
        if ($variantId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        $phienBanModel = new PhienBanSanPham();
        
        $variant = $phienBanModel->getById($variantId);
        if (!$variant) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        $sanPhamId = $variant['san_pham_id'];
        $phienBanModel->delete($variantId);

        header('Location: /admin/san-pham/phien-ban?id=' . $sanPhamId . '&success=variant_deleted');
        exit;
    }

    private function validateVariantPayload(array $input, int $sanPhamId, int $editingId = 0): array
    {
        $errors = [];

        $sku = trim((string)($input['sku'] ?? ''));
        $tenPhienBan = trim((string)($input['ten_phien_ban'] ?? ''));
        $mauSac = trim((string)($input['mau_sac'] ?? ''));
        $dungLuong = trim((string)($input['dung_luong'] ?? ''));
        $ram = trim((string)($input['ram'] ?? ''));
        $giaBanRaw = trim((string)($input['gia_ban'] ?? ''));
        $giaGocRaw = trim((string)($input['gia_goc'] ?? ''));
        $soLuongTonRaw = trim((string)($input['so_luong_ton'] ?? '0'));

        // Validate SKU
        if ($sku === '') {
            $errors['sku'] = 'SKU không được để trống.';
        } else {
            require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
            $phienBanModel = new PhienBanSanPham();
            if ($phienBanModel->kiemTraSKU($sku, $editingId)) {
                $errors['sku'] = 'SKU đã tồn tại, vui lòng dùng SKU khác.';
            }
        }

        // Validate gia_ban
        if ($giaBanRaw === '' || !is_numeric($giaBanRaw)) {
            $errors['gia_ban'] = 'Giá bán phải là số.';
        } else {
            $giaBan = (float)$giaBanRaw;
            if ($giaBan <= 0) {
                $errors['gia_ban'] = 'Giá bán phải lớn hơn 0.';
            }
        }

        // Validate gia_goc
        $giaGoc = null;
        if ($giaGocRaw !== '') {
            if (!is_numeric($giaGocRaw)) {
                $errors['gia_goc'] = 'Giá gốc phải là số.';
            } else {
                $giaGoc = (float)$giaGocRaw;
                if (isset($giaBan) && $giaGoc < $giaBan) {
                    $errors['gia_goc'] = 'Giá gốc phải lớn hơn hoặc bằng giá bán.';
                }
            }
        }

        // Validate so_luong_ton
        if (!is_numeric($soLuongTonRaw)) {
            $errors['so_luong_ton'] = 'Số lượng tồn phải là số.';
        } else {
            $soLuongTon = (int)$soLuongTonRaw;
            if ($soLuongTon < 0) {
                $errors['so_luong_ton'] = 'Số lượng tồn không được âm.';
            }
        }

        $trangThai = (isset($soLuongTon) && $soLuongTon > 0) ? 'CON_HANG' : 'HET_HANG';

        $payload = [
            'san_pham_id' => $sanPhamId,
            'sku' => addslashes($sku),
            'ten_phien_ban' => addslashes($tenPhienBan),
            'mau_sac' => addslashes($mauSac),
            'dung_luong' => addslashes($dungLuong),
            'ram' => addslashes($ram),
            'gia_ban' => $giaBan ?? 0,
            'gia_goc' => $giaGoc,
            'so_luong_ton' => $soLuongTon ?? 0,
            'trang_thai' => $trangThai,
        ];

        return [$payload, $errors];
    }

    // ===== IMAGE MANAGEMENT METHODS =====

    public function images($sanPhamId): void
    {
        $sanPhamId = (int)$sanPhamId;
        if ($sanPhamId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        $sanPham = $this->baseModel->getById($sanPhamId);
        if (!$sanPham) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/HinhAnhSanPham.php';
        $hinhAnhModel = new HinhAnhSanPham();
        $images = $hinhAnhModel->layHinhAnhTheoSanPham($sanPhamId);

        require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
        $phienBanModel = new PhienBanSanPham();
        $variants = $phienBanModel->layPhienBanTheoSanPham($sanPhamId);

        $data = [
            'sanPham' => $sanPham,
            'images' => $images,
            'variants' => $variants,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/images.php';
    }

    public function uploadImage($sanPhamId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/san-pham/hinh-anh?id=' . $sanPhamId);
            exit;
        }

        $sanPhamId = (int)$sanPhamId;
        if ($sanPhamId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/HinhAnhSanPham.php';
        $hinhAnhModel = new HinhAnhSanPham();

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /admin/san-pham/hinh-anh?id=' . $sanPhamId . '&error=no_file');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/services/cloudinary/CloudinaryService.php';
        $cloudinary = CloudinaryService::getInstance();

        try {
            $publicIdPrefix = 'product_' . $sanPhamId . '_' . time() . '_' . rand(100, 999);

            $uploadResult = $cloudinary->uploadApi()->upload($_FILES['image']['tmp_name'], [
                'folder'    => 'products',
                'public_id' => $publicIdPrefix
            ]);
            
            $imageUrl = $uploadResult['secure_url'];

        } catch (\Exception $e) {
            $_SESSION['image_error'] = "Lỗi Cloudinary: " . $e->getMessage();
            header('Location: /admin/san-pham/hinh-anh?id=' . $sanPhamId . '&error=upload_failed');
            exit;
        }

        $altText = trim((string)($_POST['alt_text'] ?? ''));
        $thuTu = (int)($_POST['thu_tu'] ?? 0);
        $laAnhChinh = isset($_POST['la_anh_chinh']) ? 1 : 0;
        $phienBanId = !empty($_POST['phien_ban_id']) ? (int)$_POST['phien_ban_id'] : null;

        if ($laAnhChinh) {
            $hinhAnhModel->datAnhChinh(0, $sanPhamId);
        }

        $payload = [
            'san_pham_id' => $sanPhamId,
            'phien_ban_id' => $phienBanId,
            'url_anh' => $imageUrl, 
            'alt_text' => addslashes($altText),
            'la_anh_chinh' => $laAnhChinh,
            'thu_tu' => $thuTu,
        ];

        $hinhAnhModel->create($payload);
        header('Location: /admin/san-pham/hinh-anh?id=' . $sanPhamId . '&success=image_uploaded');
        exit;
    }

    public function deleteImage($imageId): void
    {
        $imageId = (int)$imageId;
        if ($imageId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/HinhAnhSanPham.php';
        $hinhAnhModel = new HinhAnhSanPham();
        
        $image = $hinhAnhModel->getById($imageId);
        if (!$image) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        $sanPhamId = $image['san_pham_id'];

        $this->deleteCloudinaryImage($image['url_anh']);

        $hinhAnhModel->xoaVaXoaFile($imageId);

        header('Location: /admin/san-pham/hinh-anh?id=' . $sanPhamId . '&success=image_deleted');
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
            } catch (\Exception $e) { }
        }
    }

    public function setMainImage($imageId): void
    {
        $imageId = (int)$imageId;
        if ($imageId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/HinhAnhSanPham.php';
        $hinhAnhModel = new HinhAnhSanPham();
        
        $image = $hinhAnhModel->getById($imageId);
        if (!$image) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        $sanPhamId = $image['san_pham_id'];
        $hinhAnhModel->datAnhChinh($imageId, $sanPhamId);

        header('Location: /admin/san-pham/hinh-anh?id=' . $sanPhamId . '&success=main_image_set');
        exit;
    }

    // ===== TECHNICAL SPECIFICATIONS MANAGEMENT =====

    public function specifications($sanPhamId): void
    {
        $sanPhamId = (int)$sanPhamId;
        if ($sanPhamId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        $sanPham = $this->baseModel->getById($sanPhamId);
        if (!$sanPham) {
            header('Location: /admin/san-pham?error=not_found');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/ThongSoKyThuat.php';
        $thongSoModel = new ThongSoKyThuat();
        $specifications = $thongSoModel->layTheoSanPham($sanPhamId);

        $data = [
            'sanPham' => $sanPham,
            'specifications' => $specifications,
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/specifications.php';
    }

    public function updateSpecifications($sanPhamId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/san-pham/thong-so?id=' . $sanPhamId);
            exit;
        }

        $sanPhamId = (int)$sanPhamId;
        if ($sanPhamId <= 0) {
            header('Location: /admin/san-pham?error=invalid_id');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/models/entities/ThongSoKyThuat.php';
        $thongSoModel = new ThongSoKyThuat();

        // Process specifications array from form
        $specifications = [];
        if (isset($_POST['specifications']) && is_array($_POST['specifications'])) {
            foreach ($_POST['specifications'] as $spec) {
                if (!empty($spec['ten_thong_so']) && !empty($spec['gia_tri'])) {
                    $specifications[] = [
                        'ten_thong_so' => trim($spec['ten_thong_so']),
                        'gia_tri' => trim($spec['gia_tri']),
                        'thu_tu' => (int)($spec['thu_tu'] ?? 0),
                    ];
                }
            }
        }

        $thongSoModel->capNhatHoacTao($sanPhamId, $specifications);
        header('Location: /admin/san-pham/thong-so?id=' . $sanPhamId . '&success=specs_updated');
        exit;
    }
}
