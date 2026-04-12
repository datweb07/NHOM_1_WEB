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
}
