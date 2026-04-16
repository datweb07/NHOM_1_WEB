<footer class="app-footer">
        <div class="float-end d-none d-sm-inline">
          <b>Hệ thống Quản trị</b> FPT Shop
        </div>
        <strong>
          Copyright &copy; 2026&nbsp;
          <a href="https://fpt-shop.onrender.com/" class="text-decoration-none" style="color: #cb1c22;">FPT Shop</a>.
        </strong>
        Bản quyền thuộc về Công ty Cổ phần Bán lẻ Kỹ thuật số FPT.
        </footer>
      </div>
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <script src="<?= ASSET_URL ?>/assets/admin/js/admin.js"></script>
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <script
      src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
      integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ="
      crossorigin="anonymous"
    ></script>
    <script>
      const connectedSortables = document.querySelectorAll('.connectedSortable');
      connectedSortables.forEach((connectedSortable) => {
        let sortable = new Sortable(connectedSortable, {
          group: 'shared',
          handle: '.card-header',
        });
      });

      const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
      cardHeaders.forEach((cardHeader) => {
        cardHeader.style.cursor = 'move';
      });
    </script>
    <script
      src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
      integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
      crossorigin="anonymous"
    ></script>
    <script>
      // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
      // IT'S ALL JUST JUNK FOR DEMO
      // ++++++++++++++++++++++++++++++++++++++++++

      const sales_chart_options = {
        series: [
          {
            name: 'Digital Goods',
            data: [28, 48, 40, 19, 86, 27, 90],
          },
          {
            name: 'Electronics',
            data: [65, 59, 80, 81, 56, 55, 40],
          },
        ],
        chart: {
          height: 300,
          type: 'area',
          toolbar: {
            show: false,
          },
        },
        legend: {
          show: false,
        },
        colors: ['#0d6efd', '#20c997'],
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: 'smooth',
        },
        xaxis: {
          type: 'datetime',
          categories: [
            '2023-01-01',
            '2023-02-01',
            '2023-03-01',
            '2023-04-01',
            '2023-05-01',
            '2023-06-01',
            '2023-07-01',
          ],
        },
        tooltip: {
          x: {
            format: 'MMMM yyyy',
          },
        },
      };

      const revenueChartEl = document.querySelector('#revenue-chart');
if (revenueChartEl) {
  const sales_chart = new ApexCharts(revenueChartEl, sales_chart_options);
  sales_chart.render();
}
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
          const currentPath = window.location.pathname;
          
          // Tìm link active trong treeview
          document.querySelectorAll('.nav-treeview .nav-link').forEach(function (link) {
            const href = link.getAttribute('href');
            if (href && (currentPath === href || currentPath.startsWith(href + '/') || currentPath.startsWith(href))) {
              // Đánh dấu link này active
              link.classList.add('active');
              
              // Mở menu cha chứa link này
              const parentNavItem = link.closest('.nav-treeview')?.closest('.nav-item');
              if (parentNavItem) {
                parentNavItem.classList.add('menu-open');
                const treeview = parentNavItem.querySelector('.nav-treeview');
                if (treeview) {
                  treeview.style.display = 'block';
                }
              }
            }
          });
        }, 50); // Đợi AdminLTE init xong
      });
    </script>
    <!--begin::Notification System Script-->
    <script src="<?= ASSET_URL ?>/assets/admin/js/read-status-manager.js"></script>
    <script src="<?= ASSET_URL ?>/assets/admin/js/notification-poller.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Initialize ReadStatusManager
        window.readStatusManager = new ReadStatusManager();
        
        // Initialize notification poller with 45-second interval
        const notificationPoller = new NotificationPoller('/admin/api/notifications', 45000);
        notificationPoller.start();
        
        console.log('[Admin] Notification system initialized');
      });
    </script>
    <!--end::Notification System Script-->
    </body>
  </html>