<?php

namespace App\Controllers\Client;

require_once dirname(__DIR__, 2) . '/core/View.php';

use App\Core\View;

class AboutController
{
    public function gioiThieu()
    {
        View::render('client/about/gioi_thieu', [
            'title' => 'Giới thiệu về công ty - FPT Shop'
        ]);
    }

    public function quyCheShoatDong()
    {
        View::render('client/about/quy_che_hoat_dong', [
            'title' => 'Quy chế hoạt động - FPT Shop'
        ]);
    }

    public function gioiThieuMayDoiTra()
    {
        View::render('client/about/gioi_thieu_may_doi_tra', [
            'title' => 'Giới thiệu máy đổi trả - FPT Shop'
        ]);
    }

    public function chinhSachBaoHanh()
    {
        View::render('client/about/chinh_sach_bao_hanh', [
            'title' => 'Chính sách bảo hành - FPT Shop'
        ]);
    }

    public function chinhSachDoiTra()
    {
        View::render('client/about/chinh_sach_doi_tra', [
            'title' => 'Chính sách đổi trả - FPT Shop'
        ]);
    }

    public function chinhSachBaoMat()
    {
        View::render('client/about/chinh_sach_bao_mat', [
            'title' => 'Chính sách bảo mật - FPT Shop'
        ]);
    }

    public function cauHoiThuongGap()
    {
        View::render('client/about/cau_hoi_thuong_gap', [
            'title' => 'Câu hỏi thường gặp - FPT Shop'
        ]);
    }

    public function apple()
    {
        View::render('client/about/apple', [
            'title' => 'Đại lý uỷ quyền và TTBH uỷ quyền của Apple - FPT Shop'
        ]);
    }

    public function mangDiDong()
    {
        View::render('client/about/mang_di_dong', [
            'title' => 'Chính sách mạng di động FPT - FPT Shop'
        ]);
    }

    public function goiCuoc()
    {
        View::render('client/about/goi_cuoc', [
            'title' => 'Chính sách gói cước di động FPT - FPT Shop'
        ]);
    }

    public function diemCungCap()
    {
        View::render('client/about/diem_cung_cap', [
            'title' => 'Danh sách điểm cung cấp dịch vụ viễn thông FPT - FPT Shop'
        ]);
    }

    public function giaoHang()
    {
        View::render('client/about/giao_hang', [
            'title' => 'Chính sách Chương trình Khách hàng thân thiết tại FPT Shop - FPT Shop'
        ]);
    }

    public function giaoHangDienMay()
    {
        View::render('client/about/giao_hang_dien_may', [
            'title' => 'Chính sách giao hàng & lắp đặt Điện máy, Gia dụng - FPT Shop'
        ]);
    }

    public function giaoHangOnline()
    {
        View::render('client/about/giao_hang_online', [
            'title' => 'Chính sách giao hàng & lắp đặt Điện máy chỉ bán online - FPT Shop'
        ]);
    }

    public function khachHangThanThiet()
    {
        View::render('client/about/khach_hang_than_thiet', [
            'title' => 'Chính sách Chương trình Khách hàng thân thiết tại FPT Shop - FPT Shop'
        ]);
    }

    public function khuiHop()
    {
        View::render('client/about/khui_hop', [
            'title' => 'Chính sách khui hộp sản phẩm - FPT Shop'
        ]);
    }

    public function muaHangOnline()
    {
        View::render('client/about/mua_hang_online', [
            'title' => 'Hướng dẫn mua hàng và thanh toán online - FPT Shop'
        ]);
    }

    public function mayDoiTra()
    {
        View::render('client/about/may_doi_tra', [
            'title' => 'Giới thiệu máy đổi trả - FPT Shop'
        ]);
    }

    public function doiTra()
    {
        View::render('client/about/doi_tra', [
            'title' => 'Chính sách đổi trả - FPT Shop'
        ]);
    }

    public function baoMatDuLieu()
    {
        View::render('client/about/bao_mat_du_lieu', [
            'title' => 'Chính sách bảo mật dữ liệu cá nhân khách hàng - FPT Shop'
        ]);
    }

    public function quyChe()
    {
        View::render('client/about/quy_che', [
            'title' => 'QUY CHẾ HOẠT ĐỘNG WEBSITE CUNG CẤP DỊCH VỤ TMĐT FPTSHOP.COM.VN - FPT Shop'
        ]);
    }

    public function baoMat()
    {
        View::render('client/about/bao_mat', [
            'title' => 'Chính sách bảo mật - FPT Shop'
        ]);
    }

    public function hoTroKyThuat()
    {
        View::render('client/about/ho_tro_ky_thuat', [
            'title' => 'Quy định hỗ trợ kỹ thuật và sao lưu dữ liệu - FPT Shop'
        ]);
    }

    public function baoHanh()
    {
        View::render('client/about/bao_hanh', [
            'title' => 'Chính sách bảo hành - FPT Shop'
        ]);
    }

    public function traGop()
    {
        View::render('client/about/tra_gop', [
            'title' => 'Chính sách trả góp - FPT Shop'
        ]);
    }
}
