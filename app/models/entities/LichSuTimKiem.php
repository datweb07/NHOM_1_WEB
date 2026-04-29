<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class LichSuTimKiem extends BaseModel
{
    public function __construct()
    {
        parent::__construct('lich_su_tim_kiem');
    }

    public function luuLichSu(int $nguoiDungId, string $tuKhoa): int
    {
        return $this->create([
            'nguoi_dung_id' => $nguoiDungId,
            'tu_khoa' => $tuKhoa
        ]);
    }

    public function layLichSuTheoUser(int $nguoiDungId, int $limit = 10): array
    {
        $nguoiDungId = (int)$nguoiDungId;
        $limit = max(1, (int)$limit);
        
        $sql = "SELECT DISTINCT tu_khoa, MAX(thoi_gian_tim) as thoi_gian_gan_nhat
                FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId
                GROUP BY tu_khoa
                ORDER BY thoi_gian_gan_nhat DESC
                LIMIT $limit";
        
        return $this->query($sql);
    }

    public function xoaLichSu(int $nguoiDungId): bool
    {
        $nguoiDungId = (int)$nguoiDungId;
        $sql = "DELETE FROM {$this->table} WHERE nguoi_dung_id = $nguoiDungId";
        $this->query($sql);
        return true;
    }

    public function layTuKhoaPhoBien(int $limit = 10): array
    {
        $limit = max(1, (int)$limit);
        
        $sql = "SELECT tu_khoa, COUNT(*) as so_lan_tim
                FROM {$this->table}
                WHERE thoi_gian_tim >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY tu_khoa
                ORDER BY so_lan_tim DESC
                LIMIT $limit";
        
        return $this->query($sql);
    }
}
