/**
 * NotificationListPage - Notification list page functionality
 * 
 * This class handles the notification list page with filtering,
 * sorting, pagination, and read status management.
 * 
 * @author Notification Read Status System
 * @version 1.0.0
 */
class NotificationListPage {
    /**
     * Constructor
     */
    constructor() {
        this.apiUrl = '/admin/api/notifications/list';
        this.currentPage = 1;
        this.perPage = 20;
        this.filters = {
            category: 'all',
            priority: 'all',
            status: 'all',
            sortBy: 'time',
            sortOrder: 'desc'
        };
        this.readStatusManager = null;
        
        console.log('[NotificationListPage] Initialized');
    }

    /**
     * Initialize the page
     */
    init() {
        // Initialize ReadStatusManager
        this.readStatusManager = new ReadStatusManager();
        window.readStatusManager = this.readStatusManager;

        // Load filters from URL
        this.loadFiltersFromURL();

        // Setup event listeners
        this.setupEventListeners();

        // Load notifications
        this.loadNotifications();
    }

    /**
     * Load filters from URL query parameters
     */
    loadFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);

        this.currentPage = parseInt(urlParams.get('page')) || 1;
        this.filters.category = urlParams.get('category') || 'all';
        this.filters.priority = urlParams.get('priority') || 'all';
        this.filters.status = urlParams.get('status') || 'all';

        const sort = urlParams.get('sort') || 'time-desc';
        const [sortBy, sortOrder] = sort.split('-');
        this.filters.sortBy = sortBy;
        this.filters.sortOrder = sortOrder;

        // Update filter controls
        document.getElementById('filter-category').value = this.filters.category;
        document.getElementById('filter-priority').value = this.filters.priority;
        document.getElementById('filter-status').value = this.filters.status;
        document.getElementById('filter-sort').value = `${sortBy}-${sortOrder}`;
    }

    /**
     * Update URL with current filters
     */
    updateURL() {
        const params = new URLSearchParams();
        params.set('page', this.currentPage);
        params.set('category', this.filters.category);
        params.set('priority', this.filters.priority);
        params.set('status', this.filters.status);
        params.set('sort', `${this.filters.sortBy}-${this.filters.sortOrder}`);

        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({}, '', newUrl);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Filter change handlers
        document.getElementById('filter-category').addEventListener('change', () => {
            this.filters.category = document.getElementById('filter-category').value;
            this.currentPage = 1;
            this.updateURL();
            this.loadNotifications();
        });

        document.getElementById('filter-priority').addEventListener('change', () => {
            this.filters.priority = document.getElementById('filter-priority').value;
            this.currentPage = 1;
            this.updateURL();
            this.loadNotifications();
        });

        document.getElementById('filter-status').addEventListener('change', () => {
            this.filters.status = document.getElementById('filter-status').value;
            this.currentPage = 1;
            this.updateURL();
            this.loadNotifications();
        });

        document.getElementById('filter-sort').addEventListener('change', () => {
            const sortValue = document.getElementById('filter-sort').value;
            const [sortBy, sortOrder] = sortValue.split('-');
            this.filters.sortBy = sortBy;
            this.filters.sortOrder = sortOrder;
            this.currentPage = 1;
            this.updateURL();
            this.loadNotifications();
        });

        // Reset filters button
        document.getElementById('reset-filters-btn').addEventListener('click', () => {
            this.resetFilters();
        });

        // Mark all as read button
        document.getElementById('mark-all-read-btn').addEventListener('click', () => {
            this.markAllAsRead();
        });
    }

    /**
     * Reset filters to defaults
     */
    resetFilters() {
        this.currentPage = 1;
        this.filters = {
            category: 'all',
            priority: 'all',
            status: 'all',
            sortBy: 'time',
            sortOrder: 'desc'
        };

        document.getElementById('filter-category').value = 'all';
        document.getElementById('filter-priority').value = 'all';
        document.getElementById('filter-status').value = 'all';
        document.getElementById('filter-sort').value = 'time-desc';

        this.updateURL();
        this.loadNotifications();
    }

    /**
     * Load notifications from API
     */
    async loadNotifications() {
        console.log('[NotificationListPage] Loading notifications...');

        // Show loading state
        document.getElementById('loading-state').style.display = 'block';
        document.getElementById('notification-list').style.display = 'none';
        document.getElementById('empty-state').style.display = 'none';
        document.getElementById('pagination-container').style.display = 'none';

        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.perPage,
                category: this.filters.category,
                priority: this.filters.priority,
                status: this.filters.status,
                sort_by: this.filters.sortBy,
                sort_order: this.filters.sortOrder
            });

            const response = await fetch(`${this.apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to load notifications');
            }

            console.log('[NotificationListPage] Loaded', data.notifications.length, 'notifications');

            // Hide loading state
            document.getElementById('loading-state').style.display = 'none';

            // Render notifications
            if (data.notifications.length === 0) {
                document.getElementById('empty-state').style.display = 'block';
            } else {
                this.renderNotifications(data.notifications);
                this.renderPagination(data.pagination);
                document.getElementById('notification-list').style.display = 'block';
                document.getElementById('pagination-container').style.display = 'block';
            }

        } catch (error) {
            console.error('[NotificationListPage] Load failed:', error);

            // Hide loading state
            document.getElementById('loading-state').style.display = 'none';

            // Show error message
            document.getElementById('notification-list').innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Không thể tải thông báo. Vui lòng thử lại sau.
                </div>
            `;
            document.getElementById('notification-list').style.display = 'block';
        }
    }

    /**
     * Render notifications
     * 
     * @param {Array} notifications - Array of notification objects
     */
    renderNotifications(notifications) {
        const container = document.getElementById('notification-list');
        let html = '<div class="list-group">';

        notifications.forEach(notification => {
            const readClass = notification.is_read ? 'notification-read' : 'notification-unread';
            const priorityClass = notification.priority === 'high' ? 'text-danger' : 
                                 notification.priority === 'medium' ? 'text-warning' : 'text-muted';
            const priorityLabel = notification.priority === 'high' ? 'Cao' : 
                                 notification.priority === 'medium' ? 'Trung bình' : 'Thấp';
            
            const categoryLabels = {
                orders: 'Đơn hàng & Thanh toán',
                inventory: 'Kho hàng',
                customer: 'Khách hàng',
                system: 'Hệ thống'
            };
            const categoryLabel = categoryLabels[notification.group] || notification.group;

            const unreadIndicator = notification.is_read ? '' : '<span class="unread-indicator"></span>';
            const markUnreadBtn = notification.is_read ? 
                `<button class="btn btn-sm btn-outline-secondary mark-unread-btn" data-notification-id="${notification.id}">
                    <i class="bi bi-envelope"></i> Đánh dấu chưa đọc
                </button>` : '';

            html += `
                <div class="list-group-item notification-list-item ${readClass}" data-notification-id="${notification.id}">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1 notification-content" data-url-redirect="${notification.url_redirect}">
                            ${unreadIndicator}
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi ${notification.icon} me-2 ${priorityClass}" style="font-size: 1.5rem;"></i>
                                <h6 class="mb-0">${notification.message}</h6>
                            </div>
                            <div class="notification-meta">
                                <span class="badge bg-secondary me-2">${categoryLabel}</span>
                                <span class="badge ${priorityClass === 'text-danger' ? 'bg-danger' : priorityClass === 'text-warning' ? 'bg-warning' : 'bg-secondary'} me-2">
                                    Ưu tiên: ${priorityLabel}
                                </span>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    ${this.formatTimestamp(notification.timestamp)}
                                </small>
                            </div>
                        </div>
                        <div class="notification-actions">
                            ${markUnreadBtn}
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;

        // Attach event listeners
        this.attachNotificationEventListeners();
    }

    /**
     * Attach event listeners to notification items
     */
    attachNotificationEventListeners() {
        // Click on notification content to navigate
        document.querySelectorAll('.notification-content').forEach(content => {
            content.addEventListener('click', async (e) => {
                const listItem = content.closest('.notification-list-item');
                const notificationId = listItem.getAttribute('data-notification-id');
                const urlRedirect = content.getAttribute('data-url-redirect');

                if (!notificationId || !urlRedirect) {
                    console.warn('[NotificationListPage] Missing notification ID or redirect URL');
                    return;
                }

                // Mark as read and navigate
                await this.readStatusManager.markAsRead(notificationId, urlRedirect);
            });
        });

        // Mark as unread buttons
        document.querySelectorAll('.mark-unread-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                const notificationId = btn.getAttribute('data-notification-id');

                if (!notificationId) {
                    console.warn('[NotificationListPage] Missing notification ID');
                    return;
                }

                // Mark as unread
                const success = await this.readStatusManager.markAsUnread(notificationId);

                if (success) {
                    // Reload notifications to update UI
                    this.loadNotifications();
                }
            });
        });
    }

    /**
     * Render pagination
     * 
     * @param {Object} pagination - Pagination metadata
     */
    renderPagination(pagination) {
        const paginationContainer = document.getElementById('pagination');
        let html = '';

        // Previous button
        html += `
            <li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.page - 1}">
                    <i class="bi bi-chevron-left"></i> Trước
                </a>
            </li>
        `;

        // Page numbers (show 5 pages at a time)
        const startPage = Math.max(1, pagination.page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.page + 2);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === pagination.page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < pagination.total_pages) {
            if (endPage < pagination.total_pages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.total_pages}">${pagination.total_pages}</a></li>`;
        }

        // Next button
        html += `
            <li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.page + 1}">
                    Sau <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;

        paginationContainer.innerHTML = html;

        // Pagination info
        const start = (pagination.page - 1) * pagination.per_page + 1;
        const end = Math.min(pagination.page * pagination.per_page, pagination.total);
        document.getElementById('pagination-info').textContent = 
            `Hiển thị ${start}-${end} trong tổng số ${pagination.total} thông báo`;

        // Attach pagination click handlers
        document.querySelectorAll('#pagination .page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.getAttribute('data-page'));
                if (page && page !== this.currentPage) {
                    this.currentPage = page;
                    this.updateURL();
                    this.loadNotifications();
                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        const confirmed = confirm('Bạn có chắc chắn muốn đánh dấu tất cả thông báo là đã đọc?');
        if (!confirmed) {
            return;
        }

        // Get all notification IDs (we'll use the API to mark all)
        const success = await this.readStatusManager.markAllAsRead([]);

        if (success) {
            // Reload notifications to update UI
            this.loadNotifications();
        }
    }

    /**
     * Format timestamp for display
     * 
     * @param {string} timestamp - ISO timestamp
     * @returns {string} Formatted timestamp
     */
    formatTimestamp(timestamp) {
        if (!timestamp) {
            return 'Không rõ';
        }

        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) {
            return 'Vừa xong';
        } else if (diffMins < 60) {
            return `${diffMins} phút trước`;
        } else if (diffHours < 24) {
            return `${diffHours} giờ trước`;
        } else if (diffDays < 7) {
            return `${diffDays} ngày trước`;
        } else {
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationListPage;
}
