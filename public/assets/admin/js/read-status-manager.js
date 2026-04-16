/**
 * ReadStatusManager - Manages notification read/unread status
 * 
 * This class handles marking notifications as read/unread,
 * browser back button undo functionality, and UI updates.
 * 
 * @author Notification Read Status System
 * @version 1.0.0
 */
class ReadStatusManager {
    /**
     * Constructor
     */
    constructor() {
        this.setupBackButtonHandler();
        console.log('[ReadStatusManager] Initialized');
    }

    /**
     * Mark notification as read and push history state
     * 
     * @param {string} notificationId - Notification ID
     * @param {string} urlRedirect - URL to redirect to after marking as read
     * @returns {Promise<boolean>} Success status
     */
    async markAsRead(notificationId, urlRedirect) {
        console.log('[ReadStatusManager] Marking as read:', notificationId);

        try {
            const response = await fetch('/admin/api/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ notification_id: notificationId })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to mark as read');
            }

            console.log('[ReadStatusManager] Marked as read successfully');

            // Optimistic UI update AFTER successful API call
            this.updateNotificationUI(notificationId, true);
            this.updateBadgeCounter(-1);

            // Push history state for undo functionality
            if (urlRedirect) {
                const currentUrl = window.location.href;
                history.pushState(
                    {
                        action: 'mark_read',
                        notification_id: notificationId,
                        previous_url: currentUrl,
                        timestamp: Date.now()
                    },
                    '',
                    currentUrl
                );

                // Navigate to redirect URL
                window.location.href = urlRedirect;
            }

            return true;

        } catch (error) {
            console.error('[ReadStatusManager] Mark as read failed:', error);

            // Show error message
            this.showError('Không thể đánh dấu thông báo đã đọc. Vui lòng thử lại.');

            return false;
        }
    }

    /**
     * Mark notification as unread
     * 
     * @param {string} notificationId - Notification ID
     * @returns {Promise<boolean>} Success status
     */
    async markAsUnread(notificationId) {
        console.log('[ReadStatusManager] Marking as unread:', notificationId);

        // Optimistic UI update
        this.updateNotificationUI(notificationId, false);
        this.updateBadgeCounter(1);

        try {
            const response = await fetch('/admin/api/notifications/mark-unread', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ notification_id: notificationId })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to mark as unread');
            }

            console.log('[ReadStatusManager] Marked as unread successfully');
            return true;

        } catch (error) {
            console.error('[ReadStatusManager] Mark as unread failed:', error);

            // Revert UI changes
            this.updateNotificationUI(notificationId, true);
            this.updateBadgeCounter(-1);

            // Show error message
            this.showError('Không thể đánh dấu thông báo chưa đọc. Vui lòng thử lại.');

            return false;
        }
    }

    /**
     * Mark all notifications as read
     * 
     * @param {Array<string>} notificationIds - Array of notification IDs
     * @returns {Promise<boolean>} Success status
     */
    async markAllAsRead(notificationIds) {
        console.log('[ReadStatusManager] Marking all as read:', notificationIds.length, 'notifications');

        // Optimistic UI update
        notificationIds.forEach(id => {
            this.updateNotificationUI(id, true);
        });
        this.updateBadgeCounter(-notificationIds.length);

        try {
            const response = await fetch('/admin/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({})
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to mark all as read');
            }

            console.log('[ReadStatusManager] Marked all as read successfully:', data.marked_count);

            // Show confirmation message
            this.showSuccess('Đã đánh dấu tất cả là đã đọc');

            return true;

        } catch (error) {
            console.error('[ReadStatusManager] Mark all as read failed:', error);

            // Revert UI changes
            notificationIds.forEach(id => {
                this.updateNotificationUI(id, false);
            });
            this.updateBadgeCounter(notificationIds.length);

            // Show error message
            this.showError('Không thể đánh dấu tất cả thông báo đã đọc. Vui lòng thử lại.');

            return false;
        }
    }

    /**
     * Setup browser back button handler
     * Listens for popstate events and marks notification as unread
     */
    setupBackButtonHandler() {
        window.addEventListener('popstate', (event) => {
            const state = event.state;

            if (!state || !state.action || !state.notification_id) {
                console.log('[ReadStatusManager] Invalid history state, skipping undo');
                return;
            }

            if (state.action === 'mark_read') {
                console.log('[ReadStatusManager] Back button detected, marking as unread:', state.notification_id);
                this.markAsUnread(state.notification_id);
            }
        });
    }

    /**
     * Update notification UI styling
     * 
     * @param {string} notificationId - Notification ID
     * @param {boolean} isRead - Whether notification is read
     */
    updateNotificationUI(notificationId, isRead) {
        // Find notification elements by data-id attribute
        const notificationElements = document.querySelectorAll(`[data-notification-id="${notificationId}"]`);

        notificationElements.forEach(element => {
            if (isRead) {
                element.classList.add('notification-read');
                element.classList.remove('notification-unread');
            } else {
                element.classList.add('notification-unread');
                element.classList.remove('notification-read');
            }
        });
    }

    /**
     * Update badge counter
     * 
     * @param {number} delta - Change in counter (positive or negative)
     */
    updateBadgeCounter(delta) {
        const badge = document.querySelector('.notification-badge');
        if (!badge) {
            console.warn('[ReadStatusManager] Badge element not found');
            return;
        }

        let currentCount = parseInt(badge.textContent) || 0;
        let newCount = Math.max(0, currentCount + delta);

        badge.textContent = newCount;

        if (newCount > 0) {
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    /**
     * Show error message
     * 
     * @param {string} message - Error message
     */
    showError(message) {
        // Use browser alert for now (can be replaced with toast notification)
        alert(message);
    }

    /**
     * Show success message
     * 
     * @param {string} message - Success message
     */
    showSuccess(message) {
        // Use browser alert for now (can be replaced with toast notification)
        alert(message);
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ReadStatusManager;
}
