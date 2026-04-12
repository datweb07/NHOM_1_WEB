<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

class ZaloPayGateway implements PaymentGatewayInterface
{
    private string $appId;
    private string $key1;
    private string $key2;
    private string $url;
    private string $returnUrl;
    private string $callbackUrl;

    public function __construct()
    {

        $envConfig = EnvSetup::env(dirname(__DIR__, 3));

        $this->appId = trim($envConfig('ZALOPAY_APP_ID'));
        $this->key1 = trim($envConfig('ZALOPAY_KEY1'));
        $this->key2 = trim($envConfig('ZALOPAY_KEY2'));
        $this->url = trim($envConfig('ZALOPAY_URL'));
        
        $baseUrl = rtrim(trim($envConfig('APP_URL') ?: 'http://localhost:3000'), '/');
        $this->returnUrl = $baseUrl . '/thanh-toan/return/zalopay';
        $this->callbackUrl = $baseUrl . '/thanh-toan/callback/zalopay';
    }

    public function generatePaymentUrl(array $transaction): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $app_trans_id = date("ymd") . "_" . ($transaction['id'] ?? time()) . "_" . time();
        
        $amount = (int)$transaction['so_tien'];
        $app_time = (int)(time() * 1000); 

        $orderData = [
            "app_id" => (int)$this->appId, 
            "app_time" => $app_time,
            "app_trans_id" => $app_trans_id,
            "app_user" => "FPTShop_Customer",
            "item" => json_encode([["itemname" => "Don hang FPT Shop", "itemprice" => $amount, "itemquantity" => 1]]),
            "embed_data" => json_encode(["redirecturl" => $this->returnUrl]),
            "amount" => $amount,
            "description" => "Thanh toan don hang " . ($transaction['don_hang_id'] ?? time()), 
            "callback_url" => $this->callbackUrl
        ];

        $dataStr = $orderData["app_id"] . "|" . $orderData["app_trans_id"] . "|" . $orderData["app_user"] . "|" . $orderData["amount"] . "|" . $orderData["app_time"] . "|" . $orderData["embed_data"] . "|" . $orderData["item"];
        $orderData["mac"] = hash_hmac("sha256", $dataStr, $this->key1);

        $response = $this->execPostRequest($this->url, json_encode($orderData));
        $result = json_decode($response, true);

        if (isset($result['return_code']) && $result['return_code'] == 1) {
            return $result['order_url'];
        }

        error_log("[ZALOPAY ERROR] " . print_r($result, true));
        return null;
    }

    public function verifyCallback(array $data): bool
    {
        $reqMac = $data['mac'] ?? '';
        $reqData = $data['data'] ?? '';
        
        if (empty($reqMac) || empty($reqData)) return false;

        $mac = hash_hmac("sha256", $reqData, $this->key2);
        
        return $mac === $reqMac;
    }

    public function verifyReturnUrl(array $data): bool
    {
        $checksumData = ($data['appid'] ?? '') . "|" . 
                        ($data['apptransid'] ?? '') . "|" .
                        ($data['pmcid'] ?? '') . "|" . 
                        ($data['bankcode'] ?? '') . "|" .
                        ($data['amount'] ?? '') . "|" . 
                        ($data['discountamount'] ?? '') . "|" .
                        ($data['status'] ?? '');
        
        $mac = hash_hmac("sha256", $checksumData, $this->key2);
        
        return $mac === ($data['checksum'] ?? $data['mac'] ?? '');
    }

    public function getErrorMessage(string $errorCode): string
    {
        return $errorCode == '1' ? 'Giao dịch thành công' : 'Thanh toán ZaloPay thất bại';
    }

    public function getTransactionStatus(string $responseCode): string
    {
        return $responseCode == '1' ? 'THANH_CONG' : 'THAT_BAI';
    }

    public function isConfigured(): bool
    {
        return !empty($this->appId) && !empty($this->key1);
    }

    public function initiateRefund(string $transactionId, float $amount, string $reason): array
    {
        return [
            'success' => false, 
            'message' => 'Chức năng hoàn tiền đang phát triển', 
            'refund_id' => null
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
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }
}
