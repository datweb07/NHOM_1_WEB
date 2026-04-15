<?php
// Get current URL path
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$currentPath = strtok($currentPath, '?'); // Remove query string

// Helper function to check if current path matches
function isActive($path) {
    global $currentPath;
    return strpos($currentPath, $path) === 0 ? 'active' : '';
}

// Helper function to check if menu should be open
function isMenuOpen($paths) {
    global $currentPath;
    foreach ($paths as $path) {
        if (strpos($currentPath, $path) === 0) {
            return 'menu-open';
        }
    }
    return '';
}
?>
<!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="/admin/dashboard" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="<?= ASSET_URL ?>/assets/client/images/others/fpt-shop-banner.png"
              alt="FPT SHOP Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">FPT SHOP</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="menu"
              data-accordion="false"
            >
              <li class="nav-item">
                <a href="/admin/dashboard" class="nav-link <?= isActive('/admin/dashboard') ?>">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Dashboard
    
                  </p>
                </a>
              </li>
              <li class="nav-item <?= isMenuOpen(['/admin/san-pham', '/admin/danh-muc']) ?>">
                <a href="#" class="nav-link <?= isActive('/admin/san-pham') || isActive('/admin/danh-muc') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-box-seam-fill"></i>
                  <p>
                    Sản Phẩm
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/san-pham" class="nav-link <?= isActive('/admin/san-pham') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Danh Sách Sản Phẩm</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/admin/san-pham/them" class="nav-link <?= isActive('/admin/san-pham/them') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Thêm Sản Phẩm</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/admin/danh-muc" class="nav-link <?= isActive('/admin/danh-muc') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Danh Mục</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item <?= isMenuOpen(['/admin/don-hang']) ?>">
                <a href="#" class="nav-link <?= isActive('/admin/don-hang') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-cart-fill"></i>
                  <p>
                    Đơn Hàng
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/don-hang" class="nav-link <?= isActive('/admin/don-hang') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Danh Sách Đơn Hàng</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item <?= isMenuOpen(['/admin/thanh-toan']) ?>">
                <a href="#" class="nav-link <?= isActive('/admin/thanh-toan') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-credit-card-fill"></i>
                  <p>
                    Thanh Toán
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/thanh-toan" class="nav-link <?= isActive('/admin/thanh-toan') && !strpos($currentPath, '/health') ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Duyệt Thanh Toán</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/admin/thanh-toan/health" class="nav-link <?= isActive('/admin/thanh-toan/health') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Tình Trạng Gateway</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item <?= isMenuOpen(['/admin/khuyen-mai', '/admin/ma-giam-gia']) ?>">
                <a href="#" class="nav-link <?= isActive('/admin/khuyen-mai') || isActive('/admin/ma-giam-gia') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-tag-fill"></i>
                  <p>
                    Khuyến Mãi
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/khuyen-mai" class="nav-link <?= isActive('/admin/khuyen-mai') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Chương Trình Khuyến Mãi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/admin/ma-giam-gia" class="nav-link <?= isActive('/admin/ma-giam-gia') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Mã Giảm Giá</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item <?= isMenuOpen(['/admin/nguoi-dung']) ?>">
                <a href="#" class="nav-link <?= isActive('/admin/nguoi-dung') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-people-fill"></i>
                  <p>
                    Người Dùng
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/nguoi-dung" class="nav-link <?= isActive('/admin/nguoi-dung') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Danh Sách</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item <?= isMenuOpen(['/admin/danh-gia']) ?>">
                <a href="#" class="nav-link <?= isActive('/admin/danh-gia') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-star-fill"></i>
                  <p>
                    Đánh Giá
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/danh-gia" class="nav-link <?= isActive('/admin/danh-gia') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Danh Sách</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item <?= isMenuOpen(['/admin/banner']) ?>">
                <a href="#" class="nav-link <?= isActive('/admin/banner') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-image-fill"></i>
                  <p>
                    Banner Quảng Cáo
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/banner" class="nav-link <?= isActive('/admin/banner') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Danh Sách</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/admin/banner/them" class="nav-link <?= isActive('/admin/banner/them') ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Thêm Banner</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->