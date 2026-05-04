# ⚡ QUICK START - PRODUCTION DEPLOYMENT (5 Min Summary)

**Untuk yang terburu-buru: Ikuti langkah ini SAJA**

---

## 🚀 STEP-BY-STEP (Copy-Paste Ready)

### STEP 1: Database Migration (2 min)

```bash
cd /path/to/fitart_project
php artisan migrate --force
php artisan db:seed --force
```

✅ **Harus PASS** → Lihat "Migrated successfully" message

---

### STEP 2: Create Admin Users (2 min)

Buka terminal baru, jalankan:

```bash
php artisan tinker
```

Copy-paste semua ini:

```bash
User::create(['username' => 'superadmin', 'name' => 'Super Administrator', 'email' => 'superadmin@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'superadmin', 'is_active' => true,]);
User::create(['username' => 'finance_admin', 'name' => 'Finance Administrator', 'email' => 'finance.admin@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'finance_admin', 'is_active' => true,]);
User::create(['username' => 'ar_manager', 'name' => 'AR Manager', 'email' => 'ar.manager@fitart.co.id', 'password' => Hash::make('Admin@123'), 'role' => 'ar_manager', 'is_active' => true,]);
User::all();
```

✅ **Harus PASS** → Lihat 3+ user terbuat

Exit tinker:
```bash
exit
```

---

### STEP 3: Start Server (1 min)

```bash
php artisan serve
```

✅ **Harus PASS** → Lihat "Laravel development server started"

---

### STEP 4: Test Login (5 min)

Buka Postman atau cURL, test:

```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "superadmin@fitart.co.id",
  "password": "Admin@123"
}
```

✅ **HARUS dapat response berisi TOKEN**

---

### STEP 5: Test Master Data (5 min)

```bash
GET http://localhost:8000/api/banks
Authorization: Bearer {TOKEN_FROM_STEP_4}
```

✅ **HARUS dapat daftar 10+ bank**

---

### STEP 6: Full Testing (60-120 min)

Buka file: **TESTING_CHECKLIST_PRODUCTION.md**  
Ikuti semua phase secara urut  
Centang setiap test yang PASS

✅ **Semua harus PASS sebelum go live**

---

## 🎯 Checklist Sebelum Production

```
✅ Database migrated
✅ Admin users dibuat (6 user dengan 6 role berbeda)
✅ Super Admin bisa login
✅ Token generation bekerja
✅ Bank list ada 10+
✅ Chart of Accounts lengkap
✅ Create Invoice berhasil
✅ Create Payment berhasil
✅ Semua API endpoint tested
```

---

## 🆘 Jika Ada ERROR

### Error: "No such file or directory"
```bash
# Pastikan di folder project yang benar
cd /path/to/fitart_project
pwd  # Check path
ls -la  # Check artisan ada
```

### Error: "Connection refused"
```bash
# Pastikan MySQL running
# Windows: Start XAMPP atau Service MySQL
# Linux: sudo service mysql start
```

### Error: "SQLSTATE error"
```bash
# Cek .env DB config
cat .env | grep DB_
# Pastikan credentials benar
```

### Error: "Class not found"
```bash
# Di tinker, import model dulu
use App\Models\User;
use Illuminate\Support\Facades\Hash;
```

---

## 📊 User Login Credentials

```
Super Admin:
  Email: superadmin@fitart.co.id
  Password: Admin@123

Finance Admin:
  Email: finance.admin@fitart.co.id
  Password: Admin@123

AR Manager:
  Email: ar.manager@fitart.co.id
  Password: Admin@123

Sales Lead:
  Email: sales.lead@fitart.co.id
  Password: Admin@123

Developer:
  Email: dev@fitart.co.id
  Password: Admin@123

Auditor:
  Email: auditor@fitart.co.id
  Password: Admin@123
```

---

## 📞 Next Steps

Setelah semua pass:

1. ✅ Baca **PRODUCTION_DEPLOYMENT_GUIDE.md** untuk details lengkap
2. ✅ Gunakan **TESTING_CHECKLIST_PRODUCTION.md** untuk verifikasi complete
3. ✅ Keep **TINKER_COMMANDS_PRODUCTION.md** untuk reference
4. ✅ Backup database sebelum go live
5. ✅ Notify team bahwa production siap

---

**Status**: ✅ Ready to Deploy  
**Time**: 5-10 min setup + 1-2 jam testing  
**Version**: 1.0
