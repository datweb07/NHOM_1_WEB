<?php

require_once dirname(__DIR__) . '/core/EnvSetup.php';
require_once dirname(__DIR__) . '/models/entities/TransactionLog.php';

class LogCleanupScript
{
    private TransactionLog $transactionLog;
    private int $retentionMonths = 12;

    public function __construct()
    {
        $this->transactionLog = new TransactionLog();
    }

    public function execute(): array
    {
        $startTime = microtime(true);
        
        echo "Starting transaction log cleanup...\n";
        echo "Retention period: {$this->retentionMonths} months\n";
        
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$this->retentionMonths} months"));
        
        echo "Deleting logs older than: {$cutoffDate}\n";
        
        $countSql = "SELECT COUNT(*) as total FROM transaction_log 
                     WHERE created_at < '{$cutoffDate}'";
        $countResult = $this->transactionLog->query($countSql);
        $totalToDelete = $countResult[0]['total'] ?? 0;
        
        echo "Found {$totalToDelete} logs to delete\n";
        
        if ($totalToDelete == 0) {
            echo "No logs to delete. Cleanup complete.\n";
            return [
                'success' => true,
                'deleted_count' => 0,
                'execution_time' => microtime(true) - $startTime
            ];
        }
        
        $deleteSql = "DELETE FROM transaction_log WHERE created_at < '{$cutoffDate}'";
        
        try {
            $this->transactionLog->query($deleteSql);
            
            $elapsedTime = microtime(true) - $startTime;
            
            echo "Successfully deleted {$totalToDelete} logs\n";
            echo "Execution time: " . round($elapsedTime, 2) . " seconds\n";

            error_log(sprintf(
                "[LOG CLEANUP] Deleted %d transaction logs older than %s. Execution time: %.2f seconds",
                $totalToDelete,
                $cutoffDate,
                $elapsedTime
            ));
            
            return [
                'success' => true,
                'deleted_count' => $totalToDelete,
                'execution_time' => $elapsedTime,
                'cutoff_date' => $cutoffDate
            ];
            
        } catch (Exception $e) {
            echo "Error during cleanup: " . $e->getMessage() . "\n";
            
            error_log(sprintf(
                "[LOG CLEANUP ERROR] Failed to delete old logs: %s",
                $e->getMessage()
            ));
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'execution_time' => microtime(true) - $startTime
            ];
        }
    }

    public function getStatistics(): array
    {
        $totalSql = "SELECT COUNT(*) as total FROM transaction_log";
        $totalResult = $this->transactionLog->query($totalSql);
        $total = $totalResult[0]['total'] ?? 0;
        
        $cutoffDate = date('Y-m-d', strtotime("-{$this->retentionMonths} months"));
        $oldSql = "SELECT COUNT(*) as total FROM transaction_log WHERE created_at < '{$cutoffDate}'";
        $oldResult = $this->transactionLog->query($oldSql);
        $old = $oldResult[0]['total'] ?? 0;
        
        $recentSql = "SELECT COUNT(*) as total FROM transaction_log WHERE created_at >= '{$cutoffDate}'";
        $recentResult = $this->transactionLog->query($recentSql);
        $recent = $recentResult[0]['total'] ?? 0;
        
        return [
            'total_logs' => $total,
            'logs_older_than_retention' => $old,
            'logs_within_retention' => $recent,
            'retention_months' => $this->retentionMonths,
            'cutoff_date' => $cutoffDate
        ];
    }
}

if (php_sapi_name() === 'cli') {
    $cleanup = new LogCleanupScript();

    echo "\n=== Log Statistics (Before Cleanup) ===\n";
    $stats = $cleanup->getStatistics();
    foreach ($stats as $key => $value) {
        echo ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
    }
    echo "\n";
    
    $result = $cleanup->execute();
    
    if ($result['success']) {
        echo "\n=== Log Statistics (After Cleanup) ===\n";
        $stats = $cleanup->getStatistics();
        foreach ($stats as $key => $value) {
            echo ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
        }
    }
    
    echo "\n";
    exit($result['success'] ? 0 : 1);
}

?>
