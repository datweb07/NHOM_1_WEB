<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class DiaChi extends BaseModel
{
    public function __construct()
    {
        parent::__construct('dia_chi');
    }

    public function layDanhSachTheoUser(int $nguoiDungId): array
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId
                ORDER BY mac_dinh DESC, id DESC";
        
        return $this->query($sql);
    }

    public function layDiaChiMacDinh(int $nguoiDungId): ?array
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId AND mac_dinh = 1
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    public function themDiaChi(array $data): int
    {
        if (isset($data['mac_dinh']) && $data['mac_dinh'] == 1) {
            $this->boMacDinhTatCa($data['nguoi_dung_id']);
        }
        
        return $this->create($data);
    }

    public function capNhatDiaChi(int $id, array $data): int
    {
        if (isset($data['mac_dinh']) && $data['mac_dinh'] == 1) {
            $diaChi = $this->getById($id);
            if ($diaChi) {
                $this->boMacDinhTatCa($diaChi['nguoi_dung_id']);
            }
        }
        
        return $this->update($id, $data);
    }

    private function boMacDinhTatCa(int $nguoiDungId): void
    {
        $nguoiDungId = (int)$nguoiDungId;
        $sql = "UPDATE {$this->table} SET mac_dinh = 0 WHERE nguoi_dung_id = $nguoiDungId";
        $this->query($sql);
    }

    public function datMacDinh(int $id): int
    {
        $diaChi = $this->getById($id);
        if (!$diaChi) {
            return 0;
        }
        
        $this->boMacDinhTatCa($diaChi['nguoi_dung_id']);
        return $this->update($id, ['mac_dinh' => 1]);
    }

    public function xoaDiaChi(int $id): int
    {
        return $this->delete($id);
    }
}
