<?php
require_once dirname(__DIR__) . '/BaseModel.php';

class MaGiamGia extends BaseModel
{
    protected ?int $id = null;
    protected ?string $maCode = null;
    protected ?string $moTa = null;
    protected ?string $loaiGiam = null;
    protected ?float $giaTriGiam = null;
    protected ?float $giamToiDa = null;
    protected float $donToiThieu = 0;
    protected int $soLuotDaDung = 0;
    protected ?int $gioiHanSuDung = null;
    protected ?string $ngayBatDau = null;
    protected ?string $ngayKetThuc = null;
    protected string $trangThai = 'HOAT_DONG';

    public function __construct()
    {
        parent::__construct('ma_giam_gia');
    }

    public function timTheoMaCode(string $maCode): ?array
    {
        $safeCode = addslashes(trim($maCode));
        $sql = "SELECT * FROM {$this->table} WHERE ma_code = '$safeCode' LIMIT 1";
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }

    public function kiemTraHopLe(array $voucher, float $tongTienDonHang): bool
    {
        if (($voucher['trang_thai'] ?? '') !== 'HOAT_DONG') {
            return false;
        }

        if ($tongTienDonHang < (float)($voucher['don_toi_thieu'] ?? 0)) {
            return false;
        }

        $gioiHan = $voucher['gioi_han_su_dung'] ?? null;
        $daDung = (int)($voucher['so_luot_da_dung'] ?? 0);
        if ($gioiHan !== null && $daDung >= (int)$gioiHan) {
            return false;
        }

        $now = time();
        $batDau = strtotime((string)($voucher['ngay_bat_dau'] ?? ''));
        $ketThuc = strtotime((string)($voucher['ngay_ket_thuc'] ?? ''));

        if ($batDau !== false && $now < $batDau) {
            return false;
        }

        if ($ketThuc !== false && $now > $ketThuc) {
            return false;
        }

        return true;
    }

    public function tinhSoTienGiam(array $voucher, float $tongTienDonHang): float
    {
        if (!$this->kiemTraHopLe($voucher, $tongTienDonHang)) {
            return 0;
        }

        $loaiGiam = $voucher['loai_giam'] ?? '';
        $giaTriGiam = (float)($voucher['gia_tri_giam'] ?? 0);

        if ($loaiGiam === 'PHAN_TRAM') {
            $soTienGiam = $tongTienDonHang * $giaTriGiam / 100;
            $giamToiDa = $voucher['giam_toi_da'] !== null ? (float)$voucher['giam_toi_da'] : null;

            if ($giamToiDa !== null) {
                $soTienGiam = min($soTienGiam, $giamToiDa);
            }

            return max(0, $soTienGiam);
        }

        return max(0, $giaTriGiam);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ma_code' => $this->maCode,
            'mo_ta' => $this->moTa,
            'loai_giam' => $this->loaiGiam,
            'gia_tri_giam' => $this->giaTriGiam,
            'giam_toi_da' => $this->giamToiDa,
            'don_toi_thieu' => $this->donToiThieu,
            'so_luot_da_dung' => $this->soLuotDaDung,
            'gioi_han_su_dung' => $this->gioiHanSuDung,
            'ngay_bat_dau' => $this->ngayBatDau,
            'ngay_ket_thuc' => $this->ngayKetThuc,
            'trang_thai' => $this->trangThai,
        ];
    }
}
