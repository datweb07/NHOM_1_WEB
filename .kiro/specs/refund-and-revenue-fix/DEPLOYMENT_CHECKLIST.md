# Deployment Checklist: Refund and Revenue Fix

## Pre-Deployment Preparation

### 1. Code Review
- [ ] All code changes reviewed and approved
- [ ] No hardcoded credentials or sensitive data
- [ ] Error handling implemented for all scenarios
- [ ] Transaction logging in place
- [ ] Code follows project coding standards

### 2. Testing Verification
- [ ] All manual tests completed (see MANUAL_TESTING_GUIDE.md)
- [ ] Refund workflow tested with VNPay sandbox
- [ ] Revenue calculation verified with test data
- [ ] Performance tests passed
- [ ] Edge cases tested (COD, already refunded, etc.)

### 3. Database Preparation
- [ ] Database migration script prepared
- [ ] Migration tested on staging database
- [ ] Backup plan in place
- [ ] Rollback script prepared

### 4. Configuration Check
- [ ] VNPay credentials configured in production `.env`
- [ ] Gateway endpoints verified (production vs sandbox)
- [ ] Redis connection configured (if used)
- [ ] Email notifications configured (if applicable)

### 5. Documentation
- [ ] Admin user guide updated with refund workflow
- [ ] Technical documentation completed
- [ ] API documentation updated (if applicable)
- [ ] Deployment notes prepared

## Deployment Steps

### Step 1: Backup Current System

**Time Estimate:** 10 minutes

```bash
# Backup database
mysqldump -u [username] -p [database_name] > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup application files
tar -czf app_backup_$(date +%Y%m%d_%H%M%S).tar.gz /path/to/app

# Verify backups
ls -lh backup_*.sql
ls -lh app_backup_*.tar.gz
```

**Verification:**
- [ ] Database backup created successfully
- [ ] Application backup created successfully
- [ ] Backup files are accessible and not corrupted

---

### Step 2: Enable Maintenance Mode

**Time Estimate:** 2 minutes

```bash
# Create maintenance mode flag
touch /path/to/app/maintenance.flag

# Or use application-specific maintenance mode
php artisan down  # If using Laravel-style commands
```

**Verification:**
- [ ] Maintenance page displayed to users
- [ ] Admin access still available (if needed)

---

### Step 3: Run Database Migration

**Time Estimate:** 5 minutes

```sql
-- Add admin_id column to refund table
ALTER TABLE `refund` 
ADD COLUMN `admin_id` INT DEFAULT NULL AFTER `completed_at`,
ADD CONSTRAINT `fk_refund_admin` FOREIGN KEY (`admin_id`) 
    REFERENCES `nguoi_dung`(`id`) ON DELETE SET NULL;

-- Create indexes for performance
CREATE INDEX idx_thanh_toan_duyet ON thanh_toan(trang_thai_duyet);
CREATE INDEX idx_refund_thanh_toan ON refund(thanh_toan_id, status);
CREATE INDEX idx_don_hang_revenue ON don_hang(trang_thai, ngay_tao);

-- Verify migration
DESCRIBE refund;
SHOW INDEX FROM thanh_toan WHERE Key_name = 'idx_thanh_toan_duyet';
SHOW INDEX FROM refund WHERE Key_name = 'idx_refund_thanh_toan';
SHOW INDEX FROM don_hang WHERE Key_name = 'idx_don_hang_revenue';
```

**Verification:**
- [ ] `admin_id` column added to `refund` table
- [ ] Foreign key constraint created successfully
- [ ] All indexes created successfully
- [ ] No errors in migration log

---

### Step 4: Deploy Application Code

**Time Estimate:** 10 minutes

```bash
# Pull latest code from repository
cd /path/to/app
git pull origin main

# Or upload files via FTP/SFTP
# Upload modified files:
# - app/services/refund/RefundService.php
# - app/controllers/admin/ThanhToanController.php
# - app/controllers/admin/DashboardController.php
# - app/models/entities/Refund.php
# - app/views/admin/thanh_toan/detail.php
# - app/routes/admin/admin.php

# Set correct permissions
chmod -R 755 /path/to/app
chown -R www-data:www-data /path/to/app

# Clear application cache (if applicable)
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

**Verification:**
- [ ] All new files deployed successfully
- [ ] File permissions set correctly
- [ ] No syntax errors in PHP files
- [ ] Application cache cleared

---

### Step 5: Verify Configuration

**Time Estimate:** 5 minutes

```bash
# Check .env file has required settings
grep "VNPAY_" /path/to/app/.env
grep "MOMO_" /path/to/app/.env

# Verify file structure
ls -la app/services/refund/
ls -la app/controllers/admin/
ls -la app/views/admin/thanh_toan/
```

**Verification:**
- [ ] VNPay credentials present in `.env`
- [ ] All required files exist
- [ ] No missing dependencies

---

### Step 6: Test Critical Paths

**Time Estimate:** 15 minutes

**Test 1: Admin Login**
- [ ] Can login to admin panel
- [ ] Dashboard loads without errors

**Test 2: Payment Detail Page**
- [ ] Can view payment detail page
- [ ] Refund button displays correctly
- [ ] No JavaScript errors in console

**Test 3: Refund Workflow (Dry Run)**
- [ ] Can open refund modal
- [ ] Form validation works
- [ ] (Optional) Process test refund with small amount

**Test 4: Dashboard Revenue**
- [ ] Dashboard loads successfully
- [ ] Revenue displays correctly
- [ ] No database errors

---

### Step 7: Disable Maintenance Mode

**Time Estimate:** 2 minutes

```bash
# Remove maintenance mode flag
rm /path/to/app/maintenance.flag

# Or use application-specific command
php artisan up
```

**Verification:**
- [ ] Application accessible to users
- [ ] No maintenance page displayed

---

### Step 8: Monitor Application

**Time Estimate:** 30 minutes (ongoing)

```bash
# Monitor application logs
tail -f /path/to/app/storage/logs/laravel.log

# Monitor web server logs
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log

# Monitor database slow queries
mysql -u root -p -e "SHOW FULL PROCESSLIST;"
```

**Monitoring Checklist:**
- [ ] No PHP errors in application logs
- [ ] No 500 errors in web server logs
- [ ] Database queries performing well
- [ ] No unusual traffic patterns

---

## Post-Deployment Verification

### Functional Testing

**Test 1: Refund Button Visibility**
- [ ] Approved VNPay payment shows refund button
- [ ] COD payment hides refund button
- [ ] Already refunded payment hides button

**Test 2: Process Real Refund**
- [ ] Select a test payment (small amount)
- [ ] Process refund through admin panel
- [ ] Verify refund appears in history
- [ ] Check transaction log entry created

**Test 3: Revenue Calculation**
- [ ] Dashboard shows correct revenue
- [ ] Revenue excludes refunded payments
- [ ] Revenue excludes unapproved payments

### Performance Verification

```sql
-- Check query performance
EXPLAIN SELECT SUM(dh.tong_thanh_toan) as total_revenue
FROM don_hang dh
INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id
LEFT JOIN refund r ON tt.id = r.thanh_toan_id AND r.status = 'COMPLETED'
WHERE tt.trang_thai_duyet = 'THANH_CONG'
  AND dh.trang_thai NOT IN ('DA_HUY', 'TRA_HANG')
  AND r.id IS NULL;
```

**Performance Checklist:**
- [ ] Revenue query executes in < 500ms
- [ ] Indexes are being used (check EXPLAIN output)
- [ ] No full table scans on large tables
- [ ] Dashboard loads in < 2 seconds

### Data Integrity Check

```sql
-- Verify refund records
SELECT COUNT(*) FROM refund WHERE admin_id IS NULL;

-- Check for orphaned refunds
SELECT r.* FROM refund r
LEFT JOIN thanh_toan tt ON r.thanh_toan_id = tt.id
WHERE tt.id IS NULL;

-- Verify transaction logs
SELECT COUNT(*) FROM transaction_log 
WHERE action_type LIKE '%REFUND%' 
AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

**Data Integrity Checklist:**
- [ ] No orphaned refund records
- [ ] All new refunds have admin_id
- [ ] Transaction logs are being created
- [ ] No data corruption detected

---

## Rollback Procedure

If critical issues are discovered, follow this rollback procedure:

### Step 1: Enable Maintenance Mode

```bash
touch /path/to/app/maintenance.flag
```

### Step 2: Restore Database

```sql
-- Rollback migration (if needed)
ALTER TABLE `refund` DROP FOREIGN KEY `fk_refund_admin`;
ALTER TABLE `refund` DROP COLUMN `admin_id`;

DROP INDEX idx_thanh_toan_duyet ON thanh_toan;
DROP INDEX idx_refund_thanh_toan ON refund;
DROP INDEX idx_don_hang_revenue ON don_hang;

-- Or restore from backup
mysql -u [username] -p [database_name] < backup_[timestamp].sql
```

### Step 3: Restore Application Code

```bash
# Restore from backup
cd /path/to/app
tar -xzf app_backup_[timestamp].tar.gz

# Or revert git commit
git revert [commit_hash]
git push origin main
```

### Step 4: Clear Cache and Restart

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Restart web server
sudo systemctl restart nginx
# or
sudo systemctl restart apache2
```

### Step 5: Verify Rollback

- [ ] Application loads without errors
- [ ] Old functionality still works
- [ ] No refund-related UI elements visible

### Step 6: Disable Maintenance Mode

```bash
rm /path/to/app/maintenance.flag
```

---

## Communication Plan

### Before Deployment
- [ ] Notify team of deployment schedule
- [ ] Inform stakeholders of expected downtime
- [ ] Prepare support team for potential issues

### During Deployment
- [ ] Update status page (if applicable)
- [ ] Keep team informed of progress
- [ ] Document any issues encountered

### After Deployment
- [ ] Announce successful deployment
- [ ] Share post-deployment test results
- [ ] Provide admin team with refund workflow guide

---

## Success Criteria

Deployment is considered successful when:

- [ ] All deployment steps completed without errors
- [ ] All post-deployment tests passed
- [ ] No critical errors in logs (30 min monitoring)
- [ ] Refund workflow functional with VNPay
- [ ] Dashboard revenue calculation correct
- [ ] Performance metrics within acceptable range
- [ ] No user-reported issues (24 hours)

---

## Emergency Contacts

**Technical Lead:** [Name] - [Phone] - [Email]  
**Database Admin:** [Name] - [Phone] - [Email]  
**DevOps:** [Name] - [Phone] - [Email]  
**Product Owner:** [Name] - [Phone] - [Email]

---

## Notes

- Deployment window: [Date/Time]
- Expected downtime: 15-30 minutes
- Rollback decision point: 30 minutes after deployment
- Full monitoring period: 24 hours

## Deployment Sign-off

**Deployed by:** _________________ Date: _________  
**Verified by:** _________________ Date: _________  
**Approved by:** _________________ Date: _________
