<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class GatewayHealth extends BaseModel
{
    public function __construct()
    {
        parent::__construct('gateway_health');
    }

    public function recordSuccess(string $gatewayName): bool
    {
        $sql = "INSERT INTO gateway_health (gateway_name, success_count, failure_count, last_success_at, updated_at)
                VALUES (?, 1, 0, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    success_count = success_count + 1,
                    last_success_at = NOW(),
                    updated_at = NOW()";
        
        $stmt = mysqli_prepare($this->link, $sql);
        if (!$stmt) {
            error_log("[GATEWAY_HEALTH] Failed to prepare recordSuccess statement: " . mysqli_error($this->link));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, 's', $gatewayName);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if (!$result) {
            error_log("[GATEWAY_HEALTH] Failed to record success for {$gatewayName}: " . mysqli_error($this->link));
        }
        
        return $result;
    }

    public function recordFailure(string $gatewayName): bool
    {
        $sql = "INSERT INTO gateway_health (gateway_name, success_count, failure_count, last_failure_at, updated_at)
                VALUES (?, 0, 1, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    failure_count = failure_count + 1,
                    last_failure_at = NOW(),
                    updated_at = NOW()";
        
        $stmt = mysqli_prepare($this->link, $sql);
        if (!$stmt) {
            error_log("[GATEWAY_HEALTH] Failed to prepare recordFailure statement: " . mysqli_error($this->link));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, 's', $gatewayName);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if (!$result) {
            error_log("[GATEWAY_HEALTH] Failed to record failure for {$gatewayName}: " . mysqli_error($this->link));
        }
        

        $this->checkFailureThreshold($gatewayName);
        
        return $result;
    }

    public function getSuccessRate(string $gatewayName, int $hours = 24): float
    {
        $health = $this->getByGatewayName($gatewayName);
        
        if (!$health) {
            return 0.0;
        }
        
        $successCount = (int)($health['success_count'] ?? 0);
        $failureCount = (int)($health['failure_count'] ?? 0);
        $totalCount = $successCount + $failureCount;
        
        if ($totalCount === 0) {
            return 100.0; 
        }
        
        return ($successCount / $totalCount) * 100;
    }

    public function getByGatewayName(string $gatewayName): ?array
    {
        $sql = "SELECT * FROM gateway_health WHERE gateway_name = ?";
        
        $stmt = mysqli_prepare($this->link, $sql);
        if (!$stmt) {
            return null;
        }
        
        mysqli_stmt_bind_param($stmt, 's', $gatewayName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $data ?: null;
    }

    public function getAllGateways(): array
    {
        return $this->getAll();
    }

    public function resetCounters(string $gatewayName): bool
    {
        $sql = "UPDATE gateway_health 
                SET success_count = 0, 
                    failure_count = 0,
                    last_success_at = NULL,
                    last_failure_at = NULL,
                    updated_at = NOW()
                WHERE gateway_name = ?";
        
        $stmt = mysqli_prepare($this->link, $sql);
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, 's', $gatewayName);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    private function checkFailureThreshold(string $gatewayName): void
    {
        $health = $this->getByGatewayName($gatewayName);
        
        if (!$health) {
            return;
        }
        
        $successCount = (int)($health['success_count'] ?? 0);
        $failureCount = (int)($health['failure_count'] ?? 0);
        $totalCount = $successCount + $failureCount;
        

        if ($totalCount >= 10) {
            $failureRate = ($failureCount / $totalCount) * 100;
            
            if ($failureRate > 50) {
                error_log(sprintf(
                    "[CRITICAL] Gateway %s failure rate exceeds 50%% (%.2f%%). Success: %d, Failures: %d",
                    $gatewayName,
                    $failureRate,
                    $successCount,
                    $failureCount
                ));
            }
        }
    }

    public function getAverageProcessingTime(string $gatewayName): float
    {
        return 0.0;
    }
}
