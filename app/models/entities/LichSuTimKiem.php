<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class LichSuTimKiem extends BaseModel
{
    protected ?int $id = null;
    protected ?int $nguoiDungId = null;
    protected ?string $tuKhoa = null;
    protected ?string $thoiGianTim = null;

    public function __construct()
    {
        parent::__construct('lich_su_tim_kiem');
    }

    public function layGanDayTheoNguoiDung(int $nguoiDungId, int $limit = 10): array
    {
        $limit = max(1, (int)$limit);
        $sql = "SELECT * FROM {$this->table}
				WHERE nguoi_dung_id = " . (int)$nguoiDungId . "
				ORDER BY thoi_gian_tim DESC
				LIMIT $limit";
        return $this->query($sql);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nguoi_dung_id' => $this->nguoiDungId,
            'tu_khoa' => $this->tuKhoa,
            'thoi_gian_tim' => $this->thoiGianTim,
        ];
    }
}
