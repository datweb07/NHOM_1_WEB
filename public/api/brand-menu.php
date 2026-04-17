<?php

/**
 * API: Lấy dữ liệu Mega Menu theo Thương hiệu (Brand)
 * Trả về 5 sản phẩm nổi bật và các danh mục có chứa sản phẩm của hãng đó
 */

// Load config and database connection
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/app/models/BaseModel.php';

class MegaMenuBrandApi
{
    private $baseModel;

    public function __construct()
    {
        // Khởi tạo model chung để dùng hàm query()
        $this->baseModel = new BaseModel('san_pham');
    }

    public function getBrandMenu(): void
    {
        // Bắt buộc trả về JSON
        header('Content-Type: application/json; charset=utf-8');

        $brandName = $_GET['name'] ?? '';

        if (empty($brandName)) {
            $this->sendResponse(false, [], 'Thiếu tên thương hiệu (brand name).');
            return;
        }

        // Chống SQL Injection
        $brandNameClean = addslashes(trim($brandName));

        // --------------------------------------------------------
        // 1. QUERY LẤY 5 SẢN PHẨM NỔI BẬT CỦA HÃNG
        // Lấy thông tin cơ bản + subquery lấy ảnh chính từ bảng hinh_anh_san_pham
        // --------------------------------------------------------
        $sqlProducts = "
            SELECT 
                sp.id, 
                sp.ten_san_pham, 
                sp.slug, 
                sp.gia_hien_thi,
                (SELECT url_anh FROM hinh_anh_san_pham ha WHERE ha.san_pham_id = sp.id AND ha.la_anh_chinh = 1 LIMIT 1) as anh_chinh
            FROM san_pham sp
            WHERE sp.hang_san_xuat = '$brandNameClean' 
              AND sp.trang_thai = 'CON_BAN'
            ORDER BY sp.noi_bat DESC, sp.id DESC 
            LIMIT 5
        ";

        $products = $this->baseModel->query($sqlProducts);

        // Nếu hãng này chưa có sản phẩm nào, báo lỗi nhẹ nhàng
        if (empty($products)) {
            $this->sendResponse(false, [], "Thương hiệu $brandName chưa có sản phẩm nào đang bán.");
            return;
        }

        // --------------------------------------------------------
        // 2. QUERY LẤY CÁC DANH MỤC LIÊN QUAN ĐẾN HÃNG
        // Tìm xem các sản phẩm của hãng này đang nằm ở những danh mục nào
        // --------------------------------------------------------
        $sqlCategories = "
                SELECT DISTINCT 
                    dm.id, 
                    dm.ten, 
                    dm.slug,
                    dm.thu_tu  /* Thêm cột này vào để MySQL cho phép ORDER BY */
                FROM danh_muc dm
                INNER JOIN san_pham sp ON dm.id = sp.danh_muc_id
                WHERE sp.hang_san_xuat = '$brandNameClean' 
                  AND dm.trang_thai = 1
                ORDER BY dm.thu_tu ASC
                LIMIT 8
            ";

        $subCategories = $this->baseModel->query($sqlCategories);

        // --------------------------------------------------------
        // 3. ĐÓNG GÓI VÀ TRẢ VỀ CHO JAVASCRIPT
        // --------------------------------------------------------
        $responseData = [
            'brands' => [], // Để rỗng vì ở menu brand không cần show lại logo brand
            'products' => $products,
            'subCategories' => $subCategories
        ];

        $this->sendResponse(true, $responseData);
    }

    /**
     * Hàm helper trả về JSON
     */
    private function sendResponse(bool $success, array $data, ?string $message = null): void
    {
        $response = [
            'success' => $success,
            'data' => $data
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Khởi chạy API
$api = new MegaMenuBrandApi();
$api->getBrandMenu();