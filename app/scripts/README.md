# Payment System Scripts

This directory contains maintenance scripts for the payment system.

## Transaction Log Cleanup Script

### Purpose

The `cleanup_old_logs.php` script deletes transaction logs older than 12 months to comply with the log retention policy (Requirement 13.6).

### Usage

#### Manual Execution

Run the script manually from the command line:

```bash
php app/scripts/cleanup_old_logs.php
```

The script will:
1. Display current log statistics
2. Delete logs older than 12 months
3. Display updated statistics
4. Log the cleanup action to error logs

#### Scheduled Execution (Cron Job)

To automate log cleanup, add a cron job to run the script weekly:

```bash
# Edit crontab
crontab -e

# Add this line to run every Sunday at 2:00 AM
0 2 * * 0 /usr/bin/php /path/to/your/project/app/scripts/cleanup_old_logs.php >> /path/to/logs/cleanup.log 2>&1
```

**Recommended Schedule:**
- **Weekly**: `0 2 * * 0` (Every Sunday at 2:00 AM)
- **Monthly**: `0 2 1 * *` (First day of month at 2:00 AM)

### Output

The script provides detailed output:

```
=== Log Statistics (Before Cleanup) ===
Total logs: 15234
Logs older than retention: 3421
Logs within retention: 11813
Retention months: 12
Cutoff date: 2023-12-15

Starting transaction log cleanup...
Retention period: 12 months
Deleting logs older than: 2023-12-15 00:00:00
Found 3421 logs to delete
Successfully deleted 3421 logs
Execution time: 1.23 seconds

=== Log Statistics (After Cleanup) ===
Total logs: 11813
Logs older than retention: 0
Logs within retention: 11813
Retention months: 12
Cutoff date: 2023-12-15
```

### Configuration

The retention period is set to 12 months by default. To change it, modify the `$retentionMonths` property in the `LogCleanupScript` class:

```php
private int $retentionMonths = 12; // Change this value
```

### Error Handling

- If no logs need deletion, the script exits successfully
- Database errors are caught and logged
- Exit codes: 0 = success, 1 = failure

### Logging

All cleanup actions are logged to the system error log with the format:

```
[LOG CLEANUP] Deleted 3421 transaction logs older than 2023-12-15 00:00:00. Execution time: 1.23 seconds
```

### Security Considerations

- The script only deletes from the `transaction_log` table
- No sensitive payment data is exposed in logs
- The script requires database access via the application's configuration
- Run with appropriate user permissions (not as root)

### Monitoring

Monitor cleanup execution by:
1. Checking cron job logs: `/var/log/cron` or custom log file
2. Reviewing application error logs for cleanup entries
3. Periodically checking database size and log counts

### Troubleshooting

**Script doesn't delete any logs:**
- Verify the cutoff date calculation
- Check if logs exist older than 12 months
- Ensure database connection is working

**Permission errors:**
- Ensure the script has execute permissions: `chmod +x cleanup_old_logs.php`
- Verify the cron user has access to the project directory

**Database errors:**
- Check database credentials in `.env`
- Verify the `transaction_log` table exists
- Ensure the database user has DELETE permissions
