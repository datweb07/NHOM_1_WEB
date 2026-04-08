<?php

namespace App\Core;

require_once dirname(__DIR__) . '/models/entities/DanhMuc.php';

/**
 * Helper class để load dữ liệu cho header
 */
class HeaderHelper
{
    private static ?\DanhMuc $danhMucModel = null;

    /**
     * Lấy instance của DanhMuc model
     */
    private static function getDanhMucModel(): \DanhMuc
    {
        if (self::$danhMucModel === null) {
            self::$danhMucModel = new \DanhMuc();
        }
        return self::$danhMucModel;
    }

    /**
     * Lấy danh mục cho navigation menu
     * Trả về danh mục cha và con theo cấu trúc phân cấp
     * 
     * @return array
     */
    public static function layDanhMucNavigation(): array
    {
        $danhMucModel = self::getDanhMucModel();
        
        // Lấy tất cả danh mục đang hiển thị
        $sql = "SELECT id, ten, slug, icon_url, danh_muc_cha_id, thu_tu
                FROM danh_muc
                WHERE trang_thai = 1
                ORDER BY thu_tu ASC, ten ASC";
        
        $allCategories = $danhMucModel->query($sql);
        
        // Tổ chức thành cấu trúc cha-con
        $categoriesById = [];
        $parentCategories = [];
        
        foreach ($allCategories as $category) {
            $categoriesById[$category['id']] = $category;
            $categoriesById[$category['id']]['children'] = [];
            
            if ($category['danh_muc_cha_id'] === null) {
                $parentCategories[] = &$categoriesById[$category['id']];
            }
        }
        
        // Gán con vào cha
        foreach ($allCategories as $category) {
            if ($category['danh_muc_cha_id'] !== null && isset($categoriesById[$category['danh_muc_cha_id']])) {
                $categoriesById[$category['danh_muc_cha_id']]['children'][] = &$categoriesById[$category['id']];
            }
        }
        
        return $parentCategories;
    }

    /**
     * Lấy danh mục cha (top-level categories) cho navigation
     * 
     * @param int $limit
     * @return array
     */
    public static function layDanhMucCha(int $limit = 10): array
    {
        $danhMucModel = self::getDanhMucModel();
        
        $sql = "SELECT id, ten, slug, icon_url
                FROM danh_muc
                WHERE trang_thai = 1 AND danh_muc_cha_id IS NULL
                ORDER BY thu_tu ASC, ten ASC
                LIMIT " . (int)$limit;
        
        return $danhMucModel->query($sql);
    }

    /**
     * Lấy danh mục con theo ID cha
     * 
     * @param int $parentId
     * @return array
     */
    public static function layDanhMucCon(int $parentId): array
    {
        $danhMucModel = self::getDanhMucModel();
        
        $sql = "SELECT id, ten, slug, icon_url
                FROM danh_muc
                WHERE trang_thai = 1 AND danh_muc_cha_id = " . (int)$parentId . "
                ORDER BY thu_tu ASC, ten ASC";
        
        return $danhMucModel->query($sql);
    }

    /**
     * Lấy icon class cho danh mục
     * Map tên danh mục sang icon FontAwesome
     * 
     * @param string $categoryName
     * @return string
     */
    public static function layIconClass(string $categoryName): string
    {
        $iconMap = [
            'Điện thoại' => 'fa fa-mobile',
            'Điện Thoại' => 'fa fa-mobile',
            'Laptop' => 'fa fa-laptop',
            'Máy tính bảng' => 'fa fa-tablet',
            'Máy Tính Bảng' => 'fa fa-tablet',
            'Apple' => 'fa-brands fa-apple',
            'PC' => 'fa fa-desktop',
            'PC-Linh kiện' => 'fa fa-desktop',
            'Linh kiện' => 'fa fa-microchip',
            'Phụ kiện' => 'fa fa-headphones',
            'Máy cũ' => 'fa fa-rotate-right',
            'Máy cũ giá rẻ' => 'fa fa-rotate-right',
            'Hàng gia dụng' => 'fa fa-house-laptop',
            'Sim' => 'fa fa-sd-card',
            'Thẻ cào' => 'fa fa-sd-card',
            'Sim & Thẻ cào' => 'fa fa-sd-card',
            'Khuyến mãi' => 'fa fa-certificate',
            'Tivi' => 'fa fa-tv',
            'Màn hình' => 'fa fa-desktop',
            'Đồng hồ' => 'fa fa-clock',
            'Đồng hồ thông minh' => 'fa fa-clock',
        ];
        
        return $iconMap[$categoryName] ?? 'fa fa-box';
    }
}
