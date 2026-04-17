<?php

/**
 * PaymentGatewayFactory
 * 
 * Factory Pattern implementation for creating payment gateway instances.
 * Centralizes gateway instantiation logic and provides validation.
 */
class PaymentGatewayFactory
{
    /**
     * Gateway mapping: payment method => gateway class name
     */
    private static array $gatewayMap = [
        'COD' => 'CODHandler',
        'CHUYEN_KHOAN' => 'VNPayGateway',
        'VIETQR' => 'VietQRGateway',
        'PAYPAL' => 'PayPalGateway'
    ];

    /**
     * Create payment gateway instance based on payment method
     * 
     * @param string|null $paymentMethod Payment method code (COD, CHUYEN_KHOAN, VIETQR, PAYPAL)
     * @return PaymentGatewayInterface|null Gateway instance or null if invalid
     */
    public static function create(?string $paymentMethod): ?PaymentGatewayInterface
    {
        // Validate input
        if ($paymentMethod === null || $paymentMethod === '') {
            error_log("[PaymentGatewayFactory] Invalid payment method: null or empty");
            return null;
        }

        // Check if payment method exists in mapping
        if (!isset(self::$gatewayMap[$paymentMethod])) {
            error_log("[PaymentGatewayFactory] Unknown payment method: {$paymentMethod}");
            return null;
        }

        $gatewayClass = self::$gatewayMap[$paymentMethod];
        $gatewayPath = __DIR__ . '/' . $gatewayClass . '.php';

        // Validate gateway file exists
        if (!file_exists($gatewayPath)) {
            error_log("[PaymentGatewayFactory] Gateway file not found: {$gatewayPath}");
            return null;
        }

        // Require gateway file
        require_once $gatewayPath;

        // Instantiate gateway
        $gateway = new $gatewayClass();

        // Verify instance implements PaymentGatewayInterface
        if (!($gateway instanceof PaymentGatewayInterface)) {
            error_log("[PaymentGatewayFactory] Gateway {$gatewayClass} does not implement PaymentGatewayInterface");
            return null;
        }

        error_log("[PaymentGatewayFactory] Successfully created gateway for method: {$paymentMethod}");
        return $gateway;
    }
}
