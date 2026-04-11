<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class TransactionLog extends BaseModel
{
    public function __construct()
    {
        parent::__construct('transaction_log');
    }

    public function logRequest(int $thanhToanId, string $gatewayName, array $requestData): int
    {
        $data = [
            'thanh_toan_id' => $thanhToanId,
            'gateway_name' => addslashes($gatewayName),
            'request_data' => addslashes(json_encode($requestData, JSON_UNESCAPED_UNICODE)),
            'status' => 'PENDING'
        ];

        return $this->create($data);
    }

    public function logResponse(int $thanhToanId, array $responseData, string $status): int
    {

        $sql = "SELECT id FROM {$this->table}
                WHERE thanh_toan_id = $thanhToanId
                ORDER BY created_at DESC
                LIMIT 1";
        
        $result = $this->query($sql);
        
        if (empty($result)) {

            return $this->create([
                'thanh_toan_id' => $thanhToanId,
                'response_data' => addslashes(json_encode($responseData, JSON_UNESCAPED_UNICODE)),
                'status' => addslashes($status)
            ]);
        }
        

        $logId = $result[0]['id'];
        return $this->update($logId, [
            'response_data' => addslashes(json_encode($responseData, JSON_UNESCAPED_UNICODE)),
            'status' => addslashes($status)
        ]);
    }

    public function logCallback(int $thanhToanId, array $callbackData, bool $verificationResult): int
    {

        $sql = "SELECT id FROM {$this->table}
                WHERE thanh_toan_id = $thanhToanId
                ORDER BY created_at DESC
                LIMIT 1";
        
        $result = $this->query($sql);
        
        $callbackDataWithVerification = array_merge($callbackData, [
            'signature_verified' => $verificationResult
        ]);
        
        if (empty($result)) {

            return $this->create([
                'thanh_toan_id' => $thanhToanId,
                'callback_data' => addslashes(json_encode($callbackDataWithVerification, JSON_UNESCAPED_UNICODE)),
                'status' => 'PENDING'
            ]);
        }
        

        $logId = $result[0]['id'];
        return $this->update($logId, [
            'callback_data' => addslashes(json_encode($callbackDataWithVerification, JSON_UNESCAPED_UNICODE))
        ]);
    }

    public function findByGatewayTransactionId(string $gatewayTransactionId): ?array
    {
        $safeTransactionId = addslashes($gatewayTransactionId);
        
        $sql = "SELECT * FROM {$this->table}
                WHERE gateway_transaction_id = '$safeTransactionId'
                LIMIT 1";
        
        $result = $this->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    public function getByThanhToanId(int $thanhToanId): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE thanh_toan_id = $thanhToanId
                ORDER BY created_at ASC";
        
        return $this->query($sql);
    }

    public function getByGateway(string $gatewayName, int $limit = 20, int $offset = 0): array
    {
        $safeGatewayName = addslashes($gatewayName);
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $sql = "SELECT * FROM {$this->table}
                WHERE gateway_name = '$safeGatewayName'
                ORDER BY created_at DESC
                LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }

    public function getByStatus(string $status, int $limit = 20, int $offset = 0): array
    {
        $safeStatus = addslashes($status);
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $sql = "SELECT * FROM {$this->table}
                WHERE status = '$safeStatus'
                ORDER BY created_at DESC
                LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }

    public function updateGatewayTransactionId(int $logId, string $gatewayTransactionId): int
    {
        return $this->update($logId, [
            'gateway_transaction_id' => addslashes($gatewayTransactionId)
        ]);
    }

    public function getByDateRange(string $from, string $to, ?string $gatewayName = null): array
    {
        $safeFrom = addslashes($from);
        $safeTo = addslashes($to);
        
        $where = "DATE(created_at) BETWEEN '$safeFrom' AND '$safeTo'";
        
        if ($gatewayName !== null && $gatewayName !== '') {
            $safeGatewayName = addslashes($gatewayName);
            $where .= " AND gateway_name = '$safeGatewayName'";
        }
        
        $sql = "SELECT * FROM {$this->table}
                WHERE $where
                ORDER BY created_at DESC";
        
        return $this->query($sql);
    }

    public function countByStatusAndGateway(string $gatewayName, string $status, int $hours = 24): int
    {
        $safeGatewayName = addslashes($gatewayName);
        $safeStatus = addslashes($status);
        $hours = max(1, (int)$hours);
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE gateway_name = '$safeGatewayName'
                AND status = '$safeStatus'
                AND created_at >= DATE_SUB(NOW(), INTERVAL $hours HOUR)";
        
        $result = $this->query($sql);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    public function getFailedTransactions(int $limit = 20, int $offset = 0): array
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $sql = "SELECT tl.*, tt.don_hang_id, tt.so_tien
                FROM {$this->table} tl
                LEFT JOIN thanh_toan tt ON tl.thanh_toan_id = tt.id
                WHERE tl.status = 'FAILED'
                ORDER BY tl.created_at DESC
                LIMIT $limit OFFSET $offset";
        
        return $this->query($sql);
    }
}
?>
