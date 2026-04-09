<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/models/entities/SanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThongSoKyThuat.php';

use SanPham;
use PhienBanSanPham;
use ThongSoKyThuat;

class CompareController
{
    private SanPham $sanPhamModel;
    private PhienBanSanPham $phienBanModel;
    private ThongSoKyThuat $thongSoModel;

    public function __construct()
    {
        $this->sanPhamModel = new SanPham();
        $this->phienBanModel = new PhienBanSanPham();
        $this->thongSoModel = new ThongSoKyThuat();
    }

    public function index(): void
    {
        $selectedSlugs = $this->laySlugDaChon();

        $sanPhamSoSanh = [];
        $thongSoTheoSanPham = [];
        $tenThongSo = [];

        if (!empty($selectedSlugs)) {
            $sanPhamSoSanh = $this->sanPhamModel->laySanPhamTheoSlugs($selectedSlugs, 4);

            foreach ($sanPhamSoSanh as &$sanPham) {
                $phienBanList = $this->phienBanModel->layPhienBanTheoSanPham((int)$sanPham['id']);
                $phienBanMacDinh = $phienBanList[0] ?? null;

                $sanPham['phien_ban_mac_dinh'] = $phienBanMacDinh;
                $sanPham['tong_ton_kho'] = array_sum(array_map(static function ($pb) {
                    return (int)($pb['so_luong_ton'] ?? 0);
                }, $phienBanList));

                $thongSoList = $this->thongSoModel->layThongSoTheoSanPham((int)$sanPham['id']);
                $mapThongSo = [];

                foreach ($thongSoList as $thongSo) {
                    $ten = trim((string)($thongSo['ten_thong_so'] ?? ''));
                    if ($ten === '') {
                        continue;
                    }

                    if (!in_array($ten, $tenThongSo, true)) {
                        $tenThongSo[] = $ten;
                    }

                    $mapThongSo[$ten] = (string)($thongSo['gia_tri'] ?? '-');
                }

                $thongSoTheoSanPham[(int)$sanPham['id']] = $mapThongSo;
            }
            unset($sanPham);
        }

        $danhSachSanPham = $this->sanPhamModel->layTatCa();

        require_once dirname(__DIR__, 2) . '/views/client/san_pham/compare.php';
    }

    private function laySlugDaChon(): array
    {
        $slugs = [];

        if (isset($_GET['slugs']) && is_string($_GET['slugs'])) {
            $parts = explode(',', $_GET['slugs']);
            foreach ($parts as $part) {
                $slug = $this->chuanHoaSlug($part);
                if ($slug !== '') {
                    $slugs[] = $slug;
                }
            }
        }

        if (isset($_GET['slug']) && is_array($_GET['slug'])) {
            foreach ($_GET['slug'] as $value) {
                $slug = $this->chuanHoaSlug((string)$value);
                if ($slug !== '') {
                    $slugs[] = $slug;
                }
            }
        }

        if (isset($_GET['ids'])) {
            $ids = is_array($_GET['ids']) ? $_GET['ids'] : explode(',', (string)$_GET['ids']);
            $idList = [];
            foreach ($ids as $rawId) {
                $id = (int)$rawId;
                if ($id > 0) {
                    $idList[] = $id;
                }
            }

            if (!empty($idList)) {
                $sanPhams = $this->sanPhamModel->laySanPhamTheoIds($idList, 4);
                foreach ($sanPhams as $sp) {
                    if (!empty($sp['slug'])) {
                        $slugs[] = (string)$sp['slug'];
                    }
                }
            }
        }

        $slugs = array_values(array_unique($slugs));
        return array_slice($slugs, 0, 4);
    }

    private function chuanHoaSlug(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return '';
        }

        $value = preg_replace('/[^a-z0-9-]/', '', $value);
        return trim((string)$value, '-');
    }
}
