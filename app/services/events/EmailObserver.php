<?php

namespace App\Services\Events;

require_once __DIR__ . '/../mailer/MailerService.php';
require_once __DIR__ . '/ObserverInterface.php';

class EmailObserver implements ObserverInterface
{
    private $mailService; 

    public function __construct($mailService)
    {
        $this->mailService = $mailService;
    }

    public function update(string $eventType, array $data): void
    {
        try {
            switch ($eventType) {
                case 'ORDER_PLACED':
                    $this->sendOrderConfirmation($data);
                    break;

                case 'PAYMENT_SUCCESS':
                    $this->sendPaymentSuccessNotification($data);
                    break;

                case 'PAYMENT_RECEIVED':
                    $this->sendPaymentReceivedNotification($data);
                    break;

                default:
                    error_log("EmailObserver: Unknown event type '$eventType'");
            }
        } catch (\Exception $e) {
            error_log("EmailObserver Error [{$eventType}]: " . $e->getMessage());
        }
    }

    private function sendOrderConfirmation(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        $emailNhan = $data['email'] ?? null;
        
        error_log("EmailObserver: sendOrderConfirmation called with order_id: " . ($orderId ?? 'NULL') . ", email: " . ($emailNhan ?? 'NULL'));
        
        if (!$orderId) {
            error_log("EmailObserver: Missing order_id for ORDER_PLACED event");
            return;
        }
        
        if (!$emailNhan) {
            error_log("EmailObserver: Missing email for ORDER_PLACED event");
            return;
        }

        $orderDetails = $this->getOrderDetails($orderId);
        
        if (!$orderDetails) {
            error_log("EmailObserver: Order #$orderId not found in database");
            return;
        }

        $phuongThucThanhToan = $data['payment_method'] ?? $orderDetails['phuong_thuc_thanh_toan'];
        
        $paymentMethodDisplay = $this->formatPaymentMethod($phuongThucThanhToan);

        $emailData = [
            'to' => $emailNhan, 
            'subject' => "Xác nhận đơn hàng #{$orderId}",
            'template' => 'order_confirmation',
            'data' => [
                'order_id' => $orderId,
                'customer_name' => $orderDetails['ten_nguoi_nhan'],
                'order_date' => $orderDetails['ngay_tao'],
                'total_amount' => $orderDetails['tong_tien'],
                'payment_method' => $paymentMethodDisplay,
                'items' => $orderDetails['items'],
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

    private function sendPaymentSuccessNotification(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        $paymentMethod = $data['payment_method'] ?? 'Online';
        $emailNhan = $data['email'] ?? null; 
        
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
            'to' => $emailNhan, 
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

    private function getOrderDetails(int $orderId): ?array
    {
        require_once __DIR__ . '/../../models/entities/DonHang.php';
        
        $donHangModel = new \DonHang();
        
        $orders = $donHangModel->query("SELECT * FROM don_hang WHERE id = $orderId LIMIT 1");
        
        if (empty($orders)) {
            return null;
        }
        
        $order = $orders[0];

        require_once __DIR__ . '/../../models/entities/ChiTietDon.php';
        $chiTietModel = new \ChiTietDon();
        
        $items = $chiTietModel->query("
            SELECT ct.*, sp.ten_san_pham, ct.gia_tai_thoi_diem_mua AS gia
            FROM chi_tiet_don ct
            LEFT JOIN phien_ban_san_pham pb ON ct.phien_ban_id = pb.id
            LEFT JOIN san_pham sp ON pb.san_pham_id = sp.id
            WHERE ct.don_hang_id = $orderId
        ");
        
        $tenNguoiNhan = 'Quý khách';
        if (!empty($order['thong_tin_guest'])) {
            $guestInfo = json_decode($order['thong_tin_guest'], true);
            $tenNguoiNhan = $guestInfo['ten'] ?? 'Quý khách';
        }

        return [
            'ten_nguoi_nhan' => $tenNguoiNhan,
            'ngay_tao' => $order['ngay_tao'] ?? '',
            'tong_tien' => $order['tong_thanh_toan'] ?? 0, 
            'phuong_thuc_thanh_toan' => $order['phuong_thuc_thanh_toan'] ?? 'COD',
            'items' => $items
        ];
    }

    private function calculateEstimatedDelivery(): string
    {
        $days = rand(3, 5);
        $deliveryDate = date('d/m/Y', strtotime("+$days days"));
        return $deliveryDate;
    }

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
