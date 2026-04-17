<?php

/**
 * Client API Controller
 * Handles AJAX API requests from client-side
 */

class ApiController
{
    /**
     * XML Search API
     * GET /api/xml-search?q=keyword
     */
    public function xmlSearch(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        
        try {
            require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
            
            $keyword = $_GET['q'] ?? '';
            
            $sanPhams = [];
            if (!empty($keyword)) {
                $sanPhamModel = new \SanPham();
                $sanPhams = $sanPhamModel->layDanhSachPhanTrang(
                    $keyword,
                    0,      // danhMucId
                    null,   // giaMin
                    null,   // giaMax
                    10,     // limit
                    0       // offset
                );
            }
            
            // Build XML
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;
            
            $root = $xml->createElement('products');
            $xml->appendChild($root);
            
            foreach ($sanPhams as $sp) {
                $product = $xml->createElement('product');
                
                $id = $xml->createElement('id', htmlspecialchars((string)($sp['id'] ?? ''), ENT_XML1, 'UTF-8'));
                $product->appendChild($id);
                
                $name = $xml->createElement('name', htmlspecialchars($sp['ten_san_pham'] ?? '', ENT_XML1, 'UTF-8'));
                $product->appendChild($name);
                
                $price = $xml->createElement('price', htmlspecialchars((string)($sp['gia_hien_thi'] ?? '0'), ENT_XML1, 'UTF-8'));
                $product->appendChild($price);
                
                $image = $xml->createElement('image', htmlspecialchars($sp['anh_chinh'] ?? '', ENT_XML1, 'UTF-8'));
                $product->appendChild($image);
                
                $slug = $xml->createElement('slug', htmlspecialchars($sp['slug'] ?? '', ENT_XML1, 'UTF-8'));
                $product->appendChild($slug);
                
                $root->appendChild($product);
            }
            
            echo $xml->saveXML();
            
        } catch (\Exception $e) {
            error_log("XML Search Error: " . $e->getMessage());
            
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('products');
            $xml->appendChild($root);
            
            $error = $xml->createElement('error', htmlspecialchars($e->getMessage(), ENT_XML1, 'UTF-8'));
            $root->appendChild($error);
            
            echo $xml->saveXML();
        }
        
        exit;
    }
    
    /**
     * Brand Menu API
     * GET /api/brand-menu?name=Samsung
     */
    public function brandMenu(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $brandName = $_GET['name'] ?? '';
        
        if (empty($brandName)) {
            $this->sendJsonResponse(false, [], 'Thiếu tên thương hiệu');
            return;
        }
        
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $baseModel = new BaseModel('san_pham');
        
        $brandNameClean = addslashes(trim($brandName));
        
        // Get products
        $sqlProducts = "
            SELECT 
                sp.id, 
                sp.ten_san_pham, 
                sp.slug, 
                sp.gia_hien_thi,
                (SELECT url_anh FROM hinh_anh_san_pham ha 
                 WHERE ha.san_pham_id = sp.id AND ha.la_anh_chinh = 1 LIMIT 1) as anh_chinh
            FROM san_pham sp
            WHERE sp.hang_san_xuat = '$brandNameClean' 
              AND sp.trang_thai = 'CON_BAN'
            ORDER BY sp.noi_bat DESC, sp.id DESC 
            LIMIT 5
        ";
        
        $products = $baseModel->query($sqlProducts);
        
        if (empty($products)) {
            $this->sendJsonResponse(false, [], "Thương hiệu $brandName chưa có sản phẩm nào");
            return;
        }
        
        // Get categories
        $sqlCategories = "
            SELECT DISTINCT 
                dm.id, 
                dm.ten, 
                dm.slug,
                dm.thu_tu
            FROM danh_muc dm
            INNER JOIN san_pham sp ON dm.id = sp.danh_muc_id
            WHERE sp.hang_san_xuat = '$brandNameClean' 
              AND dm.trang_thai = 1
            ORDER BY dm.thu_tu ASC
            LIMIT 8
        ";
        
        $subCategories = $baseModel->query($sqlCategories);
        
        $responseData = [
            'brands' => [],
            'products' => $products,
            'subCategories' => $subCategories
        ];
        
        $this->sendJsonResponse(true, $responseData);
    }
    
    /**
     * Helper: Send JSON response
     */
    private function sendJsonResponse(bool $success, array $data, ?string $message = null): void
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
