<?php

require_once dirname(__DIR__, 2) . '/services/notification/NotificationService.php';
require_once dirname(__DIR__, 2) . '/services/redis/RedisService.php';
require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
require_once dirname(__DIR__, 2) . '/models/entities/Refund.php';
require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';
require_once dirname(__DIR__, 2) . '/models/entities/DanhGia.php';
require_once dirname(__DIR__, 2) . '/models/entities/TransactionLog.php';
require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
require_once dirname(__DIR__, 2) . '/models/entities/MaGiamGia.php';
require_once dirname(__DIR__, 2) . '/core/Session.php';
require_once dirname(__DIR__, 2) . '/middleware/AdminMiddleware.php';

use App\Core\Session;

class NotificationController
{
    private NotificationService $notificationService;

    public function __construct()
    {
        $redis = RedisService::getInstance();
        $donHangModel = new DonHang();
        $thanhToanModel = new ThanhToan();
        $refundModel = new Refund();
        $phienBanModel = new PhienBanSanPham();
        $danhGiaModel = new DanhGia();
        $transactionLogModel = new TransactionLog();
        $gatewayHealthModel = new GatewayHealth();
        $maGiamGiaModel = new MaGiamGia();

        $this->notificationService = new NotificationService(
            $redis,
            $donHangModel,
            $thanhToanModel,
            $refundModel,
            $phienBanModel,
            $danhGiaModel,
            $transactionLogModel,
            $gatewayHealthModel,
            $maGiamGiaModel
        );
    }

    public function index(): void
    {
        try {

            Session::start();
            
            if (!Session::isLoggedIn() || !Session::isAdmin()) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Authentication required',
                    'code' => 401
                ], 401);
                return;
            }


            $adminId = Session::getUserId();
            
            if ($adminId === null) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Invalid session',
                    'code' => 401
                ], 401);
                return;
            }


            $result = $this->notificationService->aggregateNotifications($adminId);


            $this->sendJsonResponse($result, 200);

        } catch (\Exception $e) {

            error_log('[NotificationController] Error: ' . $e->getMessage());
            error_log('[NotificationController] Stack trace: ' . $e->getTraceAsString());


            $errorMessage = 'Service temporarily unavailable';
            $statusCode = 503;


            if (strpos($e->getMessage(), 'Redis') !== false) {
                $errorMessage = 'Cache service unavailable';
            } elseif (strpos($e->getMessage(), 'database') !== false || 
                      strpos($e->getMessage(), 'MySQL') !== false) {
                $errorMessage = 'Database service unavailable';
            }

            $this->sendJsonResponse([
                'success' => false,
                'error' => $errorMessage,
                'code' => $statusCode,
                'retry_after' => 60
            ], $statusCode);
        }
    }

    public function markAsRead(): void
    {
        try {

            Session::start();
            
            if (!Session::isLoggedIn() || !Session::isAdmin()) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Authentication required',
                    'code' => 401
                ], 401);
                return;
            }

            $adminId = Session::getUserId();
            
            if ($adminId === null) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Invalid session',
                    'code' => 401
                ], 401);
                return;
            }


            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Invalid JSON payload',
                    'code' => 400
                ], 400);
                return;
            }


            if (!isset($data['notification_id'])) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Missing notification_id field',
                    'code' => 400
                ], 400);
                return;
            }

            $notificationIds = is_array($data['notification_id']) 
                ? $data['notification_id'] 
                : [$data['notification_id']];

            foreach ($notificationIds as $id) {
                if (!$this->validateNotificationId($id)) {
                    $this->sendJsonResponse([
                        'success' => false,
                        'error' => 'Invalid notification ID format: ' . $id,
                        'code' => 400
                    ], 400);
                    return;
                }
            }


            $markedCount = $this->notificationService->markAsRead($adminId, $notificationIds);

            $this->sendJsonResponse([
                'success' => true,
                'marked_count' => $markedCount
            ], 200);

        } catch (\Exception $e) {
            error_log('[NotificationController] markAsRead error: ' . $e->getMessage());
            error_log('[NotificationController] Stack trace: ' . $e->getTraceAsString());

            $this->sendJsonResponse([
                'success' => false,
                'error' => 'Internal server error',
                'code' => 500
            ], 500);
        }
    }

    public function markAsUnread(): void
    {
        try {

            Session::start();
            
            if (!Session::isLoggedIn() || !Session::isAdmin()) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Authentication required',
                    'code' => 401
                ], 401);
                return;
            }

            $adminId = Session::getUserId();
            
            if ($adminId === null) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Invalid session',
                    'code' => 401
                ], 401);
                return;
            }


            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Invalid JSON payload',
                    'code' => 400
                ], 400);
                return;
            }


            if (!isset($data['notification_id'])) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Missing notification_id field',
                    'code' => 400
                ], 400);
                return;
            }


            $notificationIds = is_array($data['notification_id']) 
                ? $data['notification_id'] 
                : [$data['notification_id']];


            foreach ($notificationIds as $id) {
                if (!$this->validateNotificationId($id)) {
                    $this->sendJsonResponse([
                        'success' => false,
                        'error' => 'Invalid notification ID format: ' . $id,
                        'code' => 400
                    ], 400);
                    return;
                }
            }


            $unmarkedCount = $this->notificationService->markAsUnread($adminId, $notificationIds);

            $this->sendJsonResponse([
                'success' => true,
                'unmarked_count' => $unmarkedCount
            ], 200);

        } catch (\Exception $e) {
            error_log('[NotificationController] markAsUnread error: ' . $e->getMessage());
            error_log('[NotificationController] Stack trace: ' . $e->getTraceAsString());

            $this->sendJsonResponse([
                'success' => false,
                'error' => 'Internal server error',
                'code' => 500
            ], 500);
        }
    }

    public function markAllAsRead(): void
    {
        try {

            Session::start();
            
            if (!Session::isLoggedIn() || !Session::isAdmin()) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Authentication required',
                    'code' => 401
                ], 401);
                return;
            }

            $adminId = Session::getUserId();
            
            if ($adminId === null) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Invalid session',
                    'code' => 401
                ], 401);
                return;
            }


            $markedCount = $this->notificationService->markAllAsRead($adminId);

            $this->sendJsonResponse([
                'success' => true,
                'marked_count' => $markedCount
            ], 200);

        } catch (\Exception $e) {
            error_log('[NotificationController] markAllAsRead error: ' . $e->getMessage());
            error_log('[NotificationController] Stack trace: ' . $e->getTraceAsString());

            $this->sendJsonResponse([
                'success' => false,
                'error' => 'Internal server error',
                'code' => 500
            ], 500);
        }
    }

    public function getNotificationList(): void
    {
        try {

            Session::start();
            
            if (!Session::isLoggedIn() || !Session::isAdmin()) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Authentication required',
                    'code' => 401
                ], 401);
                return;
            }

            $adminId = Session::getUserId();
            
            if ($adminId === null) {
                $this->sendJsonResponse([
                    'success' => false,
                    'error' => 'Invalid session',
                    'code' => 401
                ], 401);
                return;
            }


            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
            $category = $_GET['category'] ?? 'all';
            $priority = $_GET['priority'] ?? 'all';
            $status = $_GET['status'] ?? 'all';
            $sortBy = $_GET['sort_by'] ?? 'time';
            $sortOrder = $_GET['sort_order'] ?? 'desc';


            $allowedCategories = ['all', 'orders', 'inventory', 'customer', 'system'];
            $allowedPriorities = ['all', 'high', 'medium', 'low'];
            $allowedStatuses = ['all', 'read', 'unread'];
            $allowedSortBy = ['time', 'priority'];
            $allowedSortOrder = ['asc', 'desc'];

            if (!in_array($category, $allowedCategories)) {
                $category = 'all';
            }
            if (!in_array($priority, $allowedPriorities)) {
                $priority = 'all';
            }
            if (!in_array($status, $allowedStatuses)) {
                $status = 'all';
            }
            if (!in_array($sortBy, $allowedSortBy)) {
                $sortBy = 'time';
            }
            if (!in_array($sortOrder, $allowedSortOrder)) {
                $sortOrder = 'desc';
            }


            $result = $this->notificationService->getNotificationList(
                $adminId,
                $page,
                $perPage,
                $category,
                $priority,
                $status,
                $sortBy,
                $sortOrder
            );

            $this->sendJsonResponse($result, 200);

        } catch (\Exception $e) {
            error_log('[NotificationController] getNotificationList error: ' . $e->getMessage());
            error_log('[NotificationController] Stack trace: ' . $e->getTraceAsString());


            $errorMessage = 'Internal server error';
            $statusCode = 500;

            if (strpos($e->getMessage(), 'database') !== false || 
                strpos($e->getMessage(), 'MySQL') !== false ||
                strpos($e->getMessage(), 'PDO') !== false) {
                $errorMessage = 'Database service unavailable';
                $statusCode = 503;
            }

            $this->sendJsonResponse([
                'success' => false,
                'error' => $errorMessage,
                'code' => $statusCode,
                'retry_after' => 60
            ], $statusCode);
        }
    }

    public function notificationListPage(): void
    {
        try {

            Session::start();
            
            if (!Session::isLoggedIn() || !Session::isAdmin()) {
                header('Location: /admin/auth/login');
                exit;
            }


            require_once dirname(__DIR__, 2) . '/views/admin/notifications/index.php';

        } catch (\Exception $e) {
            error_log('[NotificationController] notificationListPage error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Internal server error';
        }
    }

    private function validateNotificationId(string $id): bool
    {

        return preg_match('/^[a-z_]+:(\\d+|aggregate)$/', $id) === 1;
    }

    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {

        http_response_code($statusCode);


        header('Content-Type: application/json; charset=utf-8');


        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');


        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
