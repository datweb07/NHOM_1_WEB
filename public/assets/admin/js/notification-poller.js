/**
 * NotificationPoller - AJAX Polling for Admin Notifications
 * 
 * This class handles periodic polling of the notification API
 * and updates the UI with new notifications.
 * 
 * @author Admin Notification System
 * @version 1.0.0
 */
class NotificationPoller {
    /**
     * Constructor
     * 
     * @param {string} apiUrl - The API endpoint URL
     * @param {number} interval - Polling interval in milliseconds (default: 45000ms = 45s)
     */
    constructor(apiUrl, interval = 45000) {
        this.apiUrl = apiUrl;
        this.interval = interval;
        this.originalInterval = interval;
        this.timerId = null;
        this.failureCount = 0;
        this.maxFailures = 3;
        this.isActive = true;
        
        console.log('[NotificationPoller] Initialized with interval:', interval + 'ms');
    }

    /**
     * Start polling
     * Begins the polling cycle and sets up visibility handler
     */
    start() {
        if (this.timerId) {
            console.log('[NotificationPoller] Already running');
            return;
        }

        console.log('[NotificationPoller] Starting...');
        
        // Poll immediately on start
        this.poll();
        
        // Then set up interval for subsequent polls
        this.timerId = setInterval(() => this.poll(), this.interval);
        
        // Setup page visibility handler
        this.setupVisibilityHandler();
    }

    /**
     * Stop polling
     * Clears the interval timer
     */
    stop() {
        if (this.timerId) {
            console.log('[NotificationPoller] Stopping...');
            clearInterval(this.timerId);
            this.timerId = null;
        }
    }

    /**
     * Execute a single poll request
     * Fetches notifications from the API
     */
    async poll() {
        if (!this.isActive) {
            console.log('[NotificationPoller] Inactive, skipping poll');
            return;
        }

        try {
            console.log('[NotificationPoller] Polling...');
            
            const response = await fetch(this.apiUrl, {
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
            
            if (data.success) {
                this.handleSuccess(data);
            } else {
                throw new Error(data.error || 'API returned success=false');
            }
        } catch (error) {
            this.handleError(error);
        }
    }

    /**
     * Handle successful API response
     * Resets failure count and updates UI
     * 
     * @param {Object} data - The API response data
     */
    handleSuccess(data) {
        console.log('[NotificationPoller] Success:', data.total_notifications, 'notifications');
        
        // Reset failure count on success
        this.failureCount = 0;
        
        // Reset interval to original if it was increased due to failures
        if (this.interval !== this.originalInterval) {
            this.interval = this.originalInterval;
            this.stop();
            this.start();
        }
        
        // Calculate unread count (sum of counts for unread notifications)
        const unreadCount = (data.items || [])
            .filter(item => !item.is_read)
            .reduce((sum, item) => sum + (item.count || 1), 0);
        
        // Update UI components
        this.updateBadge(unreadCount);
        this.updateDropdown(data.items || []);
        this.clearWarning();
    }

    /**
     * Handle API error with exponential backoff
     * Increments failure count and shows warning after max failures
     * 
     * @param {Error} error - The error object
     */
    handleError(error) {
        console.error('[NotificationPoller] Error:', error.message);
        
        this.failureCount++;
        
        // Show warning after max failures
        if (this.failureCount >= this.maxFailures) {
            this.showWarning('Không thể tải thông báo. Đang thử lại...');
        }
        
        // Implement exponential backoff
        if (this.failureCount > 1) {
            const backoffInterval = Math.min(
                this.originalInterval * Math.pow(2, this.failureCount - 1),
                300000 // Max 5 minutes
            );
            
            console.log('[NotificationPoller] Backing off to', backoffInterval + 'ms');
            
            this.interval = backoffInterval;
            this.stop();
            this.start();
        }
    }

    /**
     * Update badge counter
     * Shows/hides badge based on notification count
     * 
     * @param {number} count - Number of unread notifications
     */
    updateBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (!badge) {
            console.warn('[NotificationPoller] Badge element not found');
            return;
        }

        badge.textContent = count;
        
        if (count > 0) {
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    /**
     * Update dropdown content
     * Renders notification items or empty message
     * 
     * @param {Array} items - Array of notification items
     */
    updateDropdown(items) {
        const dropdown = document.querySelector('.notification-dropdown-content');
        if (!dropdown) {
            console.warn('[NotificationPoller] Dropdown element not found');
            return;
        }

        if (items.length === 0) {
            dropdown.innerHTML = '<div class="dropdown-item text-center text-muted py-3">Không có thông báo mới</div>';
            return;
        }

        // Group by category
        const grouped = this.groupByCategory(items);
        
        // Render grouped notifications
        dropdown.innerHTML = this.renderGroupedNotifications(grouped);
        
        // Attach click handlers for read status management
        this.attachNotificationClickHandlers();
    }

    /**
     * Group notifications by category
     * 
     * @param {Array} items - Array of notification items
     * @returns {Object} Grouped notifications
     */
    groupByCategory(items) {
        const groups = {
            orders: [],
            inventory: [],
            customer: [],
            system: []
        };

        items.forEach(item => {
            if (groups[item.group]) {
                groups[item.group].push(item);
            }
        });

        return groups;
    }

    /**
     * Render grouped notifications HTML
     * 
     * @param {Object} grouped - Grouped notifications
     * @returns {string} HTML string
     */
    renderGroupedNotifications(grouped) {
        let html = '';
        
        const categoryLabels = {
            orders: 'Đơn hàng & Thanh toán',
            inventory: 'Kho hàng',
            customer: 'Khách hàng',
            system: 'Hệ thống'
        };

        const priorityOrder = { high: 0, medium: 1, low: 2 };

        for (const [category, items] of Object.entries(grouped)) {
            if (items.length === 0) continue;

            // Category header
            html += `<div class="dropdown-header"><strong>${categoryLabels[category]}</strong></div>`;
            
            // Sort by priority: high > medium > low
            items.sort((a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]);

            // Render each notification
            items.forEach(item => {
                const priorityClass = item.priority === 'high' ? 'text-danger' : 
                                     item.priority === 'medium' ? 'text-warning' : '';
                
                const countBadge = item.count > 1 ? 
                    `<span class="badge bg-secondary ms-2">${item.count}</span>` : '';
                
                const readClass = item.is_read ? 'notification-read' : 'notification-unread';
                
                html += `
                    <a href="#" 
                       class="dropdown-item notification-item ${readClass}" 
                       data-notification-id="${item.id}"
                       data-url-redirect="${item.url_redirect}">
                        <i class="bi ${item.icon} me-2 ${priorityClass}"></i>
                        <span>${item.message}</span>
                        ${countBadge}
                    </a>
                `;
            });

            html += '<div class="dropdown-divider"></div>';
        }

        return html;
    }

    /**
     * Attach click handlers to notification items
     * Integrates with ReadStatusManager for read status tracking
     */
    attachNotificationClickHandlers() {
        const notificationItems = document.querySelectorAll('.notification-item');
        
        notificationItems.forEach(item => {
            item.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const notificationId = item.getAttribute('data-notification-id');
                const urlRedirect = item.getAttribute('data-url-redirect');
                
                if (!notificationId || !urlRedirect) {
                    console.warn('[NotificationPoller] Missing notification ID or redirect URL');
                    return;
                }
                
                // Use ReadStatusManager if available
                if (window.readStatusManager) {
                    await window.readStatusManager.markAsRead(notificationId, urlRedirect);
                } else {
                    // Fallback: just navigate
                    window.location.href = urlRedirect;
                }
            });
        });
    }

    /**
     * Setup page visibility handler
     * Pauses polling when page is hidden, resumes when visible
     */
    setupVisibilityHandler() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('[NotificationPoller] Page hidden, pausing...');
                this.isActive = false;
                this.stop();
            } else {
                console.log('[NotificationPoller] Page visible, resuming...');
                this.isActive = true;
                this.start();
            }
        });
    }

    /**
     * Show warning message
     * Displays a warning in the dropdown
     * 
     * @param {string} message - Warning message
     */
    showWarning(message) {
        const dropdown = document.querySelector('.notification-dropdown-content');
        if (!dropdown) return;

        // Remove existing warning if any
        this.clearWarning();

        const warning = document.createElement('div');
        warning.className = 'dropdown-item text-warning notification-warning';
        warning.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>${message}`;
        
        dropdown.prepend(warning);
    }

    /**
     * Clear warning message
     * Removes the warning from dropdown
     */
    clearWarning() {
        const warning = document.querySelector('.notification-warning');
        if (warning) {
            warning.remove();
        }
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationPoller;
}
