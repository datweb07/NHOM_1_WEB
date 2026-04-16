<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class Refund extends BaseModel
{
    public function __construct()
    {
        parent::__construct('refund');
    }

    public function createRefund(int $thanhToanId, float $amount, string $reason, ?int $adminId = null)
    {
        $sql = "INSERT INTO refund (thanh_toan_id, amount, status, reason, admin_id, created_at) 
                VALUES (?, ?, 'PENDING', ?, ?, NOW())";
        
        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param('idsi', $thanhToanId, $amount, $reason, $adminId);
        
        if ($stmt->execute()) {
            return $this->link->insert_id;
        }
        
        return false;
    }

    public function updateRefundStatus(int $id, string $status, ?string $gatewayRefundId = null): bool
    {
        $completedAt = ($status === 'COMPLETED') ? 'NOW()' : 'NULL';
        
        if ($gatewayRefundId !== null) {
            $sql = "UPDATE refund 
                    SET status = ?, gateway_refund_id = ?, completed_at = {$completedAt}
                    WHERE id = ?";
            
            $stmt = $this->link->prepare($sql);
            if (!$stmt) {
                return false;
            }
            
            $stmt->bind_param('ssi', $status, $gatewayRefundId, $id);
        } else {
            $sql = "UPDATE refund 
                    SET status = ?, completed_at = {$completedAt}
                    WHERE id = ?";
            
            $stmt = $this->link->prepare($sql);
            if (!$stmt) {
                return false;
            }
            
            $stmt->bind_param('si', $status, $id);
        }
        
        return $stmt->execute();
    }

    public function findByThanhToanId(int $thanhToanId): array
    {
        $sql = "SELECT * FROM refund WHERE thanh_toan_id = ? ORDER BY created_at DESC";
        
        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            return [];
        }
        
        $stmt->bind_param('i', $thanhToanId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRefundById(int $id): ?array
    {
        $sql = "SELECT * FROM refund WHERE id = ?";
        
        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $refund = $result->fetch_assoc();
        
        return $refund ?: null;
    }

    /**
     * Check if payment has any completed refunds
     * 
     * @param int $thanhToanId Payment ID
     * @return bool True if payment has at least one COMPLETED refund
     */
    public function hasCompletedRefund(int $thanhToanId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM refund 
                WHERE thanh_toan_id = ? AND status = 'COMPLETED'";
        
        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param('i', $thanhToanId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] ?? 0) > 0;
    }

    /**
     * Get refund statistics for a payment
     * 
     * @param int $thanhToanId Payment ID
     * @return array Array with 'total_refunded' (sum of amounts) and 'refund_count' (number of refunds)
     */
    public function getRefundStats(int $thanhToanId): array
    {
        $sql = "SELECT 
                    COALESCE(SUM(amount), 0) as total_refunded,
                    COUNT(*) as refund_count
                FROM refund 
                WHERE thanh_toan_id = ? AND status = 'COMPLETED'";
        
        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            return [
                'total_refunded' => 0.0,
                'refund_count' => 0
            ];
        }
        
        $stmt->bind_param('i', $thanhToanId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return [
            'total_refunded' => (float)($row['total_refunded'] ?? 0),
            'refund_count' => (int)($row['refund_count'] ?? 0)
        ];
    }
}
