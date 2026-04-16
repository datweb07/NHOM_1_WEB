<?php

require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/TransactionLog.php';

class PaymentService
{
    private ThanhToan $thanhToanModel;
    private DonHang $donHangModel;
    private TransactionLog $transactionLogModel;

    public function __construct()
    {
        $this->thanhToanModel = new ThanhToan();
        $this->donHangModel = new DonHang();
        $this->transactionLogModel = new TransactionLog();
    }

    public function createTransaction(int $donHangId, string $paymentMethod, float $amount): int
    {
        $expirationTime = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        $gatewayName = $this->getGatewayName($paymentMethod);
        
        $transactionData = [
            'don_hang_id' => $donHangId,
            'phuong_thuc' => addslashes($paymentMethod),
            'so_tien' => $amount,
            'gateway_name' => addslashes($gatewayName),
            'expiration_time' => addslashes($expirationTime),
            'trang_thai_duyet' => 'CHO_DUYET',
            'ngay_thanh_toan' => date('Y-m-d H:i:s')
        ];
        
        $transactionId = $this->thanhToanModel->create($transactionData);
        
        $this->transactionLogModel->logRequest($transactionId, $gatewayName, [
            'don_hang_id' => $donHangId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'expiration_time' => $expirationTime
        ]);
        
        return $transactionId;
    }

    public function processPayment(int $transactionId, string $paymentMethod): array
    {
        if ($this->checkTransactionTimeout($transactionId)) {
            return [
                'success' => false,
                'message' => 'Giao dịch đã hết hạn. Vui lòng thử lại.'
            ];
        }
        
        $gateway = $this->getGatewayInstance($paymentMethod);
        
        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Phương thức thanh toán không hợp lệ.'
            ];
        }
        
        $transaction = $this->thanhToanModel->findById($transactionId);
        
        if (!$transaction) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy giao dịch.'
            ];
        }
        
        $paymentUrl = $gateway->generatePaymentUrl($transaction);
        
        if ($paymentUrl) {
            $this->thanhToanModel->update($transactionId, [
                'payment_url' => addslashes($paymentUrl)
            ]);
        }
        
        return [
            'success' => true,
            'payment_url' => $paymentUrl,
            'transaction_id' => $transactionId
        ];
    }

    public function checkTransactionTimeout(int $transactionId): bool
    {
        $transaction = $this->thanhToanModel->findById($transactionId);
        
        if (!$transaction || !isset($transaction['expiration_time'])) {
            return false;
        }
        
        $expirationTime = strtotime($transaction['expiration_time']);
        $currentTime = time();
        
        $isExpired = $currentTime > $expirationTime;
        
        if ($isExpired && $transaction['trang_thai_duyet'] === 'CHO_DUYET') {
            $this->thanhToanModel->update($transactionId, [
                'trang_thai_duyet' => 'THAT_BAI',
                'error_message' => addslashes('Giao dịch đã hết hạn')
            ]);
            
            $this->transactionLogModel->logResponse($transactionId, [
                'status' => 'expired',
                'message' => 'Transaction expired'
            ], 'EXPIRED');
        }
        
        return $isExpired;
    }

    public function validateAmount(int $transactionId, float $paidAmount): bool
    {
        $transaction = $this->thanhToanModel->findById($transactionId);
        
        if (!$transaction) {
            return false;
        }
        
        $expectedAmount = (float)$transaction['so_tien'];
        
        $amountMatch = abs($expectedAmount - $paidAmount) < 1;
        
        if (!$amountMatch) {
            $this->transactionLogModel->logResponse($transactionId, [
                'expected_amount' => $expectedAmount,
                'paid_amount' => $paidAmount,
                'mismatch' => true
            ], 'AMOUNT_MISMATCH');
            
            $this->thanhToanModel->update($transactionId, [
                'error_code' => 'AMOUNT_MISMATCH',
                'error_message' => addslashes("Số tiền không khớp. Mong đợi: {$expectedAmount}, Nhận được: {$paidAmount}")
            ]);
        }
        
        return $amountMatch;
    }

    public function updateTransactionStatus(int $transactionId, string $status, array $additionalData = []): bool
    {
        $updateData = array_merge([
            'trang_thai_duyet' => addslashes($status)
        ], $additionalData);
        
        if ($status === 'THANH_CONG') {
            $updateData['ngay_duyet'] = date('Y-m-d H:i:s');
        }
        
        $result = $this->thanhToanModel->update($transactionId, $updateData);
        
        return $result > 0;
    }

    private function getGatewayInstance(string $paymentMethod): ?PaymentGatewayInterface
    {
        $gatewayMap = [
            'COD' => 'CODHandler',
            'CHUYEN_KHOAN' => 'VNPayGateway'
        ];
        
        $gatewayClass = $gatewayMap[$paymentMethod] ?? null;
        
        if (!$gatewayClass) {
            return null;
        }
        
        $gatewayPath = __DIR__ . "/{$gatewayClass}.php";
        
        if (!file_exists($gatewayPath)) {
            return null;
        }
        
        require_once $gatewayPath;
        
        return new $gatewayClass();
    }

    private function getGatewayName(string $paymentMethod): string
    {
        $gatewayMap = [
            'COD' => 'COD',
            'CHUYEN_KHOAN' => 'VNPAY'
        ];
        
        return $gatewayMap[$paymentMethod] ?? 'UNKNOWN';
    }

    public function getTransaction(int $transactionId): ?array
    {
        return $this->thanhToanModel->findById($transactionId);
    }

    public function getTransactionByOrderId(int $donHangId): ?array
    {
        $sql = "SELECT * FROM thanh_toan WHERE don_hang_id = $donHangId LIMIT 1";
        $result = $this->thanhToanModel->query($sql);
        
        return !empty($result) ? $result[0] : null;
    }
}
