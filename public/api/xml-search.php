<?php

while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/xml; charset=utf-8');

try {
    $basePath = dirname(__DIR__, 2);
    $configPath = $basePath . '/config/config.php';
    
    if (!file_exists($configPath)) {
        throw new \Exception("Config file not found");
    }
    
    require_once $configPath;

    $modelPath = $basePath . '/app/models/entities/SanPham.php';
    
    if (!file_exists($modelPath)) {
        throw new \Exception("SanPham model not found");
    }
    
    require_once $modelPath;

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
