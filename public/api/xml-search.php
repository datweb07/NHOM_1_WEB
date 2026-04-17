<?php
/**
 * XML Search API Endpoint
 * Direct endpoint to avoid routing issues
 */

// Clear any output buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Set XML header
header('Content-Type: application/xml; charset=utf-8');

try {
    // Include required files
    require_once dirname(__DIR__, 2) . '/app/models/entities/SanPham.php';
    
    // Get search keyword
    $keyword = $_GET['q'] ?? '';
    
    // Query products
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
    
    // Build XML document
    $xml = new \DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    // Create root element
    $root = $xml->createElement('products');
    $xml->appendChild($root);
    
    // Add products
    foreach ($sanPhams as $sp) {
        $product = $xml->createElement('product');
        
        // Add product elements with proper escaping
        $id = $xml->createElement('id', htmlspecialchars((string)($sp['id'] ?? ''), ENT_XML1, 'UTF-8'));
        $product->appendChild($id);
        
        $name = $xml->createElement('name', htmlspecialchars($sp['ten_san_pham'] ?? '', ENT_XML1, 'UTF-8'));
        $product->appendChild($name);
        
        // Use gia_hien_thi instead of gia_ban
        $price = $xml->createElement('price', htmlspecialchars((string)($sp['gia_hien_thi'] ?? '0'), ENT_XML1, 'UTF-8'));
        $product->appendChild($price);
        
        // Use anh_chinh instead of hinh_anh
        $image = $xml->createElement('image', htmlspecialchars($sp['anh_chinh'] ?? '', ENT_XML1, 'UTF-8'));
        $product->appendChild($image);
        
        $slug = $xml->createElement('slug', htmlspecialchars($sp['slug'] ?? '', ENT_XML1, 'UTF-8'));
        $product->appendChild($slug);
        
        $root->appendChild($product);
    }
    
    // Output XML
    echo $xml->saveXML();
    
} catch (\Exception $e) {
    // Return error XML
    error_log("XML Search Error: " . $e->getMessage());
    
    $xml = new \DOMDocument('1.0', 'UTF-8');
    $root = $xml->createElement('products');
    $xml->appendChild($root);
    
    $error = $xml->createElement('error', htmlspecialchars($e->getMessage(), ENT_XML1, 'UTF-8'));
    $root->appendChild($error);
    
    echo $xml->saveXML();
}

// Stop execution
exit;
