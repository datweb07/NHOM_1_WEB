<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

class VietQRGateway implements PaymentGatewayInterface
{
    private string $bankId;
    private string $accountNo;
    private string $accountName;
    private string $template;

    public function __construct()
    {
        $envConfig = EnvSetup::env(dirname(__DIR__, 3));

        $this->bankId = $envConfig('VIETQR_BANK_ID') ?: 'VCB';
        $this->accountNo = $envConfig('VIETQR_ACCOUNT_NO') ?: '';
        $this->accountName = $envConfig('VIETQR_ACCOUNT_NAME') ?: '';
        $this->template = $envConfig('VIETQR_TEMPLATE') ?: 'compact2';
    }

    public function generatePaymentUrl(array $transaction): ?string
    {
        if (empty($this->accountNo) || empty($this->accountName)) {
            $this->recordHealthFailure();
            return null;
        }

        $amount = (int) $transaction['so_tien'];
        $donHangId = $transaction['don_hang_id'] ?? '';
        $transactionId = $transaction['id'] ?? time();

        $description = !empty($donHangId) ? "DH{$donHangId}" : "TT{$transactionId}";

        $qrUrl = sprintf(
            'https://img.vietqr.io/image/%s-%s-%s.png?amount=%d&addInfo=%s&accountName=%s',
            $this->bankId,
            $this->accountNo,
            $this->template,
            $amount,
            urlencode($description),
            urlencode($this->accountName)
        );

        $this->logRequest((string) $transactionId, [
            'bank_id' => $this->bankId,
            'account_no' => $this->accountNo,
            'amount' => $amount,
            'description' => $description,
            'qr_url' => $qrUrl
        ]);

        $this->recordHealthSuccess();

        return $qrUrl;
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
            'MISSING_CONFIG' => 'Cấu hình VietQR chưa đầy đủ. Vui lòng liên hệ quản trị viên.',
            'INVALID_AMOUNT' => 'Số tiền thanh toán không hợp lệ.',
            'QR_GENERATION_FAILED' => 'Không thể tạo mã QR. Vui lòng thử lại sau.',
        ];

        return $errorMessages[$errorCode] ?? 'Đã xảy ra lỗi không xác định với thanh toán VietQR.';
    }

    public function isConfigured(): bool
    {
        return !empty($this->accountNo) && !empty($this->accountName);
    }

    public function getQRInfo(array $transaction): array
    {
        $amount = (int) $transaction['so_tien'];
        $donHangId = $transaction['don_hang_id'] ?? '';
        $transactionId = $transaction['id'] ?? time();
        $description = !empty($donHangId) ? "DH{$donHangId}" : "TT{$transactionId}";

        return [
            'qr_url' => $this->generatePaymentUrl($transaction),
            'bank_id' => $this->bankId,
            'account_no' => $this->accountNo,
            'account_name' => $this->accountName,
            'amount' => $amount,
            'description' => $description,
        ];
    }

    private function logRequest(string $transactionId, array $requestData): void
    {
        error_log(sprintf(
            "[VIETQR REQUEST] Transaction: %s, Timestamp: %s, Data: %s",
            $transactionId,
            date('Y-m-d H:i:s'),
            json_encode($requestData, JSON_UNESCAPED_UNICODE)
        ));
    }

    private function recordHealthSuccess(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        (new GatewayHealth())->recordSuccess('VietQR');
    }

    private function recordHealthFailure(): void
    {
        require_once dirname(__DIR__, 2) . '/models/entities/GatewayHealth.php';
        (new GatewayHealth())->recordFailure('VietQR');
    }
}
