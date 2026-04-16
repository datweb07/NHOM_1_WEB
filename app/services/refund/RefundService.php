<?php

require_once dirname(__DIR__, 2) . '/models/entities/Refund.php';
require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
require_once dirname(__DIR__, 2) . '/models/entities/TransactionLog.php';
require_once dirname(__DIR__) . '/payment/PaymentService.php';

class RefundService
{
    private Refund $refundModel;

    private ThanhToan $thanhToanModel;
    
    private TransactionLog $transactionLogModel;
    
    private PaymentService $paymentService;

    public function __construct()
    {
        $this->refundModel = new Refund();
        $this->thanhToanModel = new ThanhToan();
        $this->transactionLogModel = new TransactionLog();
        $this->paymentService = new PaymentService();
    }

    public function canRefund(array $thanhToan): array
    {

        if (!isset($thanhToan['trang_thai_duyet']) || $thanhToan['trang_thai_duyet'] !== 'THANH_CONG') {
            return [
                'can_refund' => false,
                'reason' => 'Chỉ có thể hoàn tiền cho thanh toán đã được duyệt thành công'
            ];
        }


        if (isset($thanhToan['phuong_thuc']) && $thanhToan['phuong_thuc'] === 'COD') {
            return [
                'can_refund' => false,
                'reason' => 'Không hỗ trợ hoàn tiền cho phương thức thanh toán COD'
            ];
        }





        if (isset($thanhToan['id']) && $this->refundModel->hasCompletedRefund($thanhToan['id'])) {
            return [
                'can_refund' => false,
                'reason' => 'Thanh toán này đã được hoàn tiền'
            ];
        }


        return [
            'can_refund' => true,
            'reason' => ''
        ];
    }

    public function initiateRefund(int $thanhToanId, float $amount, string $reason, int $adminId): array
    {

        $thanhToan = $this->thanhToanModel->findById($thanhToanId);
        
        if (!$thanhToan) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán',
                'refund_id' => null
            ];
        }


        $canRefundCheck = $this->canRefund($thanhToan);
        
        if (!$canRefundCheck['can_refund']) {
            return [
                'success' => false,
                'message' => $canRefundCheck['reason'],
                'refund_id' => null
            ];
        }


        $refundId = $this->refundModel->createRefund($thanhToanId, $amount, $reason, $adminId);
        
        if (!$refundId) {
            return [
                'success' => false,
                'message' => 'Không thể tạo bản ghi hoàn tiền',
                'refund_id' => null
            ];
        }


        $this->transactionLogModel->logRequest($thanhToanId, 'REFUND', [
            'action' => 'REFUND_INITIATED',
            'refund_id' => $refundId,
            'amount' => $amount,
            'reason' => $reason,
            'admin_id' => $adminId,
            'timestamp' => date('Y-m-d H:i:s')
        ]);


        $paymentMethod = $thanhToan['phuong_thuc'] ?? '';
        $gateway = $this->getGatewayInstance($paymentMethod);
        
        if (!$gateway) {

            $this->refundModel->updateRefundStatus($refundId, 'FAILED');
            
            $this->transactionLogModel->logRequest($thanhToanId, 'REFUND', [
                'action' => 'REFUND_FAILED',
                'refund_id' => $refundId,
                'amount' => $amount,
                'reason' => $reason,
                'error' => 'Không thể khởi tạo cổng thanh toán',
                'admin_id' => $adminId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'success' => false,
                'message' => 'Không thể khởi tạo cổng thanh toán',
                'refund_id' => $refundId
            ];
        }


        $gatewayTransactionId = $thanhToan['gateway_transaction_id'] ?? (string)$thanhToanId;
        $gatewayResult = $gateway->initiateRefund($gatewayTransactionId, $amount, $reason);


        if ($gatewayResult['success']) {

            $gatewayRefundId = $gatewayResult['refund_id'] ?? null;
            $this->refundModel->updateRefundStatus($refundId, 'COMPLETED', $gatewayRefundId);
            

            $this->transactionLogModel->logRequest($thanhToanId, 'REFUND', [
                'action' => 'REFUND_COMPLETED',
                'refund_id' => $refundId,
                'amount' => $amount,
                'reason' => $reason,
                'gateway_refund_id' => $gatewayRefundId,
                'admin_id' => $adminId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'success' => true,
                'message' => $gatewayResult['message'] ?? 'Hoàn tiền thành công',
                'refund_id' => $refundId
            ];
        } else {

            $this->refundModel->updateRefundStatus($refundId, 'FAILED');
            

            $this->transactionLogModel->logRequest($thanhToanId, 'REFUND', [
                'action' => 'REFUND_FAILED',
                'refund_id' => $refundId,
                'amount' => $amount,
                'reason' => $reason,
                'error' => $gatewayResult['message'] ?? 'Lỗi không xác định',
                'admin_id' => $adminId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'success' => false,
                'message' => $gatewayResult['message'] ?? 'Hoàn tiền thất bại',
                'refund_id' => $refundId
            ];
        }
    }

    private function getGatewayInstance(string $paymentMethod): ?object
    {
        $gatewayMap = [
            'CHUYEN_KHOAN' => 'VNPayGateway'
        ];
        
        $gatewayClass = $gatewayMap[$paymentMethod] ?? null;
        
        if (!$gatewayClass) {
            return null;
        }
        
        $gatewayPath = dirname(__DIR__) . "/payment/{$gatewayClass}.php";
        
        if (!file_exists($gatewayPath)) {
            return null;
        }
        
        require_once $gatewayPath;
        
        return new $gatewayClass();
    }
}
