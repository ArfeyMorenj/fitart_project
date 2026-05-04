# 🚀 PRODUCTION DEPLOYMENT & TESTING STRATEGY - PANDUAN LENGKAP

**Author**: Production Deployment Team  
**Updated**: April 2026  
**Project**: FitArt JPAS System  
**Status**: ✅ Ready to Deploy

---

## 📋 EXECUTIVE SUMMARY

Ketika akan **UP PRODUCTION**, ada 3 hal yang HARUS dilakukan:

1. **Database Migration** - Otomatis jalankan semua schema
2. **Core Data Setup** - Siapkan data master yang wajib ada
3. **Testing Checklist** - Verifikasi semua fungsi bekerja dengan urutan tertentu
4. **Role & Permission Registration** - Setup user dengan role yang tepat

---

## 🎯 FASE 1: PRE-DEPLOYMENT PREPARATION (BEFORE GOING LIVE)

### 1.1 Cek Environment Configuration

```bash
# SSH ke production server
ssh user@production-server

# Navigate ke project folder
cd /path/to/fitart_project

# Backup current database (SANGAT PENTING!)
mysqldump -u root -p fitart_project > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 1.2 Update .env untuk Production

```env
# Change these in production .env file:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://fitart.yourdomain.com

DB_HOST=production-db-server
DB_DATABASE=fitart_project_prod
DB_USERNAME=prod_user
DB_PASSWORD=secure_password_here

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

# Security
ENFORCE_HTTPS=true
```

### 1.3 Generate APP_KEY (Jika belum ada)

```bash
php artisan key:generate --show
# Copy hasilnya ke .env APP_KEY
```

---

## 🔄 FASE 2: DATABASE MIGRATION & CORE DATA SETUP

### 2.1 Jalankan Migration (Otomatis)

```bash
# Jalankan SEMUA migration sekaligus
php artisan migrate --force

# Atau jika ada rollback, gunakan:
php artisan migrate:fresh --force  # HATI-HATI: Hapus semua data lama!
```

**Output yang diharapkan:**
```
Migrating: 2024_01_01_000001_create_banks_table
Migrated:  2024_01_01_000001_create_banks_table
Migrating: 2024_01_01_000002_create_chart_of_accounts_table
Migrated:  2024_01_01_000002_create_chart_of_accounts_table
... (semua table termigrasi)
```

### 2.2 Setup Core Data (WAJIB Ada Sebelum Testing)

Jalankan seeding untuk data master yang essential:

```bash
# Option 1: Gunakan DatabaseSeeder yang sudah ada
php artisan db:seed

# Option 2: Jalankan seeder spesifik
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ChartOfAccountsSeeder
php artisan db:seed --class=ProductGroupSeeder
php artisan db:seed --class=TeamMemberSeeder
php artisan db:seed --class=BankSeeder
```

**Data Core Yang Harus Ada:**

| Table | Jumlah Min | Keterangan | Status |
|-------|-----------|-----------|--------|
| **Banks** | 5-10 | Daftar bank umum (BCA, Mandiri, BRI, dll) | ✅ |
| **Chart of Accounts** | 20+ | 5033=Revenue, 1535=Piutang, dll | ✅ |
| **Users** (Admin) | 1-2 | superadmin, finance admin | ⏳ Perlu dibuat |
| **Team Members** | 3-5 | Untuk sales/collector | ✅ |
| **Product Groups** | 2-3 | LSA, GPA, dll | ✅ |
| **Products** | 5-10 | IKLAN KOLOM, services | ✅ |
| **License Types** | 3-5 | License type setup | ⏳ Verifikasi |
| **Invoice Types** | 3-5 | Invoice, Credit Note, Debit Note | ⏳ Perlu dibuat |

---

## 👥 FASE 3: USER REGISTRATION & ROLE CHECKLIST

### 3.1 Register First Super Admin

**URUTAN HARUS INI:**

```bash
php artisan tinker

# 1. Cek apakah sudah ada user
User::all();  # Jika kosong, lanjut ke step 2

# 2. Buat Super Admin pertama kali
User::create([
    'username' => 'superadmin',
    'name' => 'Super Administrator',
    'email' => 'admin@fitart.co.id',
    'password' => Hash::make('SecurePassword123!'),
    'role' => 'superadmin',
    'is_active' => true,
]);

# 3. Verifikasi user terbuat
User::where('role', 'superadmin')->first();
```

**Output yang diharapkan:**
```
=> User {
     id: 1,
     username: "superadmin",
     email: "admin@fitart.co.id",
     role: "superadmin",
     is_active: 1,
   }
```

### 3.2 User Role Checklist - Yang Harus Dibuat

**Tabel User Setup untuk Production:**

```
┌─────────────────────────────────────────────────────────┐
│ USER REGISTRATION CHECKLIST - PHASE 1 (PRODUCTION INIT) │
└─────────────────────────────────────────────────────────┘

✅ MANDATORY USERS (HARUS ADA):

1. SUPER ADMIN
   ├─ Username: superadmin
   ├─ Email: superadmin@fitart.co.id
   ├─ Role: superadmin
   ├─ Permission: Full Access (All features)
   └─ Status: ACTIVE

2. FINANCE ADMIN
   ├─ Username: finance_admin
   ├─ Email: finance.admin@fitart.co.id
   ├─ Role: finance_admin
   ├─ Permission: Chart of Accounts, Invoices, Payments, Reports
   └─ Status: ACTIVE

3. AR MANAGER / COLLECTOR
   ├─ Username: ar_manager
   ├─ Email: ar.manager@fitart.co.id
   ├─ Role: ar_manager
   ├─ Permission: View Invoices, Create Payments, View Receivables
   └─ Status: ACTIVE

4. SALES TEAM LEAD (Optional - untuk produksi real)
   ├─ Username: sales_lead
   ├─ Email: sales.lead@fitart.co.id
   ├─ Role: sales_lead
   ├─ Permission: Create Invoices, View Products
   └─ Status: ACTIVE


✅ OPTIONAL USERS (Untuk testing lebih lengkap):

5. Programmer/Developer
   ├─ Username: developer
   ├─ Email: dev@fitart.co.id
   ├─ Role: developer
   ├─ Permission: Access to API docs, testing endpoints
   └─ Status: ACTIVE

6. Audit/Analyst
   ├─ Username: auditor
   ├─ Email: auditor@fitart.co.id
   ├─ Role: auditor
   ├─ Permission: Read-only for all reports
   └─ Status: ACTIVE
```

### 3.3 Create All Required Users (Tinker Commands)

```bash
php artisan tinker

# SUPER ADMIN
User::create(['username' => 'superadmin', 'name' => 'Super Admin', 'email' => 'superadmin@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'superadmin', 'is_active' => true]);

# FINANCE ADMIN
User::create(['username' => 'finance_admin', 'name' => 'Finance Admin', 'email' => 'finance.admin@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'finance_admin', 'is_active' => true]);

# AR MANAGER
User::create(['username' => 'ar_manager', 'name' => 'AR Manager', 'email' => 'ar.manager@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'ar_manager', 'is_active' => true]);

# SALES LEAD
User::create(['username' => 'sales_lead', 'name' => 'Sales Lead', 'email' => 'sales.lead@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'sales_lead', 'is_active' => true]);

# DEVELOPER
User::create(['username' => 'developer', 'name' => 'Developer', 'email' => 'dev@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'developer', 'is_active' => true]);

# AUDITOR
User::create(['username' => 'auditor', 'name' => 'Auditor', 'email' => 'auditor@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'auditor', 'is_active' => true]);

# Verifikasi semua user dibuat
User::all();
```

### 3.4 Verify User Creation

```bash
php artisan tinker

# Count total users
User::count();  # Harus 6

# Check specific role
User::where('role', 'superadmin')->first();
User::where('role', 'finance_admin')->first();
User::where('role', 'ar_manager')->first();

# Check all active users
User::where('is_active', true)->get();

# Check if all required roles exist
$roles = User::distinct('role')->pluck('role');
$roles->contains('superadmin');    // true
$roles->contains('finance_admin');  // true
$roles->contains('ar_manager');     // true
```

---

## 🧪 FASE 4: TESTING URUTAN - Dari Awal Sampai Siap Production

### 4.1 Testing Urutan (HARUS IKUTI URUTAN INI!)

```
PHASE 1: AUTHENTICATION & CORE SETUP (30 menit)
    ├─ Step 1: Login dengan SuperAdmin
    ├─ Step 2: Verify semua user role terbuat
    ├─ Step 3: Test switch user untuk setiap role
    └─ Step 4: Verify permissions sesuai role

PHASE 2: MASTER DATA VERIFICATION (45 menit)
    ├─ Step 5: Verify Bank list ada 10+ data
    ├─ Step 6: Verify Chart of Accounts lengkap
    ├─ Step 7: Verify Product Groups terbuat
    ├─ Step 8: Verify Products terbuat
    ├─ Step 9: Verify Team Members terbuat
    └─ Step 10: Verify Invoice Types exist

PHASE 3: CORE BUSINESS LOGIC (60 menit)
    ├─ Step 11: Create New Invoice (+ Invoice Items)
    ├─ Step 12: Create Payment for Invoice
    ├─ Step 13: Generate Debit/Credit Notes
    ├─ Step 14: Post to General Ledger / Journal
    ├─ Step 15: Verify General Ledger Balance
    └─ Step 16: Test Reports generation

PHASE 4: API ENDPOINT TESTING (120 menit - Dari Postman)
    ├─ GET endpoints (6 endpoint)
    ├─ POST endpoints (15 endpoint)
    ├─ PUT endpoints (15 endpoint)
    ├─ DELETE endpoints (22 endpoint)
    └─ Special endpoints (10 endpoint)

PHASE 5: SECURITY & PERFORMANCE (30 menit)
    ├─ Step 21: Test invalid login attempts
    ├─ Step 22: Test unauthorized access
    ├─ Step 23: Check database logs
    ├─ Step 24: Verify response times < 500ms
    └─ Step 25: Check error handling

TOTAL TIME: ~4-5 JAM untuk full testing cycle
```

### 4.2 PHASE 1: Authentication & Core Setup Testing

#### Test 1: Login dengan SuperAdmin

```bash
# In Postman or cURL:
POST /api/login
Content-Type: application/json

{
  "email": "superadmin@fitart.co.id",
  "password": "Admin@123"
}

# Expected Response:
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "superadmin",
      "email": "superadmin@fitart.co.id",
      "role": "superadmin"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

✅ **PASS**: Token diterima  
❌ **FAIL**: Error message atau token invalid

#### Test 2: Verify semua user role terbuat

```bash
php artisan tinker

# Get all users by role
$superadmins = User::where('role', 'superadmin')->get();
$finance = User::where('role', 'finance_admin')->get();
$ar = User::where('role', 'ar_manager')->get();

# Expected: Each role should have 1 user
echo "Superadmin: " . $superadmins->count();  // 1
echo "Finance: " . $finance->count();        // 1
echo "AR Manager: " . $ar->count();          // 1
```

✅ **PASS**: Semua role ada  
❌ **FAIL**: Ada role yang missing

#### Test 3: Test Login untuk Setiap Role

```bash
# Buat file test_login_all_roles.sh atau jalankan di Postman

# Login Finance Admin
POST /api/login
{
  "email": "finance.admin@fitart.co.id",
  "password": "Admin@123"
}

# Login AR Manager
POST /api/login
{
  "email": "ar.manager@fitart.co.id",
  "password": "Admin@123"
}

# Test setiap role, semua harus return token yang valid
```

✅ **PASS**: Semua role bisa login dengan token valid  
❌ **FAIL**: Ada role yang gagal login

### 4.3 PHASE 2: Master Data Verification

Setiap test di phase ini HARUS PASS sebelum lanjut ke Phase 3.

#### Test 5: Verify Bank List

```bash
GET /api/banks
Authorization: Bearer {superadmin_token}

# Expected Response:
{
  "status": "success",
  "data": [
    {"id": 1, "name": "BCA", "code": "014"},
    {"id": 2, "name": "Mandiri", "code": "008"},
    ... (minimal 10 bank)
  ],
  "total": 10
}
```

✅ **PASS**: Ada minimal 10 bank  
❌ **FAIL**: Bank kurang dari 10 atau endpoint error

#### Test 6: Verify Chart of Accounts

```bash
GET /api/chart-of-accounts
Authorization: Bearer {superadmin_token}

# Expected: Minimal ada:
# 5033 = Revenue/Omzet
# 1535 = Account Receivable/Piutang
# 2101 = Account Payable (jika ada)
```

✅ **PASS**: Ada account 5033 dan 1535  
❌ **FAIL**: Ada account yang missing

#### Test 7-9: Verify Master Data

```bash
# Jalankan di Postman atau tinker:

php artisan tinker

# Test 7: Product Groups
ProductGroup::count();  # Harus >= 2

# Test 8: Products
Product::count();  # Harus >= 5

# Test 9: Team Members
TeamMember::count();  # Harus >= 3
```

✅ **PASS**: Semua master data ada  
❌ **FAIL**: Ada yang kurang

### 4.4 PHASE 3: Core Business Logic Testing

#### Test 11: Create New Invoice

```bash
POST /api/invoices
Authorization: Bearer {finance_admin_token}
Content-Type: application/json

{
  "invoice_number": "INV-2024-001",
  "invoice_date": "2024-04-27",
  "due_date": "2024-05-27",
  "client_id": 1,
  "product_group_id": 1,
  "amount": 1000000,
  "tax": 100000,
  "total": 1100000,
  "status": "draft"
}

# Expected Response:
{
  "status": "success",
  "message": "Invoice berhasil dibuat",
  "data": {
    "id": 1,
    "invoice_number": "INV-2024-001",
    "total": 1100000,
    "status": "draft"
  }
}
```

✅ **PASS**: Invoice terbuat dengan ID dan status valid  
❌ **FAIL**: Validation error atau database error

#### Test 12: Create Payment

```bash
POST /api/payments
Authorization: Bearer {finance_admin_token}

{
  "invoice_id": 1,
  "payment_date": "2024-04-27",
  "amount": 1100000,
  "payment_method": "bank_transfer",
  "bank_id": 1,
  "notes": "Pembayaran penuh invoice INV-2024-001"
}

# Expected: Payment terbuat dan status invoice berubah ke "paid"
```

✅ **PASS**: Payment dibuat dan invoice status = paid  
❌ **FAIL**: Payment gagal atau status tidak berubah

#### Test 13-15: Advanced Business Logic

```bash
# Test 13: Debit/Credit Notes
# Test 14: Post to General Ledger
# Test 15: Generate Reports

# Semua harus berfungsi tanpa error
```

---

## 📊 FASE 5: API ENDPOINT TESTING (Dari Postman)

Gunakan file **Postman Collection**: `FitArt_JPAS_API_Complete.postman_collection.json`

### Testing Checklist untuk Setiap Endpoint

**Format untuk setiap endpoint:**

```
[ ] Endpoint Name
    [ ] Status Code = 200/201 (expected)
    [ ] Response format valid JSON
    [ ] No database errors
    [ ] Authorization working
    [ ] Data validation working
```

### Quick Testing Steps di Postman

```
1. Import Collection: FitArt_JPAS_API_Complete.postman_collection.json
2. Set Environment: FitArt_JPAS_Local.postman_environment.json
3. Authentication tab:
   - Masukkan token dari login superadmin
4. Run Collection:
   - Postman > Runner
   - Select Collection
   - Select Environment
   - Klik RUN
5. Review Results:
   - Check semua test PASS (hijau)
   - Catat yang FAIL (merah)
```

---

## ✅ PHASE 6: CHECKLIST SEBELUM GO LIVE

```
PRE-PRODUCTION CHECKLIST:

DATABASE & MIGRATION:
  ☐ Database berhasil dimigrate (0 errors)
  ☐ Semua table ada dan struktur benar
  ☐ Core data sudah di-seed (Bank, COA, Products, etc)
  ☐ Backup database dilakukan

AUTHENTICATION & USERS:
  ☐ Super Admin user terbuat dan bisa login
  ☐ Semua required role user terbuat
  ☐ Setiap user bisa login dengan password tepat
  ☐ Token JWT generate dengan benar
  ☐ Role-based access control berfungsi

MASTER DATA:
  ☐ Bank data: 10+ records
  ☐ Chart of Accounts: 20+ records dengan 5033 & 1535
  ☐ Product Groups: 2+ records
  ☐ Products: 5+ records
  ☐ Team Members: 3+ records
  ☐ Invoice Types: semua tipe ada
  ☐ License Types: setup selesai

BUSINESS LOGIC:
  ☐ Invoice Create/Read/Update/Delete berfungsi
  ☐ Payment processing berfungsi
  ☐ General Ledger posting berfungsi
  ☐ Reports generate dengan benar
  ☐ Validation rules berfungsi (tidak bisa create invalid data)

API TESTING:
  ☐ GET endpoints: Semua return 200 + valid data
  ☐ POST endpoints: Semua create dengan valid response
  ☐ PUT endpoints: Semua update dengan valid response
  ☐ DELETE endpoints: Semua delete tanpa orphaned data
  ☐ Authorization header: Semua endpoint require valid token

SECURITY:
  ☐ Invalid login rejected dengan 401
  ☐ Unauthorized access rejected dengan 403
  ☐ SQL Injection attempts blocked
  ☐ CORS configured properly
  ☐ .env not exposed in production
  ☐ APP_DEBUG = false

PERFORMANCE:
  ☐ Average response time < 500ms
  ☐ Slow queries logged dan dioptimasi
  ☐ Database indexes in place
  ☐ Cache configured for production

LOGS & MONITORING:
  ☐ Application logs enabled
  ☐ Error logs monitored
  ☐ Database query logs enabled
  ☐ Backup strategy tested

FINAL VERIFICATION:
  ☐ Full regression test completed
  ☐ All Phase 1-5 tests PASSED
  ☐ Team lead approval received
  ☐ Rollback plan documented
  ☐ Deployment time scheduled during low traffic
```

---

## 🔄 DEPLOYMENT COMMANDS (Production)

### Full Deployment Script

```bash
#!/bin/bash
# deploy_production.sh

set -e  # Exit on any error

echo "=== FitArt Production Deployment ==="

# 1. Backup current database
echo "1. Creating database backup..."
mysqldump -u root -p fitart_project > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Pull latest code (if using git)
echo "2. Pulling latest code..."
git pull origin main

# 3. Install dependencies
echo "3. Installing dependencies..."
composer install --no-dev --optimize-autoloader

# 4. Generate app key (if first time)
echo "4. Generating app key..."
php artisan key:generate

# 5. Run migrations
echo "5. Running database migrations..."
php artisan migrate --force

# 6. Run seeders
echo "6. Running database seeders..."
php artisan db:seed --force

# 7. Clear caches
echo "7. Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 8. Generate optimization files
echo "8. Optimizing for production..."
php artisan optimize

# 9. Set file permissions
echo "9. Setting file permissions..."
chmod -R 755 storage bootstrap/cache

# 10. Done!
echo "=== Deployment Complete ==="
```

### Run it:

```bash
chmod +x deploy_production.sh
./deploy_production.sh
```

---

## 🆘 TROUBLESHOOTING

### Problem: Migration failed
```bash
# Check error logs
tail -f storage/logs/laravel-*.log

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

### Problem: User login gagal
```bash
php artisan tinker

# Check if user exists
User::where('email', 'superadmin@fitart.co.id')->first();

# Check password is correct (test hash)
$user = User::first();
Hash::check('Admin@123', $user->password);  // Should be true
```

### Problem: API endpoint 500 error
```bash
# Check application logs
tail -f storage/logs/laravel-*.log

# Check database logs
tail -f mysql.log

# Enable debug mode temporarily
# Change APP_DEBUG=true in .env
```

---

## 📞 SUPPORT & NEXT STEPS

**Setelah production deployment:**

1. ✅ Monitor logs selama 24 jam pertama
2. ✅ Conduct full user acceptance testing
3. ✅ Enable automated backups
4. ✅ Setup monitoring & alerts
5. ✅ Document access credentials securely
6. ✅ Provide user training

---

**Last Updated**: April 27, 2026  
**Version**: 1.0  
**Status**: Ready for Production
