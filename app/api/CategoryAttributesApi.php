<?php

/**
 * API: Category Attributes
 * 
 * Endpoint để lấy danh sách thuộc tính động theo danh mục
 * Được gọi bởi AJAX từ trang quản lý phiên bản sản phẩm
 */

require_once dirname(__DIR__) . '/models/BaseModel.php';

class CategoryAttributesApi
{
    private $baseModel;

    public function __construct()
    {
        $this->baseModel = new BaseModel('thuoc_tinh_danh_muc');
    }

    /**
     * Lấy danh sách thuộc tính theo tên danh mục
     * 
     * @return void (trả về JSON response)
     */
    public function getAttributes(): void
    {
        // Bắt buộc khai báo trả về JSON
        header('Content-Type: application/json; charset=utf-8');
        
        $categoryName = $_GET['category'] ?? '';

        // Kiểm tra danh mục có được cung cấp không
        if (empty($categoryName)) {
            $this->sendResponse(false, [], 'Category name is required');
            return;
        }

        // Chống SQL Injection
        $categoryNameClean = addslashes(trim($categoryName));

        // JOIN bảng thuoc_tinh_danh_muc với bảng danh_muc để tìm thuộc tính dựa trên Tên danh mục
        $sql = "SELECT tt.name, tt.label, tt.placeholder, tt.type, tt.col
                FROM thuoc_tinh_danh_muc tt
                INNER JOIN danh_muc dm ON tt.danh_muc_id = dm.id
                WHERE dm.ten = '$categoryNameClean' AND dm.trang_thai = 1
                ORDER BY tt.thu_tu ASC";

        // Sử dụng baseModel để fetch dữ liệu
        $attributes = $this->baseModel->query($sql);

        // Trả dữ liệu mảng trực tiếp về cho JavaScript vẽ Form
        $this->sendResponse(true, $attributes);
    }

    /**
     * Gửi JSON response
     * 
     * @param bool $success
     * @param array $data
     * @param string|null $message
     * @return void
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

// Xử lý request
$api = new CategoryAttributesApi();
$api->getAttributes();
