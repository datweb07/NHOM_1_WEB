<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

/**
 * MomoGateway
 * 
 * Payment gateway handler for Momo e-wallet
 * Requirements: 3.1, 3.2, 3.3, 3.4, 4.2, 5.2, 6.1, 7.2, 7.4
 */
class MomoGateway implements PaymentGatewayInterface
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;
    private string $redirectUrl;
    private string $ipnUrl;

    public function __construct()
    {
        // Load environment variables
        $this->partnerCode = $_ENV['MOMO_PARTNER_CODE'] ?? '';
        $this->accessKey = $_ENV['MOMO_ACCESS_KEY'] ?? '';
        $this->secretKey = $_ENV['MOMO_SECRET_KEY'] ?? '';
        $this->endpoint = $_ENV['MOMO_URL'] ?? 'https://test-payment.momo.vn/v2/gateway/api/create';
        
        // Set redirect and IPN URLs
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost';
        $this->redirectUrl = $baseUrl . '/thanh-toan/return/momo';
        $this->ipnUrl = $baseUrl . '/thanh-toan/callback/momo';
    }

    /**
     * Generate payment URL for Momo redirect
     * Requirements: 3.1, 3.2, 3.3, 3.4, 6.1, 15.1, 15.2
     * 
     * @param array $transaction Transaction data
     * @return string|null Payment URL for redirect
     */
    public function generatePaymentUrl(array $transaction): ?string
    {
        if (empty($this->partnerCode) || empty($this->accessKey) || empty($this->secretKey)) {
            $this->recordHealthFailure();
            return null;
        }

        $orderId = (string)($transaction['id'] ?? time());
        $requestId = $orderId . '_' . time();
        $amount = (string)((int)$transaction['so_tien']);
        $orderInfo = 'Thanh toan don hang #' . ($transaction['don_hang_id'] ?? '');
        $requestType = "captureWallet";
        $extraData = "";

        // Build raw signature
        $rawHash = "accessKey=" . $this->accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&ipnUrl=" . $this->ipnUrl .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&partnerCode=" . $this->partnerCode .
                   "&redirectUrl=" . $this->redirectUrl .
                   "&requestId=" . $requestId .
                   "&requestType=" . $requestType;

        // Generate HMAC-SHA256 signature
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        // Build request data
        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl' => $this->ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        // Log API request (excluding secret keys) - Requirement 13.1
        $this->logRequest($orderId, $data);

        // Make HTTP POST request to Momo API
        $result = $this->execPostRequest($this->endpoint, json_encode($data));
        
        if (!$result) {
            // Log API response failure - Requirement 13.2
            $this->logResponse($orderId, ['error' => 'No response from Momo API']);
            $this->recordHealthFailure();
            return null;
        }

        $jsonResult = json_decode($result, true);

        // Log API response - Requirement 13.2
        $this->logResponse($orderId, $jsonResult);

        // Check if request was successful
        if (isset($jsonResult['payUrl']) && !empty($jsonResult['payUrl'])) {
            $this->recordHealthSuccess();
            return $jsonResult['payUrl'];
        }

        $this->recordHealthFailure();
        return null;
    }

    /**
     * Verify callback/IPN signature from Momo
     * Requirements: 4.2, 5.2, 15.1, 15.2
     * 
     * @param array $data Callback data received from Momo
     * @return bool True if signature is valid, false otherwise
     */
    public function verifyCallback(array $data): bool
    {
        $isValid = $this->verifySignature($data);
        
        // Record health based on callback verification
        if ($isValid) {
            $resultCode = (string)($data['resultCode'] ?? '99');
            if ($resultCode === '0') {
                $this->recordHealthSuccess();
            } else {
                $this->recordHealthFailure();
            }
        } else {
            $this->recordHealthFailure();
        }
        
        return $isValid;
    }

    /**
     * Verify return URL signature when customer returns from Momo
     * Requirements: 5.2
     * 
     * @param array $data Return URL parameters
     * @return bool True if signature is valid, false otherwise
     */
    public function verifyReturnUrl(array $data): bool
    {
        return $this->verifySignature($data);
    }

    /**
     * Verify HMAC-SHA256 signature
     * 
     * @param array $data Data with signature parameter
     * @return bool True if signature is valid
     */
    private function verifySignature(array $data): bool
    {
        if (empty($this->secretKey)) {
            // Log signature verification failure - Requirement 13.4
            $this->logSignatureVerification($data['orderId'] ?? 'unknown', false, 'Missing secret key');
            return false;
        }

        $signature = $data['signature'] ?? '';
        
        if (empty($signature)) {
            // Log signature verification failure - Requirement 13.4
            $this->logSignatureVerification($data['orderId'] ?? 'unknown', false, 'Missing signature in request');
            return false;
        }

        // Build raw signature for verification
        $rawHash = "accessKey=" . $this->accessKey .
                   "&amount=" . ($data['amount'] ?? '') .
                   "&extraData=" . ($data['extraData'] ?? '') .
                   "&message=" . ($data['message'] ?? '') .
                   "&orderId=" . ($data['orderId'] ?? '') .
                   "&orderInfo=" . ($data['orderInfo'] ?? '') .
                   "&orderType=" . ($data['orderType'] ?? '') .
                   "&partnerCode=" . $this->partnerCode .
                   "&payType=" . ($data['payType'] ?? '') .
                   "&requestId=" . ($data['requestId'] ?? '') .
                   "&responseTime=" . ($data['responseTime'] ?? '') .
                   "&resultCode=" . ($data['resultCode'] ?? '') .
                   "&transId=" . ($data['transId'] ?? '');

        // Generate HMAC-SHA256 signature
        $expectedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $isValid = $signature === $expectedSignature;
        
        // Log signature verification attempt - Requirement 13.4
        $this->logSignatureVerification($data['orderId'] ?? 'unknown', $isValid, $isValid ? 'Signature valid' : 'Signature mismatch');

        return $isValid;
    }

    /**
     * Get user-friendly error message in Vietnamese for Momo error code
     * Requirements: 7.2, 7.4
     * 
     * @param string $errorCode Error code from Momo
     * @return string User-friendly error message in Vietnamese
     */
    public function getErrorMessage(string $errorCode): string
    {
        $errorMessages = [
            '0' => 'Giao dịch thành công',
            '9000' => 'Giao dịch được khởi tạo, chờ người dùng xác nhận thanh toán.',
            '8000' => 'Giao dịch đang được xử lý.',
            '7000' => 'Giao dịch đang chờ thanh toán.',
            '1000' => 'Giao dịch đã được khởi tạo, chờ người dùng xác nhận thanh toán.',
            '11' => 'Truy cập bị từ chối.',
            '12' => 'Phiên bản API không được hỗ trợ cho yêu cầu này.',
            '13' => 'Xác thực dữ liệu thất bại (Checksum failed).',
            '20' => 'Định dạng dữ liệu gửi lên không đúng.',
            '21' => 'Số tiền giao dịch không hợp lệ.',
            '22' => 'Thông tin giao dịch không hợp lệ.',
            '40' => 'RequestId bị trùng.',
            '41' => 'OrderId bị trùng.',
            '42' => 'OrderId không hợp lệ hoặc không được tìm thấy.',
            '43' => 'Yêu cầu bị từ chối vì xung đột trong quá trình xử lý giao dịch.',
            '1001' => 'Giao dịch thanh toán thất bại do tài khoản người dùng không đủ tiền.',
            '1002' => 'Giao dịch bị từ chối do nhà phát hành tài khoản thanh toán.',
            '1003' => 'Giao dịch bị hủy.',
            '1004' => 'Giao dịch thất bại do số tiền thanh toán vượt quá hạn mức thanh toán của người dùng.',
            '1005' => 'Giao dịch thất bại do url hoặc QR code đã hết hạn.',
            '1006' => 'Giao dịch thất bại do người dùng đã từ chối xác nhận thanh toán.',
            '1007' => 'Giao dịch bị từ chối vì tài khoản người dùng đang ở trạng thái tạm khóa.',
            '1026' => 'Giao dịch bị hạn chế theo thể lệ chương trình khuyến mãi.',
            '1080' => 'Giao dịch hoàn tiền bị từ chối. Giao dịch thanh toán ban đầu không được tìm thấy.',
            '1081' => 'Giao dịch hoàn tiền bị từ chối. Giao dịch thanh toán ban đầu có thể đã được hoàn.',
            '2001' => 'Giao dịch thất bại do sai thông tin liên kết.',
            '2007' => 'Giao dịch thất bại do tài khoản người dùng đang bị tạm khóa.',
            '3001' => 'Liên kết thanh toán không tồn tại.',
            '3002' => 'Liên kết thanh toán đã hết hạn.',
            '3003' => 'Liên kết thanh toán đã được sử dụng.',
            '3004' => 'Liên kết thanh toán chưa được kích hoạt.',
            '4001' => 'Giao dịch bị hạn chế theo quy định.',
            '4010' => 'Đã vượt quá số lần thử thanh toán.',
            '4011' => 'Đã quá thời gian thanh toán. Xin vui lòng thực hiện lại giao dịch.',
            '4015' => 'Giao dịch thất bại do vi phạm chính sách thanh toán.',
            '4100' => 'Giao dịch thất bại do người dùng không hoàn tất xác thực giao dịch.',
            '10' => 'Hệ thống đang được bảo trì.',
            '99' => 'Lỗi không xác định.',
            '9999' => 'Giao dịch thất bại.'
        ];

        return $errorMessages[$errorCode] ?? 'Đã xảy ra lỗi không xác định với thanh toán Momo.';
    }

    /**
     * Get transaction status from Momo result code
     * 
     * @param string $resultCode Momo result code
     * @return string Transaction status (THANH_CONG, THAT_BAI, CHO_DUYET)
     */
    public function getTransactionStatus(string $resultCode): string
    {
        if ($resultCode === '0') {
            return 'THANH_CONG';
        }
        
        // Pending statuses
        if (in_array($resultCode, ['9000', '8000', '7000', '1000'])) {
            return 'CHO_DUYET';
        }
        
        return 'THAT_BAI';
    }

    /**
     * Execute POST request to Momo API
     * 
     * @param string $url API endpoint
     * @param string $data JSON data
     * @return string|false Response body or false on failure
     */
    private function execPostRequest(string $url, string $data)
    {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $result = curl_exec($ch);
        
        curl_close($ch);
        
        return $result;
    }

    /**
     * Check if Momo is configured
     * 
     * @return bool True if configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->partnerCode) && !empty($this->accessKey) && !empty($this->secretKey);
    }

    /**
     * Initiate refund for a Momo transaction
     * Requirements: 12.3
     * 
     * @param string $transactionId Gateway transaction ID (transId from Momo)
     * @param float $amount Refund amount
     * @param string $reason Refund reason
     * @return array Refund result with status and refund_id
     */
    public function initiateRefund(string $transactionId, float $amount, string $reason): array
    {
        if (empty($this->partnerCode) || empty($this->accessKey) || empty($this->secretKey)) {
            return [
                'success' => false,
                'message' => 'Momo chưa được cấu hình',
                'refund_id' => null
            ];
        }

        // Momo refund endpoint
        $refundEndpoint = str_replace('/v2/gateway/api/create', '/v2/gateway/api/refund', $this->endpoint);
        
        $orderId = 'REFUND_' . time();
        $requestId = $orderId . '_' . time();
        $amountStr = (string)((int)$amount);

        // Build raw signature for refund
        $rawHash = "accessKey=" . $this->accessKey .
                   "&amount=" . $amountStr .
                   "&description=" . $reason .
                   "&orderId=" . $orderId .
                   "&partnerCode=" . $this->partnerCode .
                   "&requestId=" . $requestId .
                   "&transId=" . $transactionId;

        // Generate HMAC-SHA256 signature
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        // Build request data
        $data = [
            'partnerCode' => $this->partnerCode,
            'orderId' => $orderId,
            'requestId' => $requestId,
            'amount' => $amountStr,
            'transId' => $transactionId,
            'lang' => 'vi',
            'description' => $reason,
            'signature' => $signature
        ];

        // Make HTTP POST request to Momo refund API
        $result = $this->execPostRequest($refundEndpoint, json_encode($data));
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Không thể kết nối đến Momo',
                'refund_id' => null
            ];
        }

        $jsonResult = json_decode($result, true);

        // Check result code
        $resultCode = (string)($jsonResult['resultCode'] ?? '99');
        
        if ($resultCode === '0') {
            return [
                'success' => true,
                'message' => 'Hoàn tiền thành công',
                'refund_id' => $jsonResult['transId'] ?? null
            ];
        }

        return [
            'success' => false,
            'message' => $this->getErrorMessage($resultCode),
            'refund_id' => null
        ];
    }

    /**
     * Log API request (excluding secret keys)
     * Requirements: 13.1, 13.7
     * 
     * @param string $orderId Order ID
     * @param array $requestData Request data
     */
    private function logRequest(string $orderId, array $requestData): void
    {
        // Remove sensitive data before logging
        $safeData = $requestData;
        unset($safeData['signature']);
        unset($safeData['accessKey']);
        
        error_log(sprintf(
            "[MOMO REQUEST] Order: %s, Timestamp: %s, Data: %s",
            $orderId,
            date('Y-m-d H:i:s'),
            json_encode($safeData, JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * Log API response
     * Requirements: 13.2
     * 
     * @param string $orderId Order ID
     * @param array $responseData Response data
     */
    private function logResponse(string $orderId, array $responseData): void
    {
        error_log(sprintf(
            "[MOMO RESPONSE] Order: %s, Timestamp: %s, Data: %s",
            $orderId,
            date('Y-m-d H:i:s'),
            json_encode($responseData, JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * Log signature verification attempt
     * Requirements: 13.4
     * 
     * @param string $orderId Order ID
     * @param bool $success Verification result
     * @param string $message Additional message
     */
    private function logSignatureVerification(string $orderId, bool $success, string $message): void
    {
        $status = $success ? 'SUCCESS' : 'FAILED';
        error_log(sprintf(
            "[MOMO SIGNATURE] Order: %s, Status: %s, Timestamp: %s, Message: %s",
            $orderId,
            $status,
            date('Y-m-d H:i:s'),
            $message
        ));
    }

    /**
     * Record successful gateway operation
     * Requirements: 15.1
     */
    private function recordHealthSuccess(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        $healthModel = new GatewayHealth();
        $healthModel->recordSuccess('Momo');
    }

    /**
     * Record failed gateway operation
     * Requirements: 15.2
     */
    private function recordHealthFailure(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        $healthModel = new GatewayHealth();
        $healthModel->recordFailure('Momo');
    }
}
