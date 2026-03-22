<?php

class SanPhamController
{
    private $baseModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/BaseModel.php';
        $this->baseModel = new BaseModel('san_pham');
    }
    
    public function index(): void
    {
        $keyword = trim((string)($_GET['keyword'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10; 
        $offset = ($page - 1) * $limit;

        $whereSql = "";
        if ($keyword !== '') {
            $keywordSafe = addslashes($keyword);
            $whereSql = "WHERE ten_san_pham LIKE '%$keywordSafe%' OR hang_san_xuat LIKE '%$keywordSafe%'";
        }

        $sqlCount = "SELECT COUNT(*) as total FROM san_pham $whereSql";
        $resultCount = $this->baseModel->query($sqlCount);
        $totalRecords = !empty($resultCount) ? (int)$resultCount[0]['total'] : 0;
        $totalPages = max(1, ceil($totalRecords / $limit));

        $sqlSelect = "SELECT * FROM san_pham $whereSql ORDER BY id DESC LIMIT $offset, $limit";
        $danhSachSanPham = $this->baseModel->query($sqlSelect);

        $data = [
            'danhSachSanPham' => $danhSachSanPham,
            'keyword' => $keyword,
            'page' => $page,
            'totalPages' => $totalPages,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/san_pham/index.php';
    }

    public function hide($id = null): void
    {
        $id = (int)($id ?? $_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: /index.php?controller=SanPham&action=index&error=invalid_id");
            exit;
        }
        
        if (!$this->baseModel->getById($id)) {
            header("Location: /index.php?controller=SanPham&action=index&error=not_found");
            exit;
        }

        $this->baseModel->update($id, ['trang_thai' => 'NGUNG_BAN']);
        
        header("Location: /index.php?controller=SanPham&action=index&success=hidden");
        exit;
    }

    public function delete($id = null): void
    {
        $id = (int)($id ?? $_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: /index.php?controller=SanPham&action=index&error=invalid_id");
            exit;
        }
        
        if (!$this->baseModel->getById($id)) {
            header("Location: /index.php?controller=SanPham&action=index&error=not_found");
            exit;
        }

        $this->baseModel->delete($id);
        
        header("Location: /index.php?controller=SanPham&action=index&success=deleted");
        exit;
    }
}
?>