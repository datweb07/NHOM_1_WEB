<?php

namespace App\Services\Events;

require_once __DIR__ . '/../mailer/MailerService.php';
require_once __DIR__ . '/ObserverInterface.php';

/**
 * Email Observer
 * Handles all email notifications based on order events
 */
class EmailObserver implements ObserverInterface
{
    private $mailService; // Remove type hint for PHP 7.x compatibility

    public function __construct($mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Handle event notifications
     * 
     * @param string $eventType Event type (ORDER_PLACED, PAYMENT_SUCCESS, PAYMENT_RECEIVED)
     * @param array $data Event data containing order_id and other relevant info
     */
    public function update(string $eventType, array $data): void
    {
        try {
            switch ($eventType) {
                case 'ORDER_PLACED':
                    // Gửi mail: "Xác nhận đơn hàng mới"
                    // Áp dụng cho: Tất cả phương thức thanh toán
                    $this->sendOrderConfirmation($data);
                    break;

                case 'PAYMENT_SUCCESS':
                    // Gửi mail: "Thanh toán thành công qua cổng thanh toán"
                    // Áp dụng cho: VNPay, PayPal (khi nhận callback thành công)
                    $this->sendPaymentSuccessNotification($data);
                    break;

                case 'PAYMENT_RECEIVED':
                    // Gửi mail: "Đã nhận tiền & Bắt đầu xử lý đơn hàng"
                    // Áp dụng cho: COD (khi admin xác nhận), VietQR, Chuyển khoản
                    $this->sendPaymentReceivedNotification($data);
                    break;

                default:
                    error_log("EmailObserver: Unknown event type '$eventType'");
            }
        } catch (\Exception $e) {
            // Log error but don't throw - email failure shouldn't break the order flow
            error_log("EmailObserver Error [{$eventType}]: " . $e->getMessage());
        }
    }

    /**
     * Send order confirmation email
     * Template: "Chúng tôi đã nhận được đơn hàng #ABC của bạn"
     */
    private function sendOrderConfirmation(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        $emailNhan = $data['email'] ?? null; // Lấy email trực tiếp từ event data
        
        error_log("EmailObserver: sendOrderConfirmation called with order_id: " . ($orderId ?? 'NULL') . ", email: " . ($emailNhan ?? 'NULL'));
        
        if (!$orderId) {
            error_log("EmailObserver: Missing order_id for ORDER_PLACED event");
            return;
        }
        
        if (!$emailNhan) {
            error_log("EmailObserver: Missing email for ORDER_PLACED event");
            return;
        }

        // Load order details
        $orderDetails = $this->getOrderDetails($orderId);
        
        if (!$orderDetails) {
            error_log("EmailObserver: Order #$orderId not found in database");
            return;
        }

        // Lấy phương thức thanh toán từ ThanhToanController (truyền qua event data)
        $phuongThucThanhToan = $data['payment_method'] ?? $orderDetails['phuong_thuc_thanh_toan'];
        
        // Convert payment method to readable format
        $paymentMethodDisplay = $this->formatPaymentMethod($phuongThucThanhToan);

        // Prepare email data
        $emailData = [
            'to' => $emailNhan, // Sử dụng email từ event data
            'subject' => "Xác nhận đơn hàng #{$orderId}",
            'template' => 'order_confirmation',
            'data' => [
                'order_id' => $orderId,
                'customer_name' => $orderDetails['ten_nguoi_nhan'],
                'order_date' => $orderDetails['ngay_tao'],
                'total_amount' => $orderDetails['tong_tien'],
                'payment_method' => $paymentMethodDisplay,
                'items' => $orderDetails['items'],
                // Thêm thông tin chi tiết về giá
                'subtotal' => $data['subtotal'] ?? $orderDetails['tong_tien'],
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'discount_code' => $data['discount_code'] ?? null
            ]
        ];

        error_log("EmailObserver: Attempting to send email to: " . $emailNhan);
        
        $result = $this->mailService->sendOrderConfirmation($emailData);
        
        if ($result) {
            error_log("EmailObserver: Successfully sent ORDER_PLACED email for order #$orderId");
        } else {
            error_log("EmailObserver: Failed to send ORDER_PLACED email for order #$orderId");
        }
    }

    /**
     * Send payment success notification
     * Template: "Giao dịch qua [VNPay/PayPal] thành công"
     */
    private function sendPaymentSuccessNotification(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        $paymentMethod = $data['payment_method'] ?? 'Online';
        $emailNhan = $data['email'] ?? null; // Lấy email từ event data
        
        error_log("EmailObserver: sendPaymentSuccessNotification called with order_id: " . ($orderId ?? 'NULL') . ", email: " . ($emailNhan ?? 'NULL'));
        
        if (!$orderId) {
            error_log("EmailObserver: Missing order_id for PAYMENT_SUCCESS event");
            return;
        }
        
        if (!$emailNhan) {
            error_log("EmailObserver: Missing email for PAYMENT_SUCCESS event");
            return;
        }

        $orderDetails = $this->getOrderDetails($orderId);
        
        if (!$orderDetails) {
            error_log("EmailObserver: Order #$orderId not found");
            return;
        }

        $emailData = [
            'to' => $emailNhan, // Sử dụng email từ event data
            'subject' => "Thanh toán thành công - Đơn hàng #{$orderId}",
            'template' => 'payment_success',
            'data' => [
                'order_id' => $orderId,
                'customer_name' => $orderDetails['ten_nguoi_nhan'],
                'payment_method' => $paymentMethod,
                'transaction_id' => $data['transaction_id'] ?? 'N/A',
                'amount' => $orderDetails['tong_tien'],
                'payment_date' => date('d/m/Y H:i:s')
            ]
        ];

        error_log("EmailObserver: Attempting to send PAYMENT_SUCCESS email to: " . $emailNhan);
        
        $result = $this->mailService->sendPaymentSuccess($emailData);
        
        if ($result) {
            error_log("EmailObserver: Successfully sent PAYMENT_SUCCESS email for order #$orderId");
        } else {
            error_log("EmailObserver: Failed to send PAYMENT_SUCCESS email for order #$orderId");
        }
    }

    /**
     * Send payment received notification
     * Template: "Đã nhận được tiền, đơn hàng đang được đóng gói"
     */
    private function sendPaymentReceivedNotification(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        
        if (!$orderId) {
            error_log("EmailObserver: Missing order_id for PAYMENT_RECEIVED event");
            return;
        }

        $orderDetails = $this->getOrderDetails($orderId);
        
        if (!$orderDetails) {
            error_log("EmailObserver: Order #$orderId not found");
            return;
        }

        $emailData = [
            'to' => $orderDetails['email'],
            'subject' => "Đã nhận thanh toán - Đơn hàng #{$orderId}",
            'template' => 'payment_received',
            'data' => [
                'order_id' => $orderId,
                'customer_name' => $orderDetails['ten_nguoi_nhan'],
                'amount' => $orderDetails['tong_tien'],
                'payment_method' => $orderDetails['phuong_thuc_thanh_toan'],
                'estimated_delivery' => $this->calculateEstimatedDelivery()
            ]
        ];

        $this->mailService->sendPaymentReceived($emailData);
        
        error_log("EmailObserver: Sent PAYMENT_RECEIVED email for order #$orderId");
    }

    /**
     * Get order details from database
     */
    private function getOrderDetails(int $orderId): ?array
    {
        require_once __DIR__ . '/../../models/entities/DonHang.php';
        
        $donHangModel = new \DonHang();
        
        // Use query instead of layTheoId
        $orders = $donHangModel->query("SELECT * FROM don_hang WHERE id = $orderId LIMIT 1");
        
        if (empty($orders)) {
            return null;
        }
        
        $order = $orders[0];

        // Get order items
        require_once __DIR__ . '/../../models/entities/ChiTietDon.php';
        $chiTietModel = new \ChiTietDon();
        
        // Use query instead of layTheoMaDon - fix field names
        $items = $chiTietModel->query("
            SELECT ct.*, sp.ten_san_pham, ct.gia_tai_thoi_diem_mua AS gia
            FROM chi_tiet_don ct
            LEFT JOIN phien_ban_san_pham pb ON ct.phien_ban_id = pb.id
            LEFT JOIN san_pham sp ON pb.san_pham_id = sp.id
            WHERE ct.don_hang_id = $orderId
        ");
        
        // Get recipient name from thong_tin_guest or database
        $tenNguoiNhan = 'Quý khách';
        if (!empty($order['thong_tin_guest'])) {
            $guestInfo = json_decode($order['thong_tin_guest'], true);
            $tenNguoiNhan = $guestInfo['ten'] ?? 'Quý khách';
        }

        return [
            'ten_nguoi_nhan' => $tenNguoiNhan,
            'ngay_tao' => $order['ngay_tao'] ?? '',
            'tong_tien' => $order['tong_thanh_toan'] ?? 0, // Sử dụng tong_thanh_toan thay vì tong_tien
            'phuong_thuc_thanh_toan' => $order['phuong_thuc_thanh_toan'] ?? 'COD',
            'items' => $items
        ];
    }

    /**
     * Calculate estimated delivery date
     */
    private function calculateEstimatedDelivery(): string
    {
        // Estimate 3-5 business days
        $days = rand(3, 5);
        $deliveryDate = date('d/m/Y', strtotime("+$days days"));
        return $deliveryDate;
    }

    /**
     * Format payment method code to readable text
     */
    private function formatPaymentMethod(string $paymentMethod): string
    {
        $methods = [
            'COD' => 'Thanh toán khi nhận hàng (COD)',
            'CHUYEN_KHOAN' => 'Chuyển khoản ngân hàng (VNPay)',
            'VNPAY' => 'Chuyển khoản ngân hàng (VNPay)',
            'PAYPAL' => 'Thanh toán PayPal',
            'VIETQR' => 'Chuyển khoản VietQR',
            'BANK_TRANSFER' => 'Chuyển khoản ngân hàng'
        ];

        return $methods[$paymentMethod] ?? $paymentMethod;
    }
}
