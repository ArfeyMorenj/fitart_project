# 📋 TEMPLATE JSON LENGKAP PUT & DELETE ENDPOINTS YANG BELUM DITEST

**Status:** Berdasarkan analisis Postman Collection - SEMUA PUT & DELETE sudah ada template di Collection
**Catatan:** Dokumentasi lengkap dengan validation rules dari controller

---

## 🔴 RINGKASAN BELUM DITEST

Dari 154 total endpoints:
- ✅ 117 dengan saved response (sudah tested)
- ❌ **47 WITHOUT saved response (BELUM TESTED)**

Dari 47 untested:
- 🔴 **15 PUT endpoints** ← Yang akan kita test
- 🔴 **15-20 DELETE endpoints** ← Yang akan kita test
- ⚪ Sisanya: GET, POST, PATCH, dll

---

---

## 📋 PUT ENDPOINTS YANG BELUM DITEST (15 endpoints)

Semuanya sudah ada di Postman Collection dengan struktur template. Berikut detail lengkap dengan body input:

### 1. PUT /api/banks/{bank}
**Controller:** BankController.php - `update()` method

**Validation Rules:**
```
code          : required | unique:banks,code,{id}
name          : required
account_number: nullable | string
account_name  : nullable | string
type          : nullable | in:M,H
acc_code      : nullable | custom validation (chart_of_accounts)
cdf_code      : nullable | custom validation (chart_of_accounts)  
is_active     : nullable | boolean
```

**JSON Body (Copy-Paste ke Postman):**
```json
{
  "code": "BCA",
  "name": "Bank Central Asia",
  "account_number": "1234567890",
  "account_name": "PT FitArt Indonesia",
  "type": "M",
  "acc_code": "1101",
  "cdf_code": "1101",
  "is_active": true
}
```

**Cara Isi:**
1. GET `/api/banks/1` dulu ambil data existing
2. Copy value dari response ke template diatas
3. Ubah salah satu field misalnya `account_number` dengan value baru
4. PUT dengan body diatas

---

### 2. PUT /api/clients/{client}
**Controller:** ClientController.php - `update()` method

**Validation Rules:**
```
code             : required | unique:clients,code,{id}
status           : nullable
name             : required
address          : nullable
city             : nullable
phone            : nullable
fax              : nullable
npwp             : nullable
npkp             : nullable
tax_name         : nullable
tax_address      : nullable
credit_term_days : nullable | integer
is_active        : nullable | boolean
```

**JSON Body:**
```json
{
  "code": "CLT001",
  "status": "active",
  "name": "PT Klien Besar",
  "address": "Jl. Sudirman No. 100",
  "city": "Jakarta Pusat",
  "phone": "021-1234567",
  "fax": "021-7654321",
  "npwp": "12.345.678.9-012.345",
  "npkp": "12.345.678.901.234",
  "tax_name": "PT Klien Besar",
  "tax_address": "Jl. Gatot Subroto No. 50",
  "credit_term_days": 30,
  "is_active": true
}
```

---

### 3. PUT /api/company/{company}
**Controller:** CompanyController.php - `update()` method

**Validation Rules:** (22 fields - WAJIB LENGKAP)
```
code                    : required | unique:companies,code,{id}
name                    : required
address                 : nullable
address_invoice         : nullable
city                    : nullable
city_invoice            : nullable
phone                   : nullable
fax                     : nullable
email                   : nullable | email
website                 : nullable
npwp                    : nullable
npkp                    : nullable
tax_name                : nullable
tax_position            : nullable
invoice_name            : nullable
invoice_position        : nullable
invoice_name_2          : nullable
invoice_position_2      : nullable
invoice_tolerance_days  : nullable | integer
upgrade_days            : nullable | integer
letterhead_top          : nullable | string
letterhead_bottom       : nullable | string
```

**JSON Body:**
```json
{
  "code": "CP001",
  "name": "PT FitArt Indonesia",
  "address": "Jl. Gajah Mada No. 1",
  "address_invoice": "Jl. Sudirman No. 100",
  "city": "Jakarta Pusat",
  "city_invoice": "Jakarta Selatan",
  "phone": "021-5000000",
  "fax": "021-5000001",
  "email": "info@fitart.id",
  "website": "https://fitart.id",
  "npwp": "01.234.567.8-901.234",
  "npkp": "12.345.678.901.234",
  "tax_name": "PT FitArt Indonesia",
  "tax_position": "Direktur Utama",
  "invoice_name": "Ricky Pratama",
  "invoice_position": "Direktur",
  "invoice_name_2": "Eka Putri",
  "invoice_position_2": "Manajer Keuangan",
  "invoice_tolerance_days": 5,
  "upgrade_days": 10,
  "letterhead_top": "Header Company - PT FitArt Indonesia",
  "letterhead_bottom": "Footer Company - Jakarta 2024"
}
```

---

### 4. PUT /api/invoices/{invoice}
**Controller:** InvoiceController.php - `update()` method  
**Role Required:** finance_admin atau super_admin  

**Validation Rules:** (35 fields - LENGKAP!)
```
number                  : required | unique:invoices,number,{id}
invoice_type_id         : required | exists:invoice_types,id
client_id               : required | exists:clients,id
bank_id                 : nullable | exists:banks,id
work_order_id           : nullable | exists:work_orders,id
period                  : nullable | string | size:7 (format: MM/YYYY)
date                    : required | date
due_date                : nullable | date
tax_date                : nullable | date
tax_number              : nullable | string
tax_note                : nullable | string
invoice_note            : nullable | string
client_address          : nullable | string
description             : nullable | string
invoice_category        : nullable | string
invoice_mode            : nullable | string | in:NORMAL,DP,PELUNASAN_DP
tax_type                : nullable | string
instance                : nullable | string
invoice_bm_km           : nullable | string | max:50
invoice_bm_km_date      : nullable | date
bruto                   : nullable | numeric
discount                : nullable | numeric
dpp                     : nullable | numeric
ppn                     : nullable | numeric
ppn_percentage          : nullable | numeric
dp                      : nullable | numeric
other                   : nullable | numeric
total                   : nullable | numeric
include_ppn             : nullable | boolean
use_old_letterhead      : nullable | boolean
without_payment_posting : nullable | boolean
stamp_and_signature     : nullable | boolean
auto_journal            : nullable | boolean
pass_protelasi          : nullable | boolean (only super_admin)
is_paid                 : nullable | boolean
```

**⚠️ Business Rule:** Jika invoice sudah `is_posted=true`, hanya super_admin yang bisa update

**JSON Body:**
```json
{
  "number": "INV/2024/001",
  "invoice_type_id": 1,
  "client_id": 1,
  "bank_id": 1,
  "work_order_id": 1,
  "period": "02/2024",
  "date": "2024-02-01",
  "due_date": "2024-03-01",
  "tax_date": "2024-02-01",
  "tax_number": "000000000000000",
  "tax_note": "Note for tax",
  "invoice_note": "Important note",
  "client_address": "Jl. Sudirman No. 100",
  "description": "Implementasi Project",
  "invoice_category": "SERVICE",
  "invoice_mode": "NORMAL",
  "tax_type": "PPN",
  "instance": "INSTANCE 1",
  "invoice_bm_km": "BM-001",
  "invoice_bm_km_date": "2024-02-01",
  "bruto": 100000000,
  "discount": 10000000,
  "dpp": 90000000,
  "ppn": 9000000,
  "ppn_percentage": 10,
  "dp": 0,
  "other": 0,
  "total": 99000000,
  "include_ppn": true,
  "use_old_letterhead": false,
  "without_payment_posting": false,
  "stamp_and_signature": true,
  "auto_journal": true,
  "pass_protelasi": false,
  "is_paid": false
}
```

---

### 5-15. PUT ENDPOINTS LAINNYA

Sisa 10 PUT endpoints dengan struktur sama seperti di file TEMPLATE_JSON_PUT_ENDPOINTS_LENGKAP.md:

| # | Endpoint | Reference |
|----|----------|-----------|
| 5 | PUT /api/items/{item} | Section 5 di template file |
| 6 | PUT /api/master-item-products/{master} | Section 6 di template file |
| 7 | PUT /api/product-groups/{group} | Section 7 di template file |
| 8 | PUT /api/products/{product} | Section 8 di template file |
| 9 | PUT /api/users/{user} | Section 9 di template file |
| 10 | PUT /api/team-members/{member} | Section 10 di template file |
| 11 | PUT /api/installations/{installation} | Section 11 di template file |
| 12 | PUT /api/licenses/{license} | Section 12 di template file |
| 13 | PUT /api/work-orders/{order} | Section 13 di template file |
| 14 | PUT /api/payments/{payment} | Section 14 di template file |
| 15 | PUT /api/invoice-items/{item} | Section 15 di template file |

---

---

## 🗑️ DELETE ENDPOINTS YANG BELUM DITEST (15-20 endpoints)

Semua DELETE endpoints memiliki struktur sama - **EMPTY BODY**, cukup kirim DELETE request ke endpoint dengan parameter ID.

### Resource Delete Pattern (Sama untuk semua):

```
DELETE {{base_url}}/api/{resource}/{id}
```

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Body:** (kosong/empty)
```json

```

---

### Detail 15-20 DELETE Endpoints:

| # | Endpoint | Controller | Notes |
|---|----------|-----------|-------|
| 1 | DELETE /api/banks/{id} | BankController@destroy | Soft delete |
| 2 | DELETE /api/clients/{id} | ClientController@destroy | Check jika ada invoices |
| 3 | DELETE /api/company/{id} | CompanyController@destroy | Cek dependencies |
| 4 | DELETE /api/invoice-types/{id} | InvoiceTypeController@destroy | **Check:** Cannot delete jika ada invoices |
| 5 | DELETE /api/items/{id} | ItemController@destroy | Soft delete |
| 6 | DELETE /api/master-item-products/{id} | MasterItemProductController@destroy | Check usage |
| 7 | DELETE /api/product-groups/{id} | ProductGroupController@destroy | Auth: super_admin, sales_operator |
| 8 | DELETE /api/products/{id} | ProductController@destroy | Soft delete |
| 9 | DELETE /api/users/{id} | UserController@destroy | Hanya self atau super_admin |
| 10 | DELETE /api/team-members/{id} | TeamMemberController@destroy | Check jika digunakan di tim |
| 11 | DELETE /api/invoices/{id} | InvoiceController@destroy | **Check:** Cannot delete jika is_posted atau is_paid (except super_admin) |
| 12 | DELETE /api/installations/{id} | InstallationController@destroy | Check relasi |
| 13 | DELETE /api/licenses/{id} | LicenseController@destroy | Cek invoices terkait |
| 14 | DELETE /api/payments/{id} | PaymentController@destroy | **Check:** Cannot delete jika is_posted |
| 15 | DELETE /api/invoice-items/{id} | InvoiceItemController@destroy | **Check:** Cannot delete jika invoice is_posted |
| 16 | DELETE /api/work-orders/{id} | WorkOrderController@destroy | Check status |
| 17 | DELETE /api/stop-licenses/{id} | StopLicenseController@destroy | Cek dependencies |
| 18 | DELETE /api/debit-credit-notes/{id} | DebitCreditNoteController@destroy | Cek posted status |
| 19 | DELETE /api/posting-payments/{id}  | PostingPaymentController@destroy | Check posted status |
| 20 | DELETE /api/tax-series/{id} | InvoiceSeriesController@destroy | Cek usage |

---

## 🎯 TESTING STEPS UNTUK DELETE:

1. **Pastikan data existence:**
   ```
   GET /api/{resource}/{id}
   ```
   Harus return 200 OK dengan data valid

2. **Check business rules:**
   - invoice-types: Jangan ada invoices terkait
   - invoices: Belum posted dan belum paid (atau user=super_admin)
   - payments: Belum posted
   - invoice-items: Invoice belum di-post
   - products: Tidak dipakai di mana-mana
   - clients: Tidak ada invoices
   - dll (lihat tabel di atas)

3. **Kirim DELETE:**
   ```
   DELETE /api/{resource}/{id}
   Headers: Authorization: Bearer {token}
   Body: (empty)
   ```

4. **Verify deleted:**
   ```
   GET /api/{resource}/{id}
   ```
   Harus return 404 atau empty (soft delete)

---

## ⚠️ CRITICAL DELETE CASES (WAJIB TEST):

### 1. DELETE /api/invoice-types/2
**Status:** Akan ERROR karena ada invoice terkait (INV ID 3)
```
Response: 422
{
  "success": false,
  "message": "Cannot delete invoice type that has invoices"
}
```
**Fix:** Delete invoice ID 3 dulu, baru delete invoice-type

### 2. DELETE /api/invoices/3 (untuk unlock invoice-type)
**Status:** OK (not posted, not paid)
```
DELETE /api/invoices/3
Response: 200 OK
{
  "success": true,
  "message": "Invoice deleted successfully"
}
```

### 3. DELETE /api/payments/{id}
**Requirement:** Hanya jika payment belum di-post atau user=super_admin
```
⚠️ Check is_posted = false sebelum delete
```

---

## 📊 SUMMARY

**Total PUT + DELETE yang belum ditest: ~30 endpoints**

- ✅ **15 PUT endpoints** - Ada template lengkap di file TEMPLATE_JSON_PUT_ENDPOINTS_LENGKAP.md
- ✅ **15-20 DELETE endpoints** - Gunakan struktur di atas, body kosong

**Next Action:**
1. Test 1 PUT endpoint dengan body dari template
2. Test 1 DELETE endpoint 
3. Catat response di Postman
4. Repeat untuk 28 endpoint lainnya

Semuanya sudah ada di Postman Collection, tinggal dijalankan dan catat hasilnya! 🚀
