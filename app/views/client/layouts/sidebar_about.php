<?php
$active_page = $active_page ?? '';
?>

<style>
    .font-size-toggle {
        display: inline-flex;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .font-size-toggle .btn-font {
        padding: 6px 18px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #6c757d;
        outline: none;
        transition: all 0.2s ease;
    }

    .font-size-toggle .btn-font.active {
        background-color: #212529;
        color: #ffffff;
        border-radius: 20px;
    }

    .sidebar-title {
        padding: 12px 16px;
        font-size: 14px;
        color: #6c757d;
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-weight: bold;
        margin-bottom: 0;
    }

    .support-sidebar .list-group-item {
        border: none;
        padding: 12px 16px;
        font-size: 14.5px;
        color: #495057;
        background-color: transparent;
        border-left: 3px solid transparent;
        border-radius: 0 8px 8px 0 !important;
        margin-bottom: 2px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .support-sidebar .list-group-item:hover {
        color: #cb1c22;
        background-color: #f8f9fa;
        padding-left: 24px;
        cursor: pointer;
    }

    .support-sidebar .list-group-item.active {
        background-color: transparent;
        color: #212529;
        font-weight: 700;
        border-left: 5px solid #cb1c22;
    }

    .support-sidebar .list-group-item.active:hover {
        padding-left: 24px;
    }
</style>

<div class="font-size-toggle">
    <button type="button" id="btn-font-small" class="btn-font active">Cỡ chữ nhỏ</button>
    <button type="button" id="btn-font-large" class="btn-font">Cỡ chữ lớn</button>
</div>

<div class="list-group support-sidebar sticky-top" style="top: 20px;">
    <div class="sidebar-title">Danh mục chính sách</div>
    <a href="/cau-hoi-thuong-gap" class="list-group-item list-group-item-action <?= $active_page === 'cau-hoi-thuong-gap' ? 'active' : '' ?>">Câu hỏi thường gặp</a>
    <a href="/gioi-thieu" class="list-group-item list-group-item-action <?= $active_page === 'gioi-thieu' ? 'active' : '' ?>">Giới thiệu về FPT Shop</a>
    <a href="/apple" class="list-group-item list-group-item-action <?= $active_page === 'apple' ? 'active' : '' ?>">Đại lý uỷ quyền và TTBH uỷ quyền của Apple</a>
    <a href="/mang-di-dong" class="list-group-item list-group-item-action <?= $active_page === 'mang-di-dong' ? 'active' : '' ?>">Chính sách mạng di động FPT</a>
    <a href="/goi-cuoc" class="list-group-item list-group-item-action <?= $active_page === 'goi-cuoc' ? 'active' : '' ?>">Chính sách gói cước di động FPT</a>
    <a href="/diem-cung-cap" class="list-group-item list-group-item-action <?= $active_page === 'diem-cung-cap' ? 'active' : '' ?>">Danh sách điểm cung cấp dịch vụ viễn thông FPT</a>
    <a href="/giao-hang" class="list-group-item list-group-item-action <?= $active_page === 'giao-hang' ? 'active' : '' ?>">Chính sách giao hàng & lắp đặt</a>
    <a href="/giao-hang-dien-may" class="list-group-item list-group-item-action <?= $active_page === 'giao-hang-dien-may' ? 'active' : '' ?>">Chính sách giao hàng & lắp đặt Điện máy, Gia dụng</a>
    <a href="/giao-hang-online" class="list-group-item list-group-item-action <?= $active_page === 'giao-hang-online' ? 'active' : '' ?>">Chính sách giao hàng & lắp đặt Điện máy chỉ bán online</a>
    <a href="/khach-hang-than-thiet" class="list-group-item list-group-item-action <?= $active_page === 'khach-hang-than-thiet' ? 'active' : '' ?>">Chính sách Chương trình Khách hàng thân thiết tại FPT Shop</a>
    <a href="/khui-hop" class="list-group-item list-group-item-action <?= $active_page === 'khui-hop' ? 'active' : '' ?>">Chính sách khui hộp sản phẩm</a>
    <a href="/mua-hang-online" class="list-group-item list-group-item-action <?= $active_page === 'mua-hang-online' ? 'active' : '' ?>">Hướng dẫn mua hàng và thanh toán online</a>
    <a href="/may-doi-tra" class="list-group-item list-group-item-action <?= $active_page === 'may-doi-tra' ? 'active' : '' ?>">Giới thiệu máy đổi trả</a>
    <a href="/doi-tra" class="list-group-item list-group-item-action <?= $active_page === 'doi-tra' ? 'active' : '' ?>">Chính sách đổi trả</a>
    <a href="/bao-mat-du-lieu" class="list-group-item list-group-item-action <?= $active_page === 'bao-mat-du-lieu' ? 'active' : '' ?>">Chính sách bảo mật dữ liệu cá nhân khách hàng</a>
    <a href="/quy-che" class="list-group-item list-group-item-action <?= $active_page === 'quy-che' ? 'active' : '' ?>">Quy chế hoạt động</a>
    <a href="/bao-mat" class="list-group-item list-group-item-action <?= $active_page === 'bao-mat' ? 'active' : '' ?>">Chính sách bảo mật</a>
    <a href="/ho-tro-ky-thuat" class="list-group-item list-group-item-action <?= $active_page === 'ho-tro-ky-thuat' ? 'active' : '' ?>">Quy định hỗ trợ kỹ thuật và sao lưu dữ liệu</a>
    <a href="/bao-hanh" class="list-group-item list-group-item-action <?= $active_page === 'bao-hanh' ? 'active' : '' ?>">Chính sách bảo hành</a>
    <a href="/tra-gop" class="list-group-item list-group-item-action <?= $active_page === 'tra-gop' ? 'active' : '' ?>">Chính sách trả góp</a>
</div>