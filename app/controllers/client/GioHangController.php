<?php

require_once dirname(__DIR__, 2) . '/core/Session.php';

class GioHangController
{
    public function addToCart($id): void
    {
        \App\Core\Session::start();
        $this->initCart();

        $productId = (int)$id;
        if ($productId <= 0) {
            return;
        }

        $ten = trim((string)($_POST['ten'] ?? 'San pham #' . $productId));
        $gia = (float)($_POST['gia'] ?? 0);
        $soLuong = (int)($_POST['so_luong'] ?? 1);

        if ($soLuong <= 0) {
            $soLuong = 1;
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['so_luong'] += $soLuong;
            return;
        }

        $_SESSION['cart'][$productId] = [
            'ten' => $ten,
            'gia' => $gia,
            'so_luong' => $soLuong,
        ];
    }

    public function remove($id): void
    {
        \App\Core\Session::start();
        $this->initCart();

        $productId = (int)$id;
        if ($productId <= 0) {
            return;
        }

        unset($_SESSION['cart'][$productId]);
    }

    public function update($id, $qty): void
    {
        \App\Core\Session::start();
        $this->initCart();

        $productId = (int)$id;
        $soLuongMoi = (int)$qty;

        if ($productId <= 0 || !isset($_SESSION['cart'][$productId])) {
            return;
        }

        if ($soLuongMoi <= 0) {
            unset($_SESSION['cart'][$productId]);
            return;
        }

        $_SESSION['cart'][$productId]['so_luong'] = $soLuongMoi;
    }

    public function clear(): void
    {
        \App\Core\Session::start();
        unset($_SESSION['cart']);
    }

    public function getCart(): array
    {
        \App\Core\Session::start();
        $this->initCart();

        return $_SESSION['cart'];
    }

    public function tinhTongTien(): float
    {
        \App\Core\Session::start();
        $this->initCart();

        $tongTien = 0;
        foreach ($_SESSION['cart'] as $item) {
            $gia = (float)($item['gia'] ?? 0);
            $soLuong = (int)($item['so_luong'] ?? 0);
            if ($gia < 0) {
                $gia = 0;
            }
            if ($soLuong < 0) {
                $soLuong = 0;
            }
            $tongTien += $gia * $soLuong;
        }

        return $tongTien;
    }

    public function tinhTongSoLuong(): int
    {
        \App\Core\Session::start();
        $this->initCart();

        $tongSoLuong = 0;
        foreach ($_SESSION['cart'] as $item) {
            $tongSoLuong += (int)($item['so_luong'] ?? 0);
        }

        return $tongSoLuong;
    }

    private function initCart(): void
    {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
}
