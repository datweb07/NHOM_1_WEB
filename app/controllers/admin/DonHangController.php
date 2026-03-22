<?php

class DonHangController
{
    private $donHangModel;

    public function __construct()
    {
        require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
        $this->donHangModel = new DonHang();
    }

    public function index(): void
    {
        $trangThai = trim((string)($_GET['trang_thai'] ?? ''));
        $danhSachDonHang = $this->donHangModel->layDanhSach($trangThai !== '' ? $trangThai : null);

        $data = [
            'danhSachDonHang' => $danhSachDonHang,
            'trangThaiFilter' => $trangThai,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/don_hang/index.php';
    }

    public function detail($id): void
    {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/don-hang?error=invalid_id');
            exit;
        }

        $donHang = $this->donHangModel->layChiTietDonHang($id);
        if ($donHang === null) {
            header('Location: /admin/don-hang?error=not_found');
            exit;
        }

        $chiTietDon = $this->donHangModel->laySanPhamTrongDon($id);
        $trangThaiKeTiep = $this->donHangModel->layTrangThaiKeTiep((string)$donHang['trang_thai']);

        $data = [
            'donHang' => $donHang,
            'chiTietDon' => $chiTietDon,
            'trangThaiKeTiep' => $trangThaiKeTiep,
            'success' => $_GET['success'] ?? '',
            'error' => $_GET['error'] ?? '',
        ];

        extract($data);
        require_once dirname(__DIR__, 2) . '/views/admin/don_hang/detail.php';
    }

    public function capNhatTrangThai($id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /admin/don-hang');
            exit;
        }

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /admin/don-hang?error=invalid_id');
            exit;
        }

        $donHang = $this->donHangModel->layChiTietDonHang($id);
        if ($donHang === null) {
            header('Location: /admin/don-hang?error=not_found');
            exit;
        }

        $trangThaiMoi = trim((string)($_POST['trang_thai'] ?? ''));
        $trangThaiHienTai = (string)$donHang['trang_thai'];

        if (!$this->donHangModel->trangThaiHopLe($trangThaiHienTai, $trangThaiMoi)) {
            header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&error=invalid_transition');
            exit;
        }

        $this->donHangModel->capNhatTrangThai($id, $trangThaiMoi);
        header('Location: /admin/don-hang/chi-tiet?id=' . $id . '&success=status_updated');
        exit;
    }
}
