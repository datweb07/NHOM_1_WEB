<?php

interface PaymentGatewayInterface
{
    public function generatePaymentUrl(array $transaction): ?string;

    public function verifyCallback(array $data): bool;

    public function verifyReturnUrl(array $data): bool;

    public function getErrorMessage(string $errorCode): string;
}
