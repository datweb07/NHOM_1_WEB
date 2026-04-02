<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class DiaChi extends BaseModel
{
    public function __construct()
    {
        parent::__construct('dia_chi');
    }

    /**
     * Lấy danh sách địa chỉ của user
     */
    public function layDanhSachTheoUser(int $nguoiDungId): array
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId
                ORDER BY mac_dinh DESC, id DESC";
        
        return $this->query($sql);
    }

    /**
     * Lấy địa chỉ mặc định
     */
    public function layDiaChiMacDinh(int $nguoiDungId): ?array
    {
        $nguoiDungId = (int)$nguoiDungId;
        
        $sql = "SELECT * FROM {$this->table}
                WHERE nguoi_dung_id = $nguoiDungId AND mac_dinh = 1
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Thêm địa chỉ mới
     */
    public function themDiaChi(array $data): int
    {
        // Nếu đặt làm mặc định, bỏ mặc định các địa chỉ khác
        if (isset($data['mac_dinh']) && $data['mac_dinh'] == 1) {
            $this->boMacDinhTatCa($data['nguoi_dung_id']);
        }
        
        return $this->create($data);
    }

    /**
     * Cập nhật địa chỉ
     */
    public function capNhatDiaChi(int $id, array $data): int
    {
        // Nếu đặt làm mặc định, bỏ mặc định các địa chỉ khác
        if (isset($data['mac_dinh']) && $data['mac_dinh'] == 1) {
            $diaChi = $this->getById($id);
            if ($diaChi) {
                $this->boMacDinhTatCa($diaChi['nguoi_dung_id']);
            }
        }
        
        return $this->update($id, $data);
    }

    /**
     * Bỏ mặc định tất cả địa chỉ của user
     */
    private function boMacDinhTatCa(int $nguoiDungId): void
    {
        $nguoiDungId = (int)$nguoiDungId;
        $sql = "UPDATE {$this->table} SET mac_dinh = 0 WHERE nguoi_dung_id = $nguoiDungId";
        $this->query($sql);
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function datMacDinh(int $id): int
    {
        $diaChi = $this->getById($id);
        if (!$diaChi) {
            return 0;
        }
        
        $this->boMacDinhTatCa($diaChi['nguoi_dung_id']);
        return $this->update($id, ['mac_dinh' => 1]);
    }

    /**
     * Xóa địa chỉ
     */
    public function xoaDiaChi(int $id): int
    {
        return $this->delete($id);
    }
}
