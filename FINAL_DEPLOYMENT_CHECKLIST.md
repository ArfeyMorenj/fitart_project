# ✅ FINAL DEPLOYMENT CHECKLIST - BEFORE GO LIVE

**PRINT THIS & TICK ALL BEFORE PRODUCTION!**

---

## 🎯 PRE-DEPLOYMENT CHECKLIST (Do This First!)

```
INFRASTRUCTURE CHECK:
  ☐ Server running (SSH accessible)
  ☐ MySQL/Database running
  ☐ Disk space available (at least 1 GB free)
  ☐ PHP version compatible (7.4+)
  ☐ Composer installed
  ☐ Git available (if pulling from repo)

CODE PREPARATION:
  ☐ Code deployed to server
  ☐ .env file configured (with production DB credentials)
  ☐ APP_KEY generated (php artisan key:generate)
  ☐ APP_ENV = production
  ☐ APP_DEBUG = false
  ☐ Backup of current database done (mysqldump)

PERMISSION CHECK:
  ☐ storage/ folder writable (chmod 755)
  ☐ bootstrap/cache/ folder writable (chmod 755)
  ☐ database/ folder exists and accessible
```

---

## 🔄 DEPLOYMENT EXECUTION CHECKLIST

### Phase 1: Database Migration (10 min)

```
[ ] Run: php artisan migrate --force
    Expected: "Migrated" messages
    ✓ PASS / ✗ FAIL

[ ] Run: php artisan db:seed --force
    Expected: "Seeded successfully" message
    ✓ PASS / ✗ FAIL

[ ] Verify in database:
    Run: php artisan tinker
         Bank::count();  # Should be 10+
    Expected: >= 10
    ✓ PASS / ✗ FAIL
```

### Phase 2: User Creation (5 min)

```
[ ] Open: php artisan tinker

[ ] Run: User::create([...superadmin...])
    Expected: User object returned
    ✓ PASS / ✗ FAIL

[ ] Run: (5 more user creation commands)
    Expected: 6 users total
    ✓ PASS / ✗ FAIL

[ ] Verify: User::count();
    Expected: 6
    Actual: ____
    ✓ PASS / ✗ FAIL

[ ] Exit: exit
```

### Phase 3: Server Start (2 min)

```
[ ] Run: php artisan serve
    Expected: "Server started at http://127.0.0.1:8000"
    ✓ PASS / ✗ FAIL

[ ] Keep terminal open (for testing)
```

### Phase 4: Authentication Testing (10 min)

```
[ ] Open Postman

[ ] Test 1: superadmin login
    POST http://localhost:8000/api/login
    Body: { "email": "superadmin@fitart.co.id", "password": "Admin@123" }
    Expected: Status 200 + token
    ✓ PASS / ✗ FAIL
    Token: ______________________

[ ] Test 2: finance_admin login
    Expected: Status 200 + token
    ✓ PASS / ✗ FAIL

[ ] Test 3: ar_manager login
    Expected: Status 200 + token
    ✓ PASS / ✗ FAIL

[ ] Test 4: Invalid login
    Expected: Status 401
    ✓ PASS / ✗ FAIL
```

### Phase 5: Master Data Verification (10 min)

```
[ ] Test: GET /api/banks
    Auth: Bearer {superadmin_token}
    Expected: 200 + 10+ banks
    ✓ PASS / ✗ FAIL

[ ] Test: GET /api/chart-of-accounts
    Expected: 200 + 20+ accounts
    ✓ PASS / ✗ FAIL

[ ] Test: GET /api/products
    Expected: 200 + 5+ products
    ✓ PASS / ✗ FAIL

[ ] Test: GET /api/team-members
    Expected: 200 + 3+ members
    ✓ PASS / ✗ FAIL
```

### Phase 6: Core Business Logic (20 min)

```
[ ] Test: Create Invoice
    POST /api/invoices
    Expected: 201 + invoice_id
    ✓ PASS / ✗ FAIL
    Invoice ID: ____

[ ] Test: Create Payment
    POST /api/payments
    Expected: 201 + payment_id
    ✓ PASS / ✗ FAIL

[ ] Test: Generate Report
    GET /api/reports/ar-aging
    Expected: 200 + report data
    ✓ PASS / ✗ FAIL
```

### Phase 7: Security Testing (10 min)

```
[ ] Test: No Token
    GET /api/invoices (without Authorization header)
    Expected: 401
    ✓ PASS / ✗ FAIL

[ ] Test: Invalid Token
    GET /api/invoices
    Auth: Bearer invalid-token-xyz
    Expected: 401
    ✓ PASS / ✗ FAIL

[ ] Test: Unauthorized Access
    DELETE /api/users/1
    Auth: Bearer {ar_manager_token}
    Expected: 403
    ✓ PASS / ✗ FAIL

[ ] Test: Valid Token
    GET /api/invoices
    Auth: Bearer {superadmin_token}
    Expected: 200 + data
    ✓ PASS / ✗ FAIL
```

### Phase 8: API Endpoint Coverage (60 min)

```
[ ] Run Postman Collection: FitArt_JPAS_API_Complete.postman_collection.json
    
[ ] Check Results:
    Total Requests: 68
    Passed: ____ / 68
    Failed: ____ / 68
    Success Rate: ____%
    Expected: 100% (or at least 95%)
    ✓ PASS / ✗ FAIL
```

### Phase 9: Final Verification (10 min)

```
[ ] Database size OK
    $ du -h database/
    Expected: < 100 MB

[ ] Error logs empty
    $ tail -f storage/logs/laravel-*.log
    Expected: No critical errors

[ ] Response times acceptable
    All API calls: < 500ms
    ✓ PASS / ✗ FAIL

[ ] All 6 users active
    tinker: User::where('is_active', true)->count()
    Expected: 6
    ✓ PASS / ✗ FAIL
```

---

## 📊 SUMMARY

### Test Results:

```
Phase 1 (Migration): ☐ PASS | ☐ FAIL
Phase 2 (Users): ☐ PASS | ☐ FAIL
Phase 3 (Server): ☐ PASS | ☐ FAIL
Phase 4 (Auth): ☐ PASS | ☐ FAIL
Phase 5 (Master Data): ☐ PASS | ☐ FAIL
Phase 6 (Business Logic): ☐ PASS | ☐ FAIL
Phase 7 (Security): ☐ PASS | ☐ FAIL
Phase 8 (API Coverage): ☐ PASS | ☐ FAIL
Phase 9 (Final Verify): ☐ PASS | ☐ FAIL

TOTAL: ☐ ALL PASS | ☐ SOME FAIL
```

### Decision:

```
All phases PASS?
  ├─ YES → ✅ READY FOR PRODUCTION (Go to section below)
  └─ NO  → ❌ NOT READY (Fix failed tests, re-run)
```

---

## 🚀 IF ALL PASS: GO LIVE STEPS

### Step 1: Update .env for Production

```bash
# Open .env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://fitart.yourdomain.com
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Step 2: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize
```

### Step 3: Enable Production Mode

```bash
# Stop development server
# Ctrl+C in terminal running php artisan serve

# Start production server (or use supervisor/systemd)
# For Apache/Nginx, just ensure DocumentRoot points to public/
```

### Step 4: Notify Team

```
✉️ Send message to team:

Subject: FitArt JPAS Production Deployment Complete ✅

Message:
- Production is now LIVE
- URLs: https://fitart.yourdomain.com
- All systems tested and verified
- Monitoring enabled
- Support team on standby

Contact: devops@fitart.co.id for issues
```

### Step 5: Monitor (24 Hours)

```
[ ] Check application logs every hour
[ ] Monitor error logs
[ ] Track response times
[ ] Monitor database performance
[ ] Verify all features working
[ ] Collect user feedback
```

---

## 🆘 TROUBLESHOOTING QUICK REFERENCE

### Migration Failed
```
❌ Issue: php artisan migrate returns error
✅ Fix: 
   - Check .env DB credentials
   - Ensure MySQL is running
   - Run: php artisan migrate:reset (be careful!)
   - Then: php artisan migrate --force again
```

### Cannot Create Users
```
❌ Issue: Tinker commands timeout
✅ Fix:
   - Make sure you paste ONE command at a time
   - Press Enter after each command
   - Wait for response before next command
   - Check: User::count(); to verify
```

### API Returns 500 Error
```
❌ Issue: Endpoints returning 500 Internal Server Error
✅ Fix:
   - Check: storage/logs/laravel-*.log
   - Check: Database connection working
   - Run: php artisan cache:clear
   - Run: php artisan config:clear
   - Restart server
```

### Token Issues
```
❌ Issue: Login returns token but API rejects it
✅ Fix:
   - Verify APP_KEY in .env
   - Regenerate: php artisan key:generate
   - Check: Authorization header format
   - Format: Authorization: Bearer {token}
```

---

## 📋 SIGN-OFF

**Deployment Executed By**: ___________________  
**Date**: ___________________  
**Time**: ___________________  

**Tests Verified By**: ___________________  
**Date**: ___________________  

**Approved for Production By**: ___________________  
**Date**: ___________________  

**Notes/Issues**:
```
_________________________________________________________________

_________________________________________________________________

_________________________________________________________________
```

---

## 📞 POST-DEPLOYMENT

**After go-live:**

- ☐ Monitor logs for 24 hours
- ☐ Document any issues
- ☐ Collect team feedback
- ☐ Plan next improvements
- ☐ Schedule next maintenance window
- ☐ Archive this checklist

---

**Version**: 1.0  
**Status**: Ready for Production Deployment  
**Print & Use**: Yes  
**Save Checklist**: After deployment (for records)
