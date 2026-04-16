<?php

require_once __DIR__ . '/../redis/RedisService.php';
require_once __DIR__ . '/../../models/entities/DonHang.php';
require_once __DIR__ . '/../../models/entities/ThanhToan.php';
require_once __DIR__ . '/../../models/entities/Refund.php';
require_once __DIR__ . '/../../models/entities/PhienBanSanPham.php';
require_once __DIR__ . '/../../models/entities/DanhGia.php';
require_once __DIR__ . '/../../models/entities/TransactionLog.php';
require_once __DIR__ . '/../../models/entities/GatewayHealth.php';
require_once __DIR__ . '/../../models/entities/MaGiamGia.php';

class NotificationService
{
    private RedisService $redis;
    private DonHang $donHangModel;
    private ThanhToan $thanhToanModel;
    private Refund $refundModel;
    private PhienBanSanPham $phienBanModel;
    private DanhGia $danhGiaModel;
    private TransactionLog $transactionLogModel;
    private GatewayHealth $gatewayHealthModel;
    private MaGiamGia $maGiamGiaModel;

    private const REDIS_KEY_PREFIX = 'notification:last_check:';
    private const REDIS_TTL = 7200; // 2 hours in seconds
    private const DEFAULT_LOOKBACK_HOURS = 24;

    public function __construct(
        RedisService $redis,
        DonHang $donHangModel,
        ThanhToan $thanhToanModel,
        Refund $refundModel,
        PhienBanSanPham $phienBanModel,
        DanhGia $danhGiaModel,
        TransactionLog $transactionLogModel,
        GatewayHealth $gatewayHealthModel,
        MaGiamGia $maGiamGiaModel
    ) {
        $this->redis = $redis;
        $this->donHangModel = $donHangModel;
        $this->thanhToanModel = $thanhToanModel;
        $this->refundModel = $refundModel;
        $this->phienBanModel = $phienBanModel;
        $this->danhGiaModel = $danhGiaModel;
        $this->transactionLogModel = $transactionLogModel;
        $this->gatewayHealthModel = $gatewayHealthModel;
        $this->maGiamGiaModel = $maGiamGiaModel;
    }

    private function getLastCheckTimestamp(int $adminId): string
    {
        $key = self::REDIS_KEY_PREFIX . $adminId;
        $timestamp = $this->redis->get($key);

        if ($timestamp === false || empty($timestamp)) {
            // Initialize to 24 hours ago
            $timestamp = date('Y-m-d H:i:s', strtotime('-' . self::DEFAULT_LOOKBACK_HOURS . ' hours'));
            $this->updateLastCheckTimestamp($adminId, $timestamp);
        }

        return $timestamp;
    }

    private function updateLastCheckTimestamp(int $adminId, string $timestamp): bool
    {
        $key = self::REDIS_KEY_PREFIX . $adminId;
        return $this->redis->set($key, $timestamp, self::REDIS_TTL);
    }

    public function clearNotificationState(int $adminId): bool
    {
        $key = self::REDIS_KEY_PREFIX . $adminId;
        return $this->redis->delete($key);
    }

    /**
     * Generate a deterministic notification ID based on type and entity ID
     * 
     * @param string $type Notification type (e.g., 'new_order_pending', 'low_stock_warning')
     * @param int|null $entityId Optional entity ID for single-entity notifications
     * @return string Notification ID in format "{type}:{entity_id}" or "{type}:aggregate"
     */
    private function generateNotificationId(string $type, ?int $entityId = null): string
    {
        if ($entityId !== null) {
            return "{$type}:{$entityId}";
        }
        return "{$type}:aggregate";
    }

    /**
     * Mark notifications as read
     * 
     * @param int $adminId Admin user ID
     * @param array $notificationIds Array of notification IDs to mark as read
     * @return int Count of marked notifications
     */
    public function markAsRead(int $adminId, array $notificationIds): int
    {
        if (empty($notificationIds)) {
            return 0;
        }

        $key = "notification:read:{$adminId}";
        
        try {
            // Add notification IDs to read Set
            $count = $this->redis->sAdd($key, ...$notificationIds);
            
            // Set/extend TTL to 7 days
            $this->redis->expire($key, 604800);
            
            return $count;
        } catch (Exception $e) {
            error_log("[NotificationService] markAsRead failed for admin {$adminId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mark notifications as unread
     * 
     * @param int $adminId Admin user ID
     * @param array $notificationIds Array of notification IDs to mark as unread
     * @return int Count of unmarked notifications
     */
    public function markAsUnread(int $adminId, array $notificationIds): int
    {
        if (empty($notificationIds)) {
            return 0;
        }

        $key = "notification:read:{$adminId}";
        
        try {
            // Remove notification IDs from read Set
            $count = $this->redis->sRem($key, ...$notificationIds);
            
            // Extend TTL to 7 days
            $this->redis->expire($key, 604800);
            
            return $count;
        } catch (Exception $e) {
            error_log("[NotificationService] markAsUnread failed for admin {$adminId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mark all notifications as read
     * 
     * @param int $adminId Admin user ID
     * @return int Count of marked notifications
     */
    public function markAllAsRead(int $adminId): int
    {
        try {
            // Get all current notifications
            $notifications = $this->aggregateNotifications($adminId);
            
            if (empty($notifications['items'])) {
                return 0;
            }

            // Extract notification IDs from all notifications
            $notificationIds = [];
            foreach ($notifications['items'] as $notification) {
                $type = $notification['type'];
                
                // For single-entity notifications, we need to query the actual IDs
                // For aggregate notifications, use the aggregate ID
                if ($notification['count'] === 1) {
                    // This is a simplification - in a real implementation,
                    // we would need to query the actual entity ID
                    $notificationIds[] = $this->generateNotificationId($type);
                } else {
                    $notificationIds[] = $this->generateNotificationId($type);
                }
            }

            if (empty($notificationIds)) {
                return 0;
            }

            $key = "notification:read:{$adminId}";
            
            // Add all notification IDs to read Set
            $count = $this->redis->sAdd($key, ...$notificationIds);
            
            // Set/extend TTL to 7 days
            $this->redis->expire($key, 604800);
            
            return $count;
        } catch (Exception $e) {
            error_log("[NotificationService] markAllAsRead failed for admin {$adminId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if a notification is read
     * 
     * @param int $adminId Admin user ID
     * @param string $notificationId Notification ID to check
     * @return bool True if notification is read, false otherwise
     */
    public function isNotificationRead(int $adminId, string $notificationId): bool
    {
        $key = "notification:read:{$adminId}";
        
        try {
            return $this->redis->sIsMember($key, $notificationId);
        } catch (Exception $e) {
            error_log("[NotificationService] isNotificationRead failed for admin {$adminId}, notification {$notificationId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get paginated notification list with filtering and sorting
     * 
     * @param int $adminId Admin user ID
     * @param int $page Page number (1-indexed)
     * @param int $perPage Items per page
     * @param string|null $category Filter by category (orders, inventory, customer, system, all)
     * @param string|null $priority Filter by priority (high, medium, low, all)
     * @param string|null $status Filter by status (read, unread, all)
     * @param string $sortBy Sort by field (time, priority)
     * @param string $sortOrder Sort order (asc, desc)
     * @return array Notification list with pagination metadata
     */
    public function getNotificationList(
        int $adminId,
        int $page = 1,
        int $perPage = 20,
        ?string $category = null,
        ?string $priority = null,
        ?string $status = null,
        string $sortBy = 'time',
        string $sortOrder = 'desc'
    ): array {
        try {
            // Validate and set defaults
            $page = max(1, $page);
            $perPage = max(1, min(100, $perPage));
            $category = $category ?? 'all';
            $priority = $priority ?? 'all';
            $status = $status ?? 'all';
            
            // Get all notifications (without time filtering for list page)
            $allNotifications = [];
            $allNotifications = array_merge($allNotifications, $this->getOrderNotificationsForList());
            $allNotifications = array_merge($allNotifications, $this->getInventoryNotificationsForList());
            $allNotifications = array_merge($allNotifications, $this->getCustomerNotificationsForList());
            $allNotifications = array_merge($allNotifications, $this->getSystemNotificationsForList());
            
            // Retrieve read notification IDs once for efficiency
            $readNotificationIds = $this->getReadNotificationIds($adminId);
            $readSet = array_flip($readNotificationIds);
            
            // Add notification ID and read status to each notification
            foreach ($allNotifications as &$notification) {
                $type = $notification['type'];
                $notificationId = $this->generateNotificationId($type);
                $notification['id'] = $notificationId;
                $notification['is_read'] = isset($readSet[$notificationId]);
            }
            unset($notification);
            
            // Apply filters
            $filteredNotifications = array_filter($allNotifications, function($notification) use ($category, $priority, $status) {
                // Category filter
                if ($category !== 'all' && $notification['group'] !== $category) {
                    return false;
                }
                
                // Priority filter
                if ($priority !== 'all' && $notification['priority'] !== $priority) {
                    return false;
                }
                
                // Status filter
                if ($status === 'read' && !$notification['is_read']) {
                    return false;
                }
                if ($status === 'unread' && $notification['is_read']) {
                    return false;
                }
                
                return true;
            });
            
            // Apply sorting
            usort($filteredNotifications, function($a, $b) use ($sortBy, $sortOrder) {
                if ($sortBy === 'priority') {
                    $priorityOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
                    $aVal = $priorityOrder[$a['priority']] ?? 3;
                    $bVal = $priorityOrder[$b['priority']] ?? 3;
                } else {
                    // Sort by time (timestamp)
                    $aVal = $a['timestamp'] ?? '';
                    $bVal = $b['timestamp'] ?? '';
                }
                
                $comparison = $aVal <=> $bVal;
                return $sortOrder === 'desc' ? -$comparison : $comparison;
            });
            
            // Calculate pagination
            $total = count($filteredNotifications);
            $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;
            
            // Apply pagination
            $paginatedNotifications = array_slice($filteredNotifications, $offset, $perPage);
            
            return [
                'success' => true,
                'notifications' => array_values($paginatedNotifications),
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ],
                'filters' => [
                    'category' => $category,
                    'priority' => $priority,
                    'status' => $status,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder
                ]
            ];
        } catch (Exception $e) {
            error_log("[NotificationService] getNotificationList failed for admin {$adminId}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get order notifications for list page (no time filtering)
     */
    private function getOrderNotificationsForList(): array
    {
        $notifications = [];

        $sql = "SELECT id, ngay_tao FROM don_hang WHERE trang_thai = 'CHO_DUYET' ORDER BY ngay_tao DESC";
        $result = $this->donHangModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $order) {
                $notifications[] = [
                    'group' => 'orders',
                    'type' => 'new_order_pending',
                    'count' => 1,
                    'message' => 'Đơn hàng #' . $order['id'] . ' chờ duyệt',
                    'url_redirect' => '/admin/don-hang/chi-tiet?id=' . $order['id'],
                    'priority' => 'high',
                    'icon' => 'bi-cart-check',
                    'timestamp' => $order['ngay_tao']
                ];
            }
        }

        $sql = "SELECT id, ngay_thanh_toan FROM thanh_toan 
                WHERE trang_thai_duyet = 'CHO_DUYET' 
                AND anh_bien_lai IS NOT NULL 
                ORDER BY ngay_thanh_toan DESC";
        $result = $this->thanhToanModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $payment) {
                $notifications[] = [
                    'group' => 'orders',
                    'type' => 'payment_pending_approval',
                    'count' => 1,
                    'message' => 'Thanh toán #' . $payment['id'] . ' chờ duyệt',
                    'url_redirect' => '/admin/thanh-toan/chi-tiet?id=' . $payment['id'],
                    'priority' => 'high',
                    'icon' => 'bi-credit-card',
                    'timestamp' => $payment['ngay_thanh_toan']
                ];
            }
        }

        $sql = "SELECT id, created_at FROM refund WHERE status = 'PENDING' ORDER BY created_at DESC";
        $result = $this->refundModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $refund) {
                $notifications[] = [
                    'group' => 'orders',
                    'type' => 'refund_request',
                    'count' => 1,
                    'message' => 'Yêu cầu hoàn tiền #' . $refund['id'],
                    'url_redirect' => '/admin/thanh-toan?filter=refund',
                    'priority' => 'high',
                    'icon' => 'bi-arrow-counterclockwise',
                    'timestamp' => $refund['created_at']
                ];
            }
        }

        return $notifications;
    }
    
    /**
     * Get inventory notifications for list page (no time filtering)
     */
    private function getInventoryNotificationsForList(): array
    {
        $notifications = [];

        $sql = "SELECT id, ten_phien_ban, so_luong_ton FROM phien_ban_san_pham 
                WHERE so_luong_ton < 5 AND so_luong_ton > 0 
                ORDER BY so_luong_ton ASC";
        $result = $this->phienBanModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $variant) {
                $notifications[] = [
                    'group' => 'inventory',
                    'type' => 'low_stock_warning',
                    'count' => 1,
                    'message' => $variant['ten_phien_ban'] . ' sắp hết hàng (' . $variant['so_luong_ton'] . ' còn lại)',
                    'url_redirect' => '/admin/san-pham?filter=low_stock',
                    'priority' => 'medium',
                    'icon' => 'bi-box-seam',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }

        $sql = "SELECT id, ten_phien_ban FROM phien_ban_san_pham 
                WHERE trang_thai = 'HET_HANG' 
                ORDER BY id DESC";
        $result = $this->phienBanModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $variant) {
                $notifications[] = [
                    'group' => 'inventory',
                    'type' => 'out_of_stock',
                    'count' => 1,
                    'message' => $variant['ten_phien_ban'] . ' hết hàng',
                    'url_redirect' => '/admin/san-pham?filter=out_of_stock',
                    'priority' => 'medium',
                    'icon' => 'bi-exclamation-triangle',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }

        return $notifications;
    }
    
    /**
     * Get customer notifications for list page (no time filtering)
     */
    private function getCustomerNotificationsForList(): array
    {
        $notifications = [];

        $sql = "SELECT id, ngay_viet FROM danh_gia ORDER BY ngay_viet DESC LIMIT 50";
        $result = $this->danhGiaModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $review) {
                $notifications[] = [
                    'group' => 'customer',
                    'type' => 'new_review',
                    'count' => 1,
                    'message' => 'Đánh giá mới #' . $review['id'],
                    'url_redirect' => '/admin/danh-gia/chi-tiet?id=' . $review['id'],
                    'priority' => 'low',
                    'icon' => 'bi-star',
                    'timestamp' => $review['ngay_viet']
                ];
            }
        }

        $sql = "SELECT id, so_sao, ngay_viet FROM danh_gia 
                WHERE so_sao BETWEEN 1 AND 3 
                ORDER BY ngay_viet DESC LIMIT 50";
        $result = $this->danhGiaModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $review) {
                $notifications[] = [
                    'group' => 'customer',
                    'type' => 'low_rating_review',
                    'count' => 1,
                    'message' => 'Đánh giá thấp (' . $review['so_sao'] . ' sao) #' . $review['id'],
                    'url_redirect' => '/admin/danh-gia/chi-tiet?id=' . $review['id'],
                    'priority' => 'high',
                    'icon' => 'bi-star-half',
                    'timestamp' => $review['ngay_viet']
                ];
            }
        }

        return $notifications;
    }
    
    /**
     * Get system notifications for list page (no time filtering)
     */
    private function getSystemNotificationsForList(): array
    {
        $notifications = [];

        $sql = "SELECT id, created_at FROM transaction_log 
                WHERE status = 'FAILED' 
                ORDER BY created_at DESC LIMIT 50";
        $result = $this->transactionLogModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $log) {
                $notifications[] = [
                    'group' => 'system',
                    'type' => 'payment_gateway_error',
                    'count' => 1,
                    'message' => 'Giao dịch thất bại #' . $log['id'],
                    'url_redirect' => '/admin/thanh-toan/health',
                    'priority' => 'high',
                    'icon' => 'bi-exclamation-octagon',
                    'timestamp' => $log['created_at']
                ];
            }
        }

        $sql = "SELECT id, ma_code FROM ma_giam_gia 
                WHERE so_luot_da_dung >= gioi_han_su_dung 
                AND gioi_han_su_dung IS NOT NULL 
                AND trang_thai = 'HOAT_DONG' 
                ORDER BY id DESC";
        $result = $this->maGiamGiaModel->query($sql);
        $count = count($result);
        
        if ($count > 0) {
            foreach ($result as $voucher) {
                $notifications[] = [
                    'group' => 'system',
                    'type' => 'voucher_exhausted',
                    'count' => 1,
                    'message' => 'Mã giảm giá "' . $voucher['ma_code'] . '" đã hết lượt',
                    'url_redirect' => '/admin/ma-giam-gia?filter=exhausted',
                    'priority' => 'medium',
                    'icon' => 'bi-ticket-perforated',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }

        return $notifications;
    }

    /**
     * Get all read notification IDs for an admin
     * 
     * @param int $adminId Admin user ID
     * @return array Array of read notification IDs
     */
    private function getReadNotificationIds(int $adminId): array
    {
        $key = "notification:read:{$adminId}";
        
        try {
            return $this->redis->sMembers($key);
        } catch (Exception $e) {
            error_log("[NotificationService] getReadNotificationIds failed for admin {$adminId}: " . $e->getMessage());
            return [];
        }
    }

    public function aggregateNotifications(int $adminId): array
    {
        $lastCheck = $this->getLastCheckTimestamp($adminId);
        $currentTime = date('Y-m-d H:i:s');

        $notifications = [];
        $notifications = array_merge($notifications, $this->getOrderNotifications($lastCheck));
        $notifications = array_merge($notifications, $this->getInventoryNotifications($lastCheck));
        $notifications = array_merge($notifications, $this->getCustomerNotifications($lastCheck));
        $notifications = array_merge($notifications, $this->getSystemNotifications($lastCheck));

        $this->updateLastCheckTimestamp($adminId, $currentTime);

        // Retrieve read notification IDs once for efficiency
        $readNotificationIds = $this->getReadNotificationIds($adminId);
        $readSet = array_flip($readNotificationIds); // Convert to associative array for O(1) lookup

        // Add notification ID and read status to each notification
        foreach ($notifications as &$notification) {
            $type = $notification['type'];
            
            // Generate notification ID based on count
            // For single-entity notifications (count = 1), we would need the actual entity ID
            // For now, we use aggregate format for all notifications
            // This will be refined when we have access to individual entity IDs
            if ($notification['count'] === 1) {
                // For single entity notifications, use aggregate for now
                // In a full implementation, we would query the actual entity ID
                $notificationId = $this->generateNotificationId($type);
            } else {
                $notificationId = $this->generateNotificationId($type);
            }
            
            $notification['id'] = $notificationId;
            $notification['is_read'] = isset($readSet[$notificationId]);
        }
        unset($notification); // Break reference

        $totalNotifications = array_reduce($notifications, function ($carry, $item) {
            return $carry + ($item['count'] ?? 0);
        }, 0);

        return [
            'success' => true,
            'total_notifications' => $totalNotifications,
            'items' => $notifications,
            'last_check' => $currentTime
        ];
    }

    private function getOrderNotifications(string $since): array
    {
        $notifications = [];

        $sql = "SELECT COUNT(*) as count FROM don_hang 
                WHERE trang_thai = 'CHO_DUYET' AND ngay_tao >= '$since'";
        $result = $this->donHangModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'orders',
                'type' => 'new_order_pending',
                'count' => $count,
                'message' => $count . ' đơn hàng mới chờ duyệt',
                'url_redirect' => '/admin/don-hang?trang_thai=CHO_DUYET',
                'priority' => 'high',
                'icon' => 'bi-cart-check'
            ];
        }

        $sql = "SELECT COUNT(*) as count FROM thanh_toan 
                WHERE trang_thai_duyet = 'CHO_DUYET' 
                AND anh_bien_lai IS NOT NULL 
                AND ngay_thanh_toan >= '$since'";
        $result = $this->thanhToanModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'orders',
                'type' => 'payment_pending_approval',
                'count' => $count,
                'message' => $count . ' thanh toán chờ duyệt',
                'url_redirect' => '/admin/thanh-toan?trang_thai=CHO_DUYET',
                'priority' => 'high',
                'icon' => 'bi-credit-card'
            ];
        }

        $sql = "SELECT COUNT(*) as count FROM refund 
                WHERE status = 'PENDING' AND created_at >= '$since'";
        $result = $this->refundModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'orders',
                'type' => 'refund_request',
                'count' => $count,
                'message' => $count . ' yêu cầu hoàn tiền',
                'url_redirect' => '/admin/thanh-toan?filter=refund',
                'priority' => 'high',
                'icon' => 'bi-arrow-counterclockwise'
            ];
        }

        $sql = "SELECT COUNT(*) as count FROM don_hang 
                WHERE trang_thai IN ('DA_HUY', 'TRA_HANG') AND ngay_cap_nhat >= '$since'";
        $result = $this->donHangModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'orders',
                'type' => 'order_cancelled_or_returned',
                'count' => $count,
                'message' => $count . ' đơn hàng bị hủy/trả',
                'url_redirect' => '/admin/don-hang?trang_thai=DA_HUY,TRA_HANG',
                'priority' => 'high',
                'icon' => 'bi-x-circle'
            ];
        }

        return $notifications;
    }

    private function getInventoryNotifications(string $since): array
    {
        $notifications = [];

        $sql = "SELECT COUNT(*) as count FROM phien_ban_san_pham 
                WHERE so_luong_ton < 5 AND so_luong_ton > 0";
        $result = $this->phienBanModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'inventory',
                'type' => 'low_stock_warning',
                'count' => $count,
                'message' => $count . ' sản phẩm sắp hết hàng',
                'url_redirect' => '/admin/san-pham?filter=low_stock',
                'priority' => 'medium',
                'icon' => 'bi-box-seam'
            ];
        }

        $sql = "SELECT COUNT(*) as count FROM phien_ban_san_pham 
                WHERE trang_thai = 'HET_HANG'";
        $result = $this->phienBanModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'inventory',
                'type' => 'out_of_stock',
                'count' => $count,
                'message' => $count . ' sản phẩm hết hàng',
                'url_redirect' => '/admin/san-pham?filter=out_of_stock',
                'priority' => 'medium',
                'icon' => 'bi-exclamation-triangle'
            ];
        }

        return $notifications;
    }

    private function getCustomerNotifications(string $since): array
    {
        $notifications = [];

        $sql = "SELECT COUNT(*) as count FROM danh_gia WHERE ngay_viet >= '$since'";
        $result = $this->danhGiaModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'customer',
                'type' => 'new_review',
                'count' => $count,
                'message' => $count . ' đánh giá mới',
                'url_redirect' => '/admin/danh-gia',
                'priority' => 'low',
                'icon' => 'bi-star'
            ];
        }

        $sql = "SELECT COUNT(*) as count FROM danh_gia 
                WHERE so_sao BETWEEN 1 AND 3 AND ngay_viet >= '$since'";
        $result = $this->danhGiaModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'customer',
                'type' => 'low_rating_review',
                'count' => $count,
                'message' => $count . ' đánh giá thấp (1-3 sao)',
                'url_redirect' => '/admin/danh-gia?rating=low',
                'priority' => 'high',
                'icon' => 'bi-star-half'
            ];
        }

        return $notifications;
    }

    private function getSystemNotifications(string $since): array
    {
        $notifications = [];

        $sql = "SELECT COUNT(*) as count FROM transaction_log 
                WHERE status = 'FAILED' AND created_at >= '$since'";
        $result = $this->transactionLogModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'system',
                'type' => 'payment_gateway_error',
                'count' => $count,
                'message' => $count . ' giao dịch thanh toán thất bại',
                'url_redirect' => '/admin/thanh-toan/health',
                'priority' => 'high',
                'icon' => 'bi-exclamation-octagon'
            ];
        }

        $sql = "SELECT COUNT(*) as count FROM ma_giam_gia 
                WHERE so_luot_da_dung >= gioi_han_su_dung 
                AND gioi_han_su_dung IS NOT NULL 
                AND trang_thai = 'HOAT_DONG'";
        $result = $this->maGiamGiaModel->query($sql);
        $count = (int)($result[0]['count'] ?? 0);
        
        if ($count > 0) {
            $notifications[] = [
                'group' => 'system',
                'type' => 'voucher_exhausted',
                'count' => $count,
                'message' => $count . ' mã giảm giá đã hết lượt',
                'url_redirect' => '/admin/ma-giam-gia?filter=exhausted',
                'priority' => 'medium',
                'icon' => 'bi-ticket-perforated'
            ];
        }

        return $notifications;
    }
}
