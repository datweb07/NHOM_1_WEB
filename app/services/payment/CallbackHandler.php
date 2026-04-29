<?php

require_once __DIR__ . '/PaymentService.php';
require_once __DIR__ . '/VNPayGateway.php';
require_once dirname(__DIR__, 2) . '/models/entities/TransactionLog.php';
require_once dirname(__DIR__, 2) . '/models/entities/DonHang.php';
require_once dirname(__DIR__, 2) . '/models/entities/PhienBanSanPham.php';

class CallbackHandler
{
    private PaymentService $paymentService;
    private TransactionLog $transactionLog;
    private DonHang $donHangModel;
    private PhienBanSanPham $phienBanModel;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
        $this->transactionLog = new TransactionLog();
        $this->donHangModel = new DonHang();
        $this->phienBanModel = new PhienBanSanPham();
    }

    public function handleVNPayCallback(array $data): array
    {
        $startTime = microtime(true);

        $sourceIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->logCallbackRequest('VNPAY', $data, $sourceIp);
        
        $transactionId = $data['vnp_TxnRef'] ?? null;
        
        if (!$transactionId) {
            return [
                'RspCode' => '99',
                'Message' => 'Invalid transaction reference'
            ];
        }

        $transaction = $this->paymentService->getTransaction($transactionId);
        
        if (!$transaction) {
            return [
                'RspCode' => '01',
                'Message' => 'Order not found'
            ];
        }

        $this->transactionLog->logCallback($transactionId, $data, false);

        $gateway = new VNPayGateway();
        $isValidSignature = $gateway->verifyCallback($data);

        if (!$isValidSignature) {
            $this->logSecurityViolation($transactionId, 'VNPAY', 'SIGNATURE_FAILED', [
                'source_ip' => $sourceIp,
                'data' => $data
            ]);
            
            $this->transactionLog->logResponse($transactionId, [
                'error' => 'Invalid signature',
                'severity' => 'critical',
                'ip' => $sourceIp
            ], 'SIGNATURE_FAILED');

            return [
                'RspCode' => '97',
                'Message' => 'Invalid signature'
            ];
        }

        $this->transactionLog->logCallback($transactionId, $data, true);

        $gatewayTransactionId = $data['vnp_TransactionNo'] ?? null;
        
        if ($gatewayTransactionId) {
            $existingLog = $this->transactionLog->findByGatewayTransactionId($gatewayTransactionId);
            
            if ($existingLog && $existingLog['thanh_toan_id'] != $transactionId) {
                return [
                    'RspCode' => '02',
                    'Message' => 'Duplicate transaction'
                ];
            }
        }

        if ($this->paymentService->checkTransactionTimeout($transactionId)) {

            $this->logSecurityViolation($transactionId, 'VNPAY', 'EXPIRED_CALLBACK', [
                'source_ip' => $sourceIp,
                'gateway_transaction_id' => $gatewayTransactionId
            ]);
            
            $this->handleExpiredTransaction($transaction);
            
            return [
                'RspCode' => '04',
                'Message' => 'Transaction expired'
            ];
        }

        $paidAmount = ($data['vnp_Amount'] ?? 0) / 100; 
        
        if (!$this->paymentService->validateAmount($transactionId, $paidAmount)) {
            $this->logSecurityViolation($transactionId, 'VNPAY', 'AMOUNT_MISMATCH', [
                'source_ip' => $sourceIp,
                'paid_amount' => $paidAmount,
                'expected_amount' => $transaction['so_tien']
            ]);
            
            return [
                'RspCode' => '04',
                'Message' => 'Invalid amount'
            ];
        }

        $responseCode = $data['vnp_ResponseCode'] ?? '99';
        

        if ($responseCode === '00') {
            error_log(sprintf(
                "[VNPAY SUCCESS] Transaction: %s, Gateway Transaction: %s, Amount: %s",
                $transactionId,
                $gatewayTransactionId,
                $paidAmount
            ));
            
            $this->handleSuccessfulPayment($transaction, $gatewayTransactionId, 'VNPAY', $data);
            
            $result = [
                'RspCode' => '00',
                'Message' => 'Success'
            ];
        } else {
            $scenario = $responseCode === '24' ? 'USER CANCELED' : 'PAYMENT FAILED';
            error_log(sprintf(
                "[VNPAY %s] Transaction: %s, Response Code: %s, Amount: %s",
                $scenario,
                $transactionId,
                $responseCode,
                $paidAmount
            ));
            
            $this->handleFailedPayment($transaction, $responseCode, 'VNPAY', $data);
            
            $result = [
                'RspCode' => '00',
                'Message' => 'Success'
            ];
        }


        $elapsedTime = microtime(true) - $startTime;
        
        if ($elapsedTime > 3) {
            error_log("VNPay callback processing took {$elapsedTime} seconds");
        }

        return $result;
    }

    private function handleSuccessfulPayment(array $transaction, ?string $gatewayTransactionId, string $gatewayName, array $callbackData): void
    {
        $transactionId = $transaction['id'];
        $donHangId = $transaction['don_hang_id'];

        $updateData = [
            'gateway_transaction_id' => $gatewayTransactionId ? addslashes($gatewayTransactionId) : null
        ];

        $this->paymentService->updateTransactionStatus($transactionId, 'THANH_CONG', $updateData);

        $this->donHangModel->update($donHangId, [
            'trang_thai' => 'DA_XAC_NHAN'
        ]);

        $this->transactionLog->logResponse($transactionId, [
            'status' => 'success',
            'gateway_transaction_id' => $gatewayTransactionId,
            'gateway_name' => $gatewayName,
            'callback_data' => $callbackData
        ], 'SUCCESS');

        if ($gatewayTransactionId) {
            $logs = $this->transactionLog->getByThanhToanId($transactionId);
            if (!empty($logs)) {
                $this->transactionLog->updateGatewayTransactionId($logs[0]['id'], $gatewayTransactionId);
            }
        }

        try {
            require_once dirname(__DIR__, 2) . '/services/events/EventManager.php';
            require_once dirname(__DIR__, 2) . '/services/events/EmailObserver.php';
            require_once dirname(__DIR__, 2) . '/services/mailer/MailerService.php';
            
            $emailNhan = null;
            $orders = $this->donHangModel->query("SELECT thong_tin_guest FROM don_hang WHERE id = $donHangId LIMIT 1");
            if (!empty($orders)) {
                $thongTinGuest = $orders[0]['thong_tin_guest'] ?? null;
                if ($thongTinGuest) {
                    $guestInfo = json_decode($thongTinGuest, true);
                    $emailNhan = $guestInfo['email'] ?? null;
                }
            }
            
            if ($emailNhan) {
                $eventManager = new \App\Services\Events\EventManager();
                $mailerService = new \MailerService();
                $emailObserver = new \App\Services\Events\EmailObserver($mailerService);
                $eventManager->attach($emailObserver);
                
                $eventManager->notify('PAYMENT_SUCCESS', [
                    'order_id' => $donHangId,
                    'payment_method' => $this->formatPaymentMethodForEmail($gatewayName),
                    'transaction_id' => $gatewayTransactionId ?? 'N/A',
                    'email' => $emailNhan, 
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
                
                error_log("CallbackHandler: Triggered PAYMENT_SUCCESS event with email: $emailNhan");
            } else {
                error_log("CallbackHandler: Cannot send PAYMENT_SUCCESS email - no email found for order #$donHangId");
            }
        } catch (\Exception $e) {
            error_log("Failed to trigger PAYMENT_SUCCESS event: " . $e->getMessage());
        }
    }

    private function handleFailedPayment(array $transaction, string $errorCode, string $gatewayName, array $callbackData): void
    {
        $transactionId = $transaction['id'];
        $donHangId = $transaction['don_hang_id'];

        $gateway = new VNPayGateway();
        $errorMessage = $gateway->getErrorMessage($errorCode);

        $this->paymentService->updateTransactionStatus($transactionId, 'THAT_BAI', [
            'error_code' => addslashes($errorCode),
            'error_message' => addslashes($errorMessage)
        ]);

        $this->donHangModel->update($donHangId, [
            'trang_thai' => 'DA_HUY'
        ]);

        $this->restoreInventory($donHangId);

        error_log(sprintf(
            "[PAYMENT FAILED] Gateway: %s, Transaction: %d, Order: %d, Error Code: %s, Error Message: %s",
            $gatewayName,
            $transactionId,
            $donHangId,
            $errorCode,
            $errorMessage
        ));

        $this->transactionLog->logResponse($transactionId, [
            'status' => 'failed',
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'gateway_name' => $gatewayName,
            'callback_data' => $callbackData
        ], 'FAILED');
    }

    private function handleExpiredTransaction(array $transaction): void
    {
        $transactionId = $transaction['id'];
        $donHangId = $transaction['don_hang_id'];

        $this->paymentService->updateTransactionStatus($transactionId, 'THAT_BAI', [
            'error_code' => 'EXPIRED',
            'error_message' => addslashes('Giao dịch đã hết hạn')
        ]);

        $this->donHangModel->update($donHangId, [
            'trang_thai' => 'DA_HUY'
        ]);

        $this->restoreInventory($donHangId);
    }

    private function restoreInventory(int $donHangId): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/ChiTietDon.php';
        $chiTietDonModel = new ChiTietDon();

        $orderItems = $chiTietDonModel->layTheoDonHang($donHangId);

        foreach ($orderItems as $item) {
            $phienBanId = $item['phien_ban_san_pham_id'];
            $quantity = $item['so_luong'];

            $phienBan = $this->phienBanModel->findById($phienBanId);
            
            if ($phienBan) {
                $newQuantity = $phienBan['so_luong'] + $quantity;
                $this->phienBanModel->update($phienBanId, [
                    'so_luong' => $newQuantity
                ]);
            }
        }
    }

    private function logCallbackRequest(string $gatewayName, array $data, string $sourceIp): void
    {
        $safeData = $data;
        unset($safeData['vnp_SecureHash']);
        unset($safeData['signature']);
        
        error_log(sprintf(
            "[%s CALLBACK] Source IP: %s, Timestamp: %s, Data: %s",
            $gatewayName,
            $sourceIp,
            date('Y-m-d H:i:s'),
            json_encode($safeData, JSON_UNESCAPED_UNICODE)
        ));
    }

    private function logSecurityViolation(int $transactionId, string $gatewayName, string $violationType, array $details): void
    {
        $logMessage = sprintf(
            "[SECURITY VIOLATION] Severity: CRITICAL, Transaction: %d, Gateway: %s, Type: %s, Timestamp: %s, Details: %s",
            $transactionId,
            $gatewayName,
            $violationType,
            date('Y-m-d H:i:s'),
            json_encode($details, JSON_UNESCAPED_UNICODE)
        );
        
        error_log($logMessage);
        
        $this->transactionLog->logResponse($transactionId, [
            'severity' => 'critical',
            'violation_type' => $violationType,
            'gateway_name' => $gatewayName,
            'details' => $details,
            'timestamp' => date('Y-m-d H:i:s')
        ], $violationType);
    }

    private function formatPaymentMethodForEmail(string $gatewayName): string
    {
        $methods = [
            'VNPAY' => 'Chuyển khoản ngân hàng (VNPay)',
            'PAYPAL' => 'Thanh toán PayPal',
            'VIETQR' => 'Chuyển khoản VietQR'
        ];

        return $methods[$gatewayName] ?? $gatewayName;
    }
}
