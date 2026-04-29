<?php

require_once dirname(__DIR__) . '/models/BaseModel.php';

class CategoryAttributesApi
{
    private $baseModel;

    public function __construct()
    {
        $this->baseModel = new BaseModel('thuoc_tinh_danh_muc');
    }

    public function getAttributes(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $categoryName = $_GET['category'] ?? '';

        if (empty($categoryName)) {
            $this->sendResponse(false, [], 'Category name is required');
            return;
        }

        $categoryNameClean = addslashes(trim($categoryName));

        $sql = "SELECT tt.name, tt.label, tt.placeholder, tt.type, tt.col
                FROM thuoc_tinh_danh_muc tt
                INNER JOIN danh_muc dm ON tt.danh_muc_id = dm.id
                WHERE dm.ten = '$categoryNameClean' AND dm.trang_thai = 1
                ORDER BY tt.thu_tu ASC";

        $attributes = $this->baseModel->query($sql);

        $this->sendResponse(true, $attributes);
    }

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

$api = new CategoryAttributesApi();
$api->getAttributes();
