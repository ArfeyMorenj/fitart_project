# 📋 TEMPLATE JSON USER ENDPOINTS (Belum Ada Saved Response)

**Scan:** UserController + Database users table  
**Database:** 6 users existing (ID 1-6)  
**All endpoints belum ada saved response - siap untuk test**

---

## 📊 USERS DATABASE EXISTING

| ID | Username | Name | Email | Role | Status |
|----|----------|------|-------|------|--------|
| 1 | superadmin | Super Admin | superadmin@fitart.co.id | super_admin | active ✅ |
| 2 | financeadmin | Finance Admin | financeadmin@fitart.co.id | finance_admin | active ✅ |
| 3 | arcollector | AR Collector | arcollector@fitart.co.id | ar_collector | active ✅ |
| 4 | salesoperator | Sales Operator | salesoperator@fitart.co.id | sales_operator | active ✅ |
| 5 | manager | Manager | manager@fitart.co.id | manager | active ✅ |
| 6 | admin | Administrator | admin@fitart.co.id | super_admin | active ✅ |

---

## 1️⃣  POST /api/users - CREATE NEW USER

**Controller:** UserController@store  
**Authorization:** Requires: super_admin  
**Method:** POST  
**No saved response yet** ❌

### Validation Rules:
```
username  : nullable | string | max:50 | unique:users
name      : required | string | max:255
email     : required | email | unique:users
password  : required | string | min:6
role      : required | in:super_admin,finance_admin,sales_operator,ar_collector,auditor_viewer,manager
status    : nullable | in:active,inactive
```

### JSON Template - CREATE NEW USER:
```json
{
  "username": "newuser",
  "name": "New User Name",
  "email": "newuser@fitart.co.id",
  "password": "password123",
  "role": "auditor_viewer",
  "status": "active"
}
```

**Testing:** `POST /api/users`

### Notes:
- ✓ Username optional - auto-generate dari email jika kosong
- ✓ Password harus min 6 char - akan di-hash otomatis
- ✓ Email HARUS unique
- ✓ Role harus salah satu dari 6 pilihan
- ✓ Status: default "active" jika tidak dikirim
- ✓ Response: 201 Created dengan data user baru

---

## 2️⃣  PUT /api/users/{id} - UPDATE USER

**Controller:** UserController@update  
**Authorization:** Requires: super_admin  
**Method:** PUT  
**No saved response yet** ❌

### Validation Rules:
```
username  : nullable | string | max:50 | unique:users,username,{id}
name      : nullable | string | max:255
email     : nullable | email | unique:users,email,{id}
password  : nullable | string | min:6
role      : nullable | in:super_admin,finance_admin,sales_operator,ar_collector,auditor_viewer,manager
status    : nullable | in:active,inactive
```

### JSON Template - UPDATE USER ID 5 (manager):
```json
{
  "name": "Manager Updated",
  "email": "manager.updated@fitart.co.id",
  "password": "newpassword123",
  "role": "manager",
  "status": "active"
}
```

**Testing:** `PUT /api/users/5`

### Alternative Templates:

**Option 1: Change Role Only**
```json
{
  "role": "sales_operator"
}
```

**Option 2: Change Email + Password**
```json
{
  "email": "manager.newemail@fitart.co.id",
  "password": "newpass456"
}
```

**Option 3: Change Status Only**
```json
{
  "status": "inactive"
}
```

### Notes:
- ✓ Semua field optional (nullable)
- ✓ Field yang tidak dikirim tidak akan berubah
- ✓ Password hanya di-hash jika diisi
- ✓ Email harus unique (across other users)
- ✓ Username auto-update dari email jika email berubah
- ✓ Response: 200 OK dengan data user ter-update

---

## 3️⃣  DELETE /api/users/{id} - DELETE USER

**Controller:** UserController@destroy  
**Authorization:** Requires: super_admin  
**Method:** DELETE  
**No body needed**  
**No saved response yet** ❌

### Testing: `DELETE /api/users/6`

**Don't try:**
```
DELETE /api/users/1   ← Cannot delete yourself (403 error if logged in as ID 1)
```

### Notes:
- ✓ Soft delete (user record dikeep, hanya marked deleted)
- ✓ Tidak bisa delete user sendiri (403 error)
- ✓ Response: 200 OK dengan message "User deleted successfully"

---

## 4️⃣  POST /api/users/{id}/assign-role - CHANGE ROLE

**Controller:** UserController@assignRole  
**Authorization:** Requires: super_admin  
**Method:** POST  
**No saved response yet** ❌

### Validation Rules:
```
role : required | in:super_admin,finance_admin,sales_operator,ar_collector,auditor_viewer,manager
```

### JSON Template - ASSIGN ROLE TO USER ID 4:
```json
{
  "role": "finance_admin"
}
```

**Testing:** `POST /api/users/4/assign-role`

### Alternative Templates:

**Option 1: Change to sales_operator**
```json
{
  "role": "sales_operator"
}
```

**Option 2: Change to ar_collector**
```json
{
  "role": "ar_collector"
}
```

**Option 3: Change to auditor_viewer**
```json
{
  "role": "auditor_viewer"
}
```

### Notes:
- ✓ Hanya role field yang dibutuhkan
- ✓ Role WAJIB ada (required)
- ✓ Hanya super_admin yang bisa set role
- ✓ Response: 200 OK dengan data user (role ter-update)

---

## 5️⃣  POST /api/users/{id}/toggle-status - CHANGE ACTIVE/INACTIVE

**Controller:** UserController@toggleStatus  
**Authorization:** Requires: super_admin  
**Method:** POST  
**No body needed**  
**No saved response yet** ❌

### Testing: `POST /api/users/3/toggle-status`

**Don't send any body** - endpoint akan toggle otomatis:
- Jika current status = active → menjadi inactive
- Jika current status = inactive → menjadi active

### How it works:
```
Sebelum: POST /api/users/3/toggle-status
User ID 3 is_active = true

Setelah:
User ID 3 is_active = false (di-toggle)

Call lagi:
POST /api/users/3/toggle-status
User ID 3 is_active = true (di-toggle kembali)
```

### Notes:
- ✓ Tidak perlu body JSON
- ✓ Otomatis toggle active ↔ inactive
- ✓ Response: 200 OK dengan message "User status changed to {new_status}"
- ✓ Data user return dengan is_active value terbaru

---

## 🎯 TESTING CHECKLIST

### POST /api/users (Create):
- [ ] Test dengan template di atas
- [ ] Pastikan email UNIK (tidak ada user dengan email itu)
- [ ] Response: 201 Created
- [ ] Check: User baru bisa login dengan credentials yang dibuat
- [ ] Test fail: Email sudah ada (409 duplicate)
- [ ] Test fail: Password < 6 characters (422 validation)
- [ ] Test fail: Invalid role (422 validation)

### PUT /api/users/{id} (Update):
- [ ] Test dengan ID 5 (manager)
- [ ] Update name + email
- [ ] Response: 200 OK dengan data ter-update
- [ ] Test change password
- [ ] Test change role
- [ ] Test send hanya beberapa field (selective update)
- [ ] Test fail: Email duplicate (422)
- [ ] Test fail: Invalid role (422)

### DELETE /api/users/{id} (Delete):
- [ ] Test DELETE /api/users/6 (admin)
- [ ] Response: 200 OK
- [ ] Test fail: Try delete yourself (403)

### POST /api/users/{id}/assign-role (Assign Role):
- [ ] Test assign manager → finance_admin
- [ ] Response: 200 OK, role changed
- [ ] Test assign sales_operator → ar_collector
- [ ] Response: 200 OK, role changed
- [ ] Test fail: Invalid role (422)

### POST /api/users/{id}/toggle-status (Toggle Status):
- [ ] Test toggle user ID 3
- [ ] Check ID 3 from active → inactive
- [ ] Call toggle again
- [ ] Check ID 3 from inactive → active
- [ ] Response: 200 OK dengan pesan status change

---

## 📋 AUTHORIZATION MATRIX

| Endpoint | Method | Role Required | Notes |
|----------|--------|---------------|-------|
| /users | POST | super_admin | Create new user |
| /users/{id} | PUT | super_admin | Update user |
| /users/{id} | DELETE | super_admin | Delete user, not self |
| /users/{id}/assign-role | POST | super_admin | Change role |
| /users/{id}/toggle-status | POST | super_admin | Toggle active/inactive |

**All require:** AUTH token + super_admin role

---

## ⚠️ VALIDATION ERROR EXAMPLES

**Email not unique:**
```json
{
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

**Invalid role:**
```json
{
  "errors": {
    "role": ["The selected role is invalid."]
  }
}
```

**Password too short:**
```json
{
  "errors": {
    "password": ["The password must be at least 6 characters."]
  }
}
```

**Cannot delete yourself:**
```json
{
  "success": false,
  "message": "Cannot delete your own account"
}
```

---

## 🚀 RECOMMENDED TESTING ORDER

1. **POST /api/users** - Create new user first
   - Use email: `testuser@fitart.co.id`
   - Use role: `auditor_viewer` (role yang belum ada di system)
   - Catat user ID yang di-return

2. **POST /api/users/{new_id}/assign-role** - Test assign role
   - Change role ke `sales_operator`
   - Verify role berubah

3. **PUT /api/users/{new_id}** - Test update
   - Update name + email
   - Verify update work

4. **POST /api/users/{new_id}/toggle-status** - Test toggle
   - Toggle active → inactive
   - Call lagi untuk toggle inactive → active

5. **DELETE /api/users/{new_id}** - Test delete
   - Delete user yang baru dibuat
   - Verify deleted

---

## 📌 KEY POINTS

- ✅ **All endpoints require super_admin role**
- ✅ **6 valid roles:** super_admin, finance_admin, sales_operator, ar_collector, auditor_viewer, manager
- ✅ **POST /users perlu:** name, email, password, role (required)
- ✅ **PUT /users perlu:** any field (optional)
- ✅ **toggleStatus tanpa body** - otomatis toggle
- ✅ **Password auto-hash** - jangan send hashed password
- ✅ **Username auto-generate** - dari email jika tidak disediakan

---

**SEMUA TEMPLATE SUDAH READY UNTUK COPY-PASTE KE POSTMAN! 🚀**
