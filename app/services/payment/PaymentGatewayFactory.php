<?php

class PaymentGatewayFactory
{
    private static array $gatewayMap = [
        'COD' => 'CODHandler',
        'CHUYEN_KHOAN' => 'VNPayGateway',
        'VIETQR' => 'VietQRGateway',
        'PAYPAL' => 'PayPalGateway'
    ];

    public static function create(?string $paymentMethod): ?PaymentGatewayInterface
    {
        if ($paymentMethod === null || $paymentMethod === '') {
            error_log("[PaymentGatewayFactory] Invalid payment method: null or empty");
            return null;
        }

        if (!isset(self::$gatewayMap[$paymentMethod])) {
            error_log("[PaymentGatewayFactory] Unknown payment method: {$paymentMethod}");
            return null;
        }

        $gatewayClass = self::$gatewayMap[$paymentMethod];
        $gatewayPath = __DIR__ . '/' . $gatewayClass . '.php';

        if (!file_exists($gatewayPath)) {
            error_log("[PaymentGatewayFactory] Gateway file not found: {$gatewayPath}");
            return null;
        }

        require_once $gatewayPath;

        $gateway = new $gatewayClass();

        if (!($gateway instanceof PaymentGatewayInterface)) {
            error_log("[PaymentGatewayFactory] Gateway {$gatewayClass} does not implement PaymentGatewayInterface");
            return null;
        }

        error_log("[PaymentGatewayFactory] Successfully created gateway for method: {$paymentMethod}");
        return $gateway;
    }
}
