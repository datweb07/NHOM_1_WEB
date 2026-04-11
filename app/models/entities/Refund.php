<?php

require_once dirname(__DIR__) . '/BaseModel.php';

class Refund extends BaseModel
{
    public function __construct()
    {
        parent::__construct('refund');
    }

    public function createRefund(int $thanhToanId, float $amount, string $reason)
    {
        $sql = "INSERT INTO refund (thanh_toan_id, amount, status, reason, created_at) 
                VALUES (?, ?, 'PENDING', ?, NOW())";
        
        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param('ids', $thanhToanId, $amount, $reason);
        
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
}
