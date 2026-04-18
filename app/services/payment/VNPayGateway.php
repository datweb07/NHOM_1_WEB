<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

class VNPayGateway implements PaymentGatewayInterface
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;
    private string $returnUrl;
    private string $ipnUrl;

    public function __construct()
    {
        $envConfig = EnvSetup::env(dirname(__DIR__, 3));

        $this->tmnCode = $envConfig('VNPAY_TMN_CODE');
        $this->hashSecret = $envConfig('VNPAY_HASH_SECRET');
        $this->url = $envConfig('VNPAY_URL') ?: 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';

        $baseUrl = $envConfig('APP_URL') ?: 'http://localhost:3000';

        $this->returnUrl = $baseUrl . '/thanh-toan/return/vnpay';
        $this->ipnUrl = $baseUrl . '/thanh-toan/callback/vnpay';
    }

    public function generatePaymentUrl(array $transaction): ?string
    {
        if (empty($this->tmnCode) || empty($this->hashSecret)) {
            $this->recordHealthFailure();
            return null;
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnpTxnRef = $transaction['id'] ?? time();

        $vnpOrderInfo = 'Thanh toan don hang ' . ($transaction['don_hang_id'] ?? $vnpTxnRef);

        $vnpOrderType = 'billpayment';
        $vnpAmount = ((int) $transaction['so_tien']) * 100;
        $vnpLocale = 'vn';

        $vnpIpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($vnpIpAddr === '::1' || !filter_var($vnpIpAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $vnpIpAddr = '127.0.0.1';
        }

        $startTime = date('YmdHis');
        $expireTime = date('YmdHis', strtotime('+15 minutes'));

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $vnpAmount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnpIpAddr,
            "vnp_Locale" => $vnpLocale,
            "vnp_OrderInfo" => $vnpOrderInfo,
            "vnp_OrderType" => $vnpOrderType,
            "vnp_ReturnUrl" => $this->returnUrl,
            "vnp_TxnRef" => $vnpTxnRef,
            "vnp_ExpireDate" => $expireTime,
        ];

        ksort($inputData);

        $hashData = "";
        $query = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $vnpUrl = $this->url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        $this->logRequest((string) $vnpTxnRef, $inputData);
        $this->logResponse((string) $vnpTxnRef, ['payment_url' => $vnpUrl, 'status' => 'URL_GENERATED']);
        $this->recordHealthSuccess();

        return $vnpUrl;
    }

    public function verifyCallback(array $data): bool
    {
        $isValid = $this->verifySignature($data);

        if (!$isValid) {
            $this->recordHealthFailure();
            return false;
        }

        $responseCode = $data['vnp_ResponseCode'] ?? '99';
        if ($responseCode === '00') {
            $this->recordHealthSuccess();
        } else {
            $this->recordHealthFailure();
        }

        return true;
    }

    public function verifyReturnUrl(array $data): bool
    {
        return $this->verifySignature($data);
    }

    private function verifySignature(array $data): bool
    {
        if (empty($this->hashSecret)) {
            $this->logSignatureVerification($data['vnp_TxnRef'] ?? 'unknown', false, 'Missing hash secret');
            return false;
        }

        $vnpSecureHash = $data['vnp_SecureHash'] ?? '';

        if (empty($vnpSecureHash)) {
            $this->logSignatureVerification($data['vnp_TxnRef'] ?? 'unknown', false, 'Missing signature in request');
            return false;
        }

        $dataForVerification = $data;
        unset($dataForVerification['vnp_SecureHash']);
        unset($dataForVerification['vnp_SecureHashType']);

        ksort($dataForVerification);

        $hashData = "";
        $i = 0;

        foreach ($dataForVerification as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $isValid = hash_equals($secureHash, $vnpSecureHash);

        $this->logSignatureVerification(
            $data['vnp_TxnRef'] ?? 'unknown',
            $isValid,
            $isValid ? 'Signature valid' : 'Signature mismatch'
        );

        return $isValid;
    }

    public function getErrorMessage(string $errorCode): string
    {
        $errorMessages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần.',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do: Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch.',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê).',
        ];

        return $errorMessages[$errorCode] ?? 'Đã xảy ra lỗi không xác định với thanh toán VNPay.';
    }

    public function getTransactionStatus(string $responseCode): string
    {
        return $responseCode === '00' ? 'THANH_CONG' : 'THAT_BAI';
    }

    public function isConfigured(): bool
    {
        return !empty($this->tmnCode) && !empty($this->hashSecret);
    }

    public function initiateRefund(string $transactionId, float $amount, string $reason): array
    {
        if (empty($this->tmnCode) || empty($this->hashSecret)) {
            return ['success' => false, 'message' => 'VNPay chưa được cấu hình', 'refund_id' => null];
        }

        $isSandbox = strpos($this->url, 'sandbox') !== false;
        
        if ($isSandbox) {
            //vì vnpay ko hỗ trợ refund, nên nhóm tạo VNPay sanbox mock để mô phỏng hoàn tiền
            error_log(sprintf(
                "[VNPAY REFUND MOCK] Transaction: %s, Amount: %s, Reason: %s, Timestamp: %s",
                $transactionId,
                $amount,
                $reason,
                date('Y-m-d H:i:s')
            ));
            
            return [
                'success' => true,
                'message' => 'Hoàn tiền thành công (Sandbox Mode - Simulated)',
                'refund_id' => 'REFUND_' . $transactionId . '_' . time(),
            ];
        }


        $refundUrl = str_replace('/paymentv2/vpcpay.html', '/merchant_webapi/api/transaction', $this->url);

        $vnpRequestId = time() . rand(1000, 9999);
        $vnpAmount = ((int) $amount) * 100;
        $vnpTransactionType = '02'; // 02 = Hoàn tiền toàn phần

        $safeReason = preg_replace('/[^a-zA-Z0-9 ]/', '', $reason);

        $inputData = [
            "vnp_RequestId" => $vnpRequestId,
            "vnp_Version" => "2.1.0",
            "vnp_Command" => "refund",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_TransactionType" => $vnpTransactionType,
            "vnp_TxnRef" => $transactionId,
            "vnp_Amount" => $vnpAmount,
            "vnp_OrderInfo" => $safeReason,
            "vnp_TransactionNo" => $transactionId,
            "vnp_TransactionDate" => date('YmdHis'),
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CreateBy" => 'Admin',
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ];

        ksort($inputData);

        $hashData = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $inputData['vnp_SecureHash'] = $vnpSecureHash;

        $result = $this->execPostRequest($refundUrl, json_encode($inputData));

        if (!$result) {
            return ['success' => false, 'message' => 'Không thể kết nối đến VNPay', 'refund_id' => null];
        }

        $jsonResult = json_decode($result, true);
        $responseCode = $jsonResult['vnp_ResponseCode'] ?? '99';

        if ($responseCode === '00') {
            return [
                'success' => true,
                'message' => 'Hoàn tiền thành công',
                'refund_id' => $jsonResult['vnp_TransactionNo'] ?? null,
            ];
        }

        return [
            'success' => false,
            'message' => $this->getErrorMessage($responseCode),
            'refund_id' => null,
        ];
    }

    private function execPostRequest(string $url, string $data)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function logRequest(string $transactionId, array $requestData): void
    {
        $safeData = $requestData;
        unset($safeData['vnp_SecureHash']);

        error_log(sprintf(
            "[VNPAY REQUEST] Transaction: %s, Timestamp: %s, Data: %s",
            $transactionId,
            date('Y-m-d H:i:s'),
            json_encode($safeData, JSON_UNESCAPED_UNICODE)
        ));
    }

    private function logResponse(string $transactionId, array $responseData): void
    {
        error_log(sprintf(
            "[VNPAY RESPONSE] Transaction: %s, Timestamp: %s, Data: %s",
            $transactionId,
            date('Y-m-d H:i:s'),
            json_encode($responseData, JSON_UNESCAPED_UNICODE)
        ));
    }

    private function logSignatureVerification(string $transactionId, bool $success, string $message): void
    {
        error_log(sprintf(
            "[VNPAY SIGNATURE] Transaction: %s, Status: %s, Timestamp: %s, Message: %s",
            $transactionId,
            $success ? 'SUCCESS' : 'FAILED',
            date('Y-m-d H:i:s'),
            $message
        ));
    }

    private function recordHealthSuccess(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        (new GatewayHealth())->recordSuccess('VNPay');
    }

    private function recordHealthFailure(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        (new GatewayHealth())->recordFailure('VNPay');
    }
}