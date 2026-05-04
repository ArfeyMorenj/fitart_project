# 🔧 TINKER COMMANDS - PRODUCTION SETUP (Copy & Paste)

**Project**: FitArt JPAS  
**Purpose**: Create admin users and verify production setup  
**Version**: 1.0  

---

## 📌 HOW TO USE THIS FILE

1. **SSH into production server**:
   ```bash
   ssh user@production-server
   cd /path/to/fitart_project
   ```

2. **Open Laravel Tinker**:
   ```bash
   php artisan tinker
   ```

3. **Copy-paste commands SATU PER SATU** (not all at once!)
   - Wait for each command to finish
   - Check the output
   - Copy next command

4. **Exit Tinker**:
   ```bash
   exit
   ```

---

## ✅ SECTION 1: CREATE SUPER ADMIN USER

### Command 1.1: Create Super Admin

```bash
User::create([
    'username' => 'superadmin',
    'name' => 'Super Administrator',
    'email' => 'superadmin@fitart.co.id',
    'password' => Hash::make('Admin@123'),
    'role' => 'superadmin',
    'is_active' => true,
]);
```

**Expected Output**:
```
=> User {
     id: 1,
     username: "superadmin",
     name: "Super Administrator",
     email: "superadmin@fitart.co.id",
     role: "superadmin",
     is_active: true,
   }
```

---

## 👥 SECTION 2: CREATE REQUIRED ROLE USERS

### Command 2.1: Create Finance Admin

```bash
User::create([
    'username' => 'finance_admin',
    'name' => 'Finance Administrator',
    'email' => 'finance.admin@fitart.co.id',
    'password' => Hash::make('Admin@123'),
    'role' => 'finance_admin',
    'is_active' => true,
]);
```

### Command 2.2: Create AR Manager

```bash
User::create([
    'username' => 'ar_manager',
    'name' => 'AR Manager',
    'email' => 'ar.manager@fitart.co.id',
    'password' => Hash::make('Admin@123'),
    'role' => 'ar_manager',
    'is_active' => true,
]);
```

### Command 2.3: Create Sales Lead

```bash
User::create([
    'username' => 'sales_lead',
    'name' => 'Sales Team Lead',
    'email' => 'sales.lead@fitart.co.id',
    'password' => Hash::make('Admin@123'),
    'role' => 'sales_lead',
    'is_active' => true,
]);
```

### Command 2.4: Create Developer User

```bash
User::create([
    'username' => 'developer',
    'name' => 'Developer',
    'email' => 'dev@fitart.co.id',
    'password' => Hash::make('Admin@123'),
    'role' => 'developer',
    'is_active' => true,
]);
```

### Command 2.5: Create Auditor User

```bash
User::create([
    'username' => 'auditor',
    'name' => 'Auditor',
    'email' => 'auditor@fitart.co.id',
    'password' => Hash::make('Admin@123'),
    'role' => 'auditor',
    'is_active' => true,
]);
```

---

## ✔️ SECTION 3: VERIFICATION COMMANDS

### Command 3.1: Count Total Users

```bash
User::count();
```

**Expected**: 6

---

### Command 3.2: List All Users

```bash
User::all();
```

**Expected**: Display 6 users dengan role berbeda

---

### Command 3.3: Verify Super Admin

```bash
User::where('role', 'superadmin')->first();
```

**Expected**:
```
=> User {
     role: "superadmin",
     username: "superadmin",
     email: "superadmin@fitart.co.id",
   }
```

---

### Command 3.4: Verify Finance Admin

```bash
User::where('role', 'finance_admin')->first();
```

**Expected**: User dengan role finance_admin ada

---

### Command 3.5: Verify All Roles Exist

```bash
User::distinct('role')->pluck('role');
```

**Expected Output**:
```
=> Illuminate\Support\Collection {
     all: [
       "superadmin",
       "finance_admin",
       "ar_manager",
       "sales_lead",
       "developer",
       "auditor",
     ],
   }
```

---

### Command 3.6: Verify All Users Active

```bash
User::where('is_active', false)->count();
```

**Expected**: 0 (semua user aktif)

---

### Command 3.7: Count by Role

```bash
User::selectRaw('role, count(*) as total')->groupBy('role')->get();
```

**Expected**: Setiap role ada 1 user

---

## 🔐 SECTION 4: PASSWORD VERIFICATION

### Command 4.1: Test Super Admin Password

```bash
$user = User::where('email', 'superadmin@fitart.co.id')->first();
Hash::check('Admin@123', $user->password);
```

**Expected**: true

---

### Command 4.2: Test Wrong Password

```bash
$user = User::where('email', 'superadmin@fitart.co.id')->first();
Hash::check('WrongPassword', $user->password);
```

**Expected**: false

---

### Command 4.3: Test All User Passwords

```bash
$users = User::all();
$users->each(function($user) {
    echo $user->email . ": " . (Hash::check('Admin@123', $user->password) ? "OK" : "FAIL") . "\n";
});
```

**Expected**: Semua OK (jika semua user punya password Admin@123)

---

## 🗄️ SECTION 5: MASTER DATA VERIFICATION

### Command 5.1: Count Banks

```bash
Bank::count();
```

**Expected**: 10+ banks

---

### Command 5.2: Count Chart of Accounts

```bash
ChartOfAccount::count();
```

**Expected**: 20+

---

### Command 5.3: Verify Revenue Account (5033)

```bash
ChartOfAccount::where('code', '5033')->first();
```

**Expected**: Data account 5033

---

### Command 5.4: Verify Piutang Account (1535)

```bash
ChartOfAccount::where('code', '1535')->first();
```

**Expected**: Data account 1535

---

### Command 5.5: Count Product Groups

```bash
ProductGroup::count();
```

**Expected**: 2+

---

### Command 5.6: Count Products

```bash
Product::count();
```

**Expected**: 5+

---

### Command 5.7: Count Team Members

```bash
TeamMember::count();
```

**Expected**: 3+

---

## 🚨 SECTION 6: TROUBLESHOOTING COMMANDS

### Command 6.1: Delete All Users (If Something Wrong)

```bash
User::truncate();
```

⚠️ **WARNING**: Ini menghapus SEMUA user! Hanya gunakan jika benar-benar perlu restart

---

### Command 6.2: Delete Specific User

```bash
User::where('email', 'superadmin@fitart.co.id')->delete();
```

---

### Command 6.3: Check Database Connection

```bash
DB::connection()->getPdo();
```

**Expected**: Database connected (no error)

---

### Command 6.4: Check Migrations Status

```bash
exit
php artisan migrate:status
```

**Expected**: All migrations listed as "Ran" (✓)

---

### Command 6.5: Verify Model Relationships

```bash
$user = User::first();
$user->refresh();
echo "User: " . $user->username;
```

**Expected**: User data displayed correctly

---

## 📝 SECTION 7: QUICK REFERENCE

### Login Credentials (After Setup):

```
┌─────────────────────────────────────────────┐
│ PRODUCTION USER CREDENTIALS                │
├─────────────────────────────────────────────┤
│                                             │
│ Username: superadmin                        │
│ Email: superadmin@fitart.co.id              │
│ Password: Admin@123                         │
│ Role: superadmin                            │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│ Username: finance_admin                     │
│ Email: finance.admin@fitart.co.id           │
│ Password: Admin@123                         │
│ Role: finance_admin                         │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│ Username: ar_manager                        │
│ Email: ar.manager@fitart.co.id              │
│ Password: Admin@123                         │
│ Role: ar_manager                            │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│ Username: sales_lead                        │
│ Email: sales.lead@fitart.co.id              │
│ Password: Admin@123                         │
│ Role: sales_lead                            │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 🔄 SECTION 8: COMPLETE SETUP IN ONE GO

Jika ingin jalankan SEMUA setup command sekaligus, gunakan ini di tinker:

```bash
# Open tinker
php artisan tinker

# Paste all commands below (sekaligus):
User::create(['username' => 'superadmin', 'name' => 'Super Administrator', 'email' => 'superadmin@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'superadmin', 'is_active' => true,]);
User::create(['username' => 'finance_admin', 'name' => 'Finance Administrator', 'email' => 'finance.admin@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'finance_admin', 'is_active' => true,]);
User::create(['username' => 'ar_manager', 'name' => 'AR Manager', 'email' => 'ar.manager@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'ar_manager', 'is_active' => true,]);
User::create(['username' => 'sales_lead', 'name' => 'Sales Team Lead', 'email' => 'sales.lead@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'sales_lead', 'is_active' => true,]);
User::create(['username' => 'developer', 'name' => 'Developer', 'email' => 'dev@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'developer', 'is_active' => true,]);
User::create(['username' => 'auditor', 'name' => 'Auditor', 'email' => 'auditor@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'auditor', 'is_active' => true,]);

# Verify
User::all();
```

---

## 📞 HELP & SUPPORT

**If you get errors:**

1. **Class not found** → Make sure models are imported
2. **SQLSTATE error** → Check database connection
3. **Timeout** → Tinker timed out, exit and re-run

**Contact**: devops@fitart.co.id

---

**Document Version**: 1.0  
**Last Updated**: April 27, 2026  
**Status**: Ready for Production Use
