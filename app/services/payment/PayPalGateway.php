<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

class PayPalGateway implements PaymentGatewayInterface
{
    private string $clientId;
    private string $secret;
    private string $baseUrl;

    public function __construct()
    {
        $envConfig = EnvSetup::env(dirname(__DIR__, 3));

        $this->clientId = $envConfig('PAYPAL_CLIENT_ID') ?: '';
        $this->secret = $envConfig('PAYPAL_SECRET') ?: '';
        
        // Chọn URL gọi API tùy thuộc vào môi trường
        $mode = trim($envConfig('PAYPAL_MODE') ?? 'sandbox');
        $this->baseUrl = ($mode === 'sandbox')
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    private function getAccessToken(): ?string
    {
        error_log("DEBUG PAYPAL: ID = [" . $this->clientId . "] | Secret rỗng? = " . (empty($this->secret) ? 'CÓ (LỖI)' : 'KHÔNG (TỐT)'));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Xác thực Basic Auth bằng Client ID và Secret
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ":" . $this->secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        // Tắt SSL Verify ở local để tránh lỗi, lên host thật thì nên bật = true
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("[PAYPAL] Failed to get access token. HTTP Code: $httpCode, Response: $response");
            return null;
        }

        $data = json_decode($response);
        return $data->access_token ?? null;
    }

    public function generatePaymentUrl(array $transaction): ?string
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            $this->recordHealthFailure();
            return null;
        }

        $donHangId = $transaction['don_hang_id'] ?? '';
        $tongTien = (float)($transaction['so_tien'] ?? 0);
        
        $amountUSD = round($tongTien / 25000, 2);

        $envConfig = EnvSetup::env(dirname(__DIR__, 3));
        $appUrl = $envConfig('APP_URL') ?: 'http://localhost:3000';
        
        $returnUrl = $appUrl . '/thanh-toan/return/paypal';
        $cancelUrl = $appUrl . '/thanh-toan';

        $orderData = [
            "intent" => "CAPTURE", // CAPTURE nghĩa là trừ tiền ngay
            "purchase_units" => [
                [
                    "reference_id" => "DH_" . $donHangId,
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => (string)$amountUSD
                    ],
                    "description" => "Thanh toan don hang " . $donHangId
                ]
            ],
            "application_context" => [
                "return_url" => $returnUrl,   
                "cancel_url" => $cancelUrl,  
                "user_action" => "PAY_NOW"    
            ]
        ];

        $ch = curl_init($this->baseUrl . '/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->logRequest((string)($transaction['id'] ?? time()), [
            'don_hang_id' => $donHangId,
            'amount_vnd' => $tongTien,
            'amount_usd' => $amountUSD,
            'http_code' => $httpCode,
            'response' => $response
        ]);

        if ($httpCode !== 201) {
            error_log("[PAYPAL] Failed to create order. HTTP Code: $httpCode, Response: $response");
            $this->recordHealthFailure();
            return null;
        }

        $result = json_decode($response);

        if (isset($result->id) && isset($transaction['id'])) {
            require_once dirname(__DIR__, 2) . '/models/entities/ThanhToan.php';
            $thanhToanModel = new ThanhToan();
            $thanhToanModel->update($transaction['id'], [
                'gateway_transaction_id' => $result->id
            ]);
        }

        if (isset($result->links)) {
            foreach ($result->links as $link) {
                if ($link->rel === 'approve') {
                    $this->recordHealthSuccess();
                    return $link->href;
                }
            }
        }

        $this->recordHealthFailure();
        return null;
    }

    public function capturePayment(string $paypalOrderId): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Không thể xác thực với PayPal'];
        }

        $ch = curl_init($this->baseUrl . "/v2/checkout/orders/{$paypalOrderId}/capture");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log("[PAYPAL CAPTURE] HTTP Code: $httpCode, Response: $response");

        $result = json_decode($response);

        if ($httpCode === 201 && isset($result->status) && $result->status === 'COMPLETED') {
            return [
                'success' => true,
                'gateway_transaction_id' => $result->purchase_units[0]->payments->captures[0]->id ?? $paypalOrderId,
                'full_data' => $result
            ];
        }

        return [
            'success' => false,
            'message' => $result->message ?? 'Thanh toán thất bại',
            'data' => $result
        ];
    }

    public function verifyCallback(array $data): bool
    {
        return true;
    }

    public function verifyReturnUrl(array $data): bool
    {
        return !empty($data['token']);
    }

    public function getErrorMessage(string $errorCode): string
    {
        $errorMessages = [
            'MISSING_CONFIG' => 'Cấu hình PayPal chưa đầy đủ. Vui lòng liên hệ quản trị viên.',
            'INVALID_AMOUNT' => 'Số tiền thanh toán không hợp lệ.',
            'ORDER_CREATION_FAILED' => 'Không thể tạo đơn hàng PayPal. Vui lòng thử lại sau.',
            'CAPTURE_FAILED' => 'Không thể xác nhận thanh toán. Vui lòng liên hệ hỗ trợ.',
        ];

        return $errorMessages[$errorCode] ?? 'Đã xảy ra lỗi không xác định với thanh toán PayPal.';
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->secret);
    }

    private function logRequest(string $transactionId, array $requestData): void
    {
        error_log(sprintf(
            "[PAYPAL REQUEST] Transaction: %s, Timestamp: %s, Data: %s",
            $transactionId,
            date('Y-m-d H:i:s'),
            json_encode($requestData, JSON_UNESCAPED_UNICODE)
        ));
    }

    private function recordHealthSuccess(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        (new GatewayHealth())->recordSuccess('PayPal');
    }

    private function recordHealthFailure(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        (new GatewayHealth())->recordFailure('PayPal');
    }
}
