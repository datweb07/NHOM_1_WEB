<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class GioHang extends BaseModel
{
    protected ?int $id = null;
    protected ?int $nguoiDungId = null;
    protected ?string $sessionId = null;
    protected ?string $ngayTao = null;
    protected ?string $ngayCapNhat = null;

    public function __construct()
    {
        parent::__construct('gio_hang');
    }

    public function layTheoNguoiDung(?int $nguoiDungId): ?array
    {
        if ($nguoiDungId === null) {
            return null;
        }

        $sql = "SELECT * FROM {$this->table} WHERE nguoi_dung_id = " . (int)$nguoiDungId . ' LIMIT 1';
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }

    public function layTheoSession(string $sessionId): ?array
    {
        $safeSessionId = addslashes($sessionId);
        $sql = "SELECT * FROM {$this->table} WHERE session_id = '$safeSessionId' LIMIT 1";
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nguoi_dung_id' => $this->nguoiDungId,
            'session_id' => $this->sessionId,
            'ngay_tao' => $this->ngayTao,
            'ngay_cap_nhat' => $this->ngayCapNhat,
        ];
    }
}
