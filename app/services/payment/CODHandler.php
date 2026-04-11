<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';

class CODHandler implements PaymentGatewayInterface
{
    private DonHang $donHangModel;

    public function __construct()
    {
        $this->donHangModel = new DonHang();
    }

    public function generatePaymentUrl(array $transaction): ?string
    {
        return null;
    }

    public function verifyCallback(array $data): bool
    {
        return true;
    }

    public function verifyReturnUrl(array $data): bool
    {
        return true;
    }

    public function getErrorMessage(string $errorCode): string
    {
        $errorMessages = [
            'ORDER_NOT_FOUND' => 'Không tìm thấy đơn hàng.',
            'INVALID_AMOUNT' => 'Số tiền thanh toán không hợp lệ.',
            'ORDER_CANCELLED' => 'Đơn hàng đã bị hủy.',
            'DELIVERY_FAILED' => 'Giao hàng thất bại.',
            'PAYMENT_REFUSED' => 'Khách hàng từ chối thanh toán khi nhận hàng.'
        ];

        return $errorMessages[$errorCode] ?? 'Đã xảy ra lỗi với thanh toán COD.';
    }

    public function processPayment(array $transaction): array
    {
        $donHangId = $transaction['don_hang_id'] ?? null;

        if (!$donHangId) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin đơn hàng.'
            ];
        }

        $updateResult = $this->donHangModel->update($donHangId, [
            'trang_thai' => 'CHO_DUYET'
        ]);

        if ($updateResult > 0) {
            return [
                'success' => true,
                'message' => 'Đơn hàng COD đã được tạo thành công. Vui lòng chuẩn bị tiền mặt khi nhận hàng.',
                'payment_method' => 'COD',
                'requires_redirect' => false
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể cập nhật trạng thái đơn hàng.'
        ];
    }

    public function confirmPayment(int $transactionId): array
    {
        require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
        $thanhToanModel = new ThanhToan();

        $transaction = $thanhToanModel->findById($transactionId);

        if (!$transaction) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy giao dịch.'
            ];
        }

        $updateResult = $thanhToanModel->update($transactionId, [
            'trang_thai_duyet' => 'THANH_CONG',
            'ngay_duyet' => date('Y-m-d H:i:s')
        ]);

        if ($updateResult > 0) {
            $donHangId = $transaction['don_hang_id'];
            $this->donHangModel->update($donHangId, [
                'trang_thai' => 'DA_GIAO'
            ]);

            return [
                'success' => true,
                'message' => 'Đã xác nhận thanh toán COD thành công.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể xác nhận thanh toán.'
        ];
    }
}
