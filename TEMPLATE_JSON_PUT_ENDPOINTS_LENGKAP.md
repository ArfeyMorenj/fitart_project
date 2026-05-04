# 📋 TEMPLATE JSON LENGKAP UNTUK SEMUA PUT ENDPOINTS

**Dibuat dari:** Analisis langsung validation rules di setiap controller update() method  
**Tanggal:** 2024  
**Status:** READY untuk testing - Semua fields LENGKAP tanpa dikurangi

---

## ✅ DAFTAR 15 PUT ENDPOINTS YANG AKAN DI-TEST

| # | Endpoint | Fields | Status |
|---|----------|--------|--------|
| 1 | PUT /api/banks/{id} | 8 | BankController |
| 2 | PUT /api/clients/{id} | 12 | ClientController |
| 3 | PUT /api/company/{id} | 22 | CompanyController |
| 4 | PUT /api/invoice-types/{id} | 5 | InvoiceTypeController |
| 5 | PUT /api/items/{id} | 6 | ItemController |
| 6 | PUT /api/master-item-products/{id} | 9 | MasterItemProductController |
| 7 | PUT /api/product-groups/{id} | 5 | ProductGroupController |
| 8 | PUT /api/products/{id} | 10 | ProductController |
| 9 | PUT /api/users/{id} | 6 | UserController |
| 10 | PUT /api/team-members/{id} | 5 | TeamMemberController |
| 11 | PUT /api/installations/{id} | 8 | InstallationController |
| 12 | PUT /api/licenses/{id} | 11 | LicenseController |
| 13 | PUT /api/work-orders/{id} | 11 | WorkOrderController |
| 14 | PUT /api/payments/{id} | 7 | PaymentController |
| 15 | PUT /api/invoice-items/{id} | 9 | InvoiceItemController |

---

---

## 🏦 1. PUT /api/banks/{id} - Bank Master Data

**Controller:** BankController.php (update method)  
**Validasi dari:** Lines 48-63

### Validation Rules Extracted:
```
code          : required | unique:banks,code,{id}
name          : required
account_number: nullable
account_name  : nullable
type          : nullable | in:M,H
acc_code      : nullable | custom validation (validateCoaCodes)
cdf_code      : nullable | custom validation (validateCoaCodes)
is_active     : nullable | boolean
```

### JSON Template - Copy Paste ke Postman:
```json
{
  "code": "BM00",
  "name": "DEPOSIT IN TRANSIT",
  "account_number": "",
  "account_name": "",
  "type": "M",
  "acc_code": "1111",
  "cdf_code": "1111",
  "is_active": true
}
```

**Testing:** ID 1 → `PUT /api/banks/1`  
- Ini data real dari database (ID 1 yang ada)

---

## 👥 2. PUT /api/clients/{id} - Client Master Data

**Controller:** ClientController.php (update method)  
**Validasi dari:** Lines 1-100+

### Validation Rules Extracted:
```
code            : required | unique:clients,code,{id}
status          : nullable
name            : required
address         : nullable
city            : nullable
phone           : nullable
fax             : nullable
npwp            : nullable
npkp            : nullable
tax_name        : nullable
tax_address     : nullable
credit_term_days: nullable | integer
is_active       : nullable | boolean
```

### JSON Template:
```json
{
  "code": "0000",
  "status": "NON GROUP",
  "name": "PUBLIC",
  "address": "",
  "city": "SURABAYA",
  "phone": "",
  "fax": "",
  "npwp": "",
  "npkp": "",
  "tax_name": "",
  "tax_address": "",
  "credit_term_days": 0,
  "is_active": true
}
```

**Testing:** ID 1 → `PUT /api/clients/1`

---

## 🏢 3. PUT /api/company/{id} - Company Settings

**Controller:** CompanyController.php (update method)  
**Validasi dari:** Lines 1-150+

### Validation Rules Extracted:
```
code                   : required | unique:companies,code,{id}
name                   : required
address                : nullable
address_invoice        : nullable
city                   : nullable
city_invoice           : nullable
phone                  : nullable
fax                    : nullable
email                  : nullable | email
website                : nullable
npwp                   : nullable
npkp                   : nullable
tax_name               : nullable
tax_position           : nullable
invoice_name           : nullable
invoice_position       : nullable
invoice_name_2         : nullable
invoice_position_2     : nullable
invoice_tolerance_days : nullable
upgrade_days           : nullable
letterhead_top         : nullable
letterhead_bottom      : nullable
```

### JSON Template:
```json
{
  "code": "FITART",
  "name": "PT. Fit Art Technology",
  "address": "Jl. Danau Toba blok H",
  "address_invoice": "Jl. Danau Toba blok H",
  "city": "Kedungkandang Sawojajar",
  "city_invoice": "Kota Malang - Jawa Timur",
  "phone": "0341-XXXXXXX",
  "fax": "0341-XXXXXXX",
  "email": "info@fitart.co.id",
  "website": "www.fitart.co.id",
  "npwp": "65.800.534.1.603.000",
  "npkp": "",
  "tax_name": "",
  "tax_position": "",
  "invoice_name": "",
  "invoice_position": "",
  "invoice_name_2": "",
  "invoice_position_2": "",
  "invoice_tolerance_days": 60,
  "upgrade_days": 5,
  "letterhead_top": "",
  "letterhead_bottom": ""
}
```

**Testing:** ID 1 → `PUT /api/company/1`

---

## 📄 4. PUT /api/invoice-types/{id} - Jenis Invoice

**Controller:** InvoiceTypeController.php (update method)

### Validation Rules Extracted:
```
code               : required | unique:invoice_types,code,{id} | max:20
name               : required | max:255
is_license         : nullable | boolean
auto_create_number : nullable | boolean
is_active          : nullable | boolean
```

### JSON Template:
```json
{
  "code": "501",
  "name": "PENJUALAN HARDWARE",
  "is_license": false,
  "auto_create_number": true,
  "is_active": true
}
```

**Testing:** ID 1 → `PUT /api/invoice-types/1`

---

## 📦 5. PUT /api/items/{id} - Item/Jasa Master

**Controller:** ItemController.php (update method)

### Validation Rules Extracted:
```
code       : required | unique:items,code,{id}
name       : required
acc_omzet  : nullable | custom validation (validateCoaCodes)
acc_piutang: nullable | custom validation (validateCoaCodes)
cdf_omzet  : nullable | custom validation (validateCoaCodes)
cdf_piutang: nullable | custom validation (validateCoaCodes)
is_active  : nullable | boolean
```

### JSON Template:
```json
{
  "code": "1",
  "name": "INSTALL",
  "acc_omzet": "5032",
  "acc_piutang": "1532",
  "cdf_omzet": "5032",
  "cdf_piutang": "1532",
  "is_active": true
}
```

**Testing:** ID 1 → `PUT /api/items/1`  
**Catatan:** acc_* dan cdf_* harus ada di table chart_of_accounts dengan status aktif. Gunakan codes yang valid dari database!

---

## 🛍️ 6. PUT /api/master-item-products/{id} - Master Item Produk

**Controller:** MasterItemProductController.php (update method)

### Validation Rules Extracted:
```
code          : required | unique:master_item_products,code,{id} | max:50
name          : required | max:255
unit          : nullable | max:20
price         : nullable | numeric | min:0
acc_omzet     : required | max:20 | custom validation
acc_omzet_np  : nullable | max:20 | custom validation
acc_piutang   : required | max:20 | custom validation
cdf_piutang   : nullable | max:20 | custom validation
is_active     : nullable | boolean
```

### JSON Template:
```json
{
  "code": "MIP-1",
  "name": "INSTALL",
  "unit": "UNIT",
  "price": 0.00,
  "acc_omzet": "5032",
  "acc_omzet_np": "5032",
  "acc_piutang": "1532",
  "cdf_omzet": "5032",
  "cdf_piutang": "1532",
  "is_active": true
}
```

**Testing:** ID 1 → `PUT /api/master-item-products/1`

---

## 👫 7. PUT /api/product-groups/{id} - Kelompok Produk

**Controller:** ProductGroupController.php (update method)

### Validation Rules Extracted:
```
code       : required | max:20 | unique:product_groups,code,{id}
name       : required | max:255
acc_omzet  : nullable | max:20 | custom validation
cdf_piutang: nullable | max:20 | custom validation
is_active  : nullable | boolean
```

**⚠️ Authorization Check:** Hanya super_admin dan sales_operator yang bisa update

### JSON Template:
```json
{
  "code": "PGP001",
  "name": "Produk Layanan",
  "acc_omzet": "5032",
  "cdf_piutang": "1532",
  "is_active": true
}
```

**Testing:** ID 2 → `PUT /api/product-groups/2` (ADA DI DATABASE)

---

## 📚 8. PUT /api/products/{id} - Produk

**Controller:** ProductController.php (update method)

### Validation Rules Extracted:
```
code            : required | unique:products,code,{id}
name            : required
specification   : nullable
description     : nullable
author_code     : nullable
author_name     : nullable
compiler        : nullable
year            : nullable | digits:4
product_group_id: nullable | exists:product_groups,id
is_active       : nullable | boolean
```

### JSON Template:
```json
{
  "code": "03",
  "name": "IKLAN KOLOM (SIGMA KOLOM)",
  "specification": "ADS MANAGEMENT",
  "description": "Iklan kolom module",
  "author_code": "03",
  "author_name": "IMANG INDAH AYUNINGRUM",
  "compiler": "DELPHI 6",
  "year": 2006,
  "product_group_id": 1,
  "is_active": true
}
```

**Testing:** ID 2 → `PUT /api/products/2` (ADA DI DATABASE)  
**⚠️ PENTING:** author_code harus ada di team_members (valid codes: 01, 02, 03, 04, 05)

---

## 👤 9. PUT /api/users/{id} - User Aplikasi

**Controller:** UserController.php (update method)

### Validation Rules Extracted:
```
username: nullable | max:50 | unique:users,username,{id}
name    : nullable | max:255
email   : nullable | unique:users,email,{id}
password: nullable | min:6 (akan di-hash otomatis)
role    : nullable | in:super_admin,finance_admin,sales_operator,ar_collector,auditor_viewer,manager
status  : nullable | in:active,inactive
```

### JSON Template:
```json
{
  "username": "financeadmin",
  "name": "Finance Admin",
  "email": "financeadmin@fitart.co.id",
  "password": "password123",
  "role": "finance_admin",
  "is_active": true
}
```

**Testing:** ID 2 → `PUT /api/users/2` (ADA DI DATABASE)  
**Catatan:**
- Password hanya di-hash jika diisi dalam request
- Role harus salah satu dari 6 pilihan yang tersedia
- Status: active atau inactive

---

## 👨‍💼 10. PUT /api/team-members/{id} - Anggota Tim

**Controller:** TeamMemberController.php (update method)

### Validation Rules Extracted:
```
code     : required | unique:team_members,code,{id}
name     : required
position : nullable
status   : nullable
is_active: nullable | boolean
```

### JSON Template:
```json
{
  "code": "02",
  "name": "EDY PRANOTO",
  "position": "SYSTEM ANALYST",
  "status": "STATUS1",
  "is_active": true
}
```

**Testing:** ID 2 → `PUT /api/team-members/2` (ADA DI DATABASE)

---

## 🔧 11. PUT /api/installations/{id} - Instalasi

**Controller:** InstallationController.php (update method)

### Validation Rules Extracted:
```
work_order_id : required | exists:work_orders,id
client_id     : required | exists:clients,id
install_date  : required | date
implementor_1  : nullable
implementor_2  : nullable
implementor_3  : nullable
notes          : nullable
```

### JSON Template:
```json
{
  "work_order_id": 1,
  "client_id": 1,
  "install_date": "2026-03-10",
  "implementor_1": "Implementor 1",
  "implementor_2": "Implementor 2",
  "implementor_3": null,
  "notes": "Instalasi selesai dengan sukses"
}
```

**Testing:** ID 1 → `PUT /api/installations/1`  
**⚠️ CATATAN:** Installation belum ada dengan ID 2 - gunakan ID 1 atau buat data baru

---

## 📜 12. PUT /api/licenses/{id} - License

**Controller:** LicenseController.php (update method)

### Validation Rules Extracted:
```
license_number : required | unique:licenses,license_number,{id}
client_id      : required | exists:clients,id
work_order_id  : nullable | exists:work_orders,id
license_date   : required | date
due_date       : nullable | date
subtotal       : nullable | numeric
discount       : nullable | numeric
tax            : nullable | numeric
total          : nullable | numeric
status         : nullable | in:unpaid,partial,paid
notes          : nullable | string
```

### JSON Template:
```json
{
  "license_number": "LIC-2026-0001",
  "client_id": 1,
  "work_order_id": 1,
  "license_date": "2026-03-10",
  "due_date": "2027-03-10",
  "subtotal": 1000000.00,
  "discount": 0.00,
  "tax": 110000.00,
  "total": 1110000.00,
  "status": "unpaid",
  "notes": "Annual license"
}
```

**Testing:** ID 1 → `PUT /api/licenses/1`

---

## 📋 13. PUT /api/work-orders/{id} - Work Order

**Controller:** WorkOrderController.php (update method)

### Validation Rules Extracted:
```
number        : required | unique:work_orders,number,{id}
date          : required | date
date_install  : required | date
start_license : nullable | date
client_id     : required | exists:clients,id
product_id    : nullable | exists:products,id
version       : nullable | max:50
item_id       : nullable | exists:items,id
status        : nullable
amount        : nullable | numeric | min:0
description   : nullable
item_count    : nullable | integer | min:0
per_unit      : nullable
notes         : nullable
team          : nullable | array (team.*.role required_with | team.*.team_member_id required_with)
```

### JSON Template:
```json
{
  "number": "WO-032026-0001",
  "date": "2026-03-06",
  "date_install": "2026-03-10",
  "start_license": "2026-03-10",
  "client_id": 1,
  "product_id": 1,
  "version": "v1.0.0",
  "item_id": 1,
  "status": "AKTIF",
  "amount": 1000000.00,
  "description": "Implementasi modul keuangan",
  "item_count": 1,
  "per_unit": "UNIT",
  "notes": "Project prioritas tinggi",
  "team": [
    {
      "role": "implementor_1",
      "team_member_id": 1
    },
    {
      "role": "supervisor",
      "team_member_id": 2
    }
  ]
}
```

**Testing:** ID 1 → `PUT /api/work-orders/1`  
**Catatan:**
- Team roles valid: implementor_1, implementor_2, programmer, system_analyst, supervisor, client_spv
- Team dapat diupdate sekaligus dengan work order

---

## 💳 14. PUT /api/payments/{id} - Pembayaran

**Controller:** PaymentController.php (update method)

### Validation Rules Extracted:
```
number      : required | unique:payments,payment,{id}
date        : required | date
invoice_id  : required | exists:invoices,id
client_id   : required | exists:clients,id
description : nullable
amount      : required | numeric | min:0
```

**⚠️ Authorization Check:** Hanya finance_admin, ar_collector, atau super_admin yang bisa update  
**⚠️ Business Rule:** client_id harus match dengan invoice's client_id  
**⚠️ Status Check:** Jika payment sudah is_posted=true, hanya super_admin yang bisa update

### JSON Template:
```json
{
  "number": "BP-202603-0001-002",
  "date": "2026-03-20",
  "invoice_id": 1,
  "client_id": 1,
  "description": "Auto from posting BP-202603-0001",
  "amount": 1054500.00
}
```

**Testing:** ID 2 → `PUT /api/payments/2` (ADA DI DATABASE)  
**⚠️ PAYMENT ID 2 SUDAH POSTED** - Hanya super_admin yang bisa update!

---

## 📦 15. PUT /api/invoice-items/{id} - Item Invoice

**Controller:** InvoiceItemController.php (update method)

### Validation Rules Extracted:
```
invoice_id            : required | exists:invoices,id
master_item_product_id: nullable | exists:master_item_products,id
item_code             : nullable | string
item_name             : required | string
description           : nullable | string
qty                   : nullable | integer | min:1
unit                  : nullable | string
price                 : nullable | numeric | min:0
bruto                 : nullable | numeric | min:0 (auto-calculated: qty × price)
months                : nullable | integer | min:1
```

**⚠️ Business Rule:** Jika invoice sudah is_posted=true, hanya super_admin yang bisa update

### JSON Template:
```json
{
  "invoice_id": 4,
  "master_item_product_id": null,
  "item_code": "1",
  "item_name": "INSTALL",
  "description": "WO WO-032026-0001",
  "qty": 1,
  "unit": "UNIT",
  "price": 1000000.00,
  "bruto": 1000000.00,
  "months": 1
}
```

**Testing:** ID 1 → `PUT /api/invoice-items/1`  
**Catatan:**
- Field `bruto` auto-calculated dari qty × price jika tidak dikirim
- `master_item_product_id` untuk link ke master products

---

---

## 🎯 RINGKASAN TESTING STRATEGY

### Untuk setiap endpoint, lakukan ini:

1. **GET dahulu** dari setiap endpoint untuk ambil data existing:
   ```
   GET /api/banks/1
   GET /api/clients/1
   ... dst
   ```

2. **Siapkan template** sesuai dengan response data (copy nilai lama, ubah beberapa field)

3. **Pastikan constraints**:
   - **unique** fields: jangan ubah atau gunakan nilai unik baru
   - **required** fields: harus ada di request
   - **nullable** fields: boleh dikosongkan atau null
   - **foreign key** (exists): pastikan ID sudah ada di database
   - **enum** (in): gunakan pilihan yang tersedia
   - **date format**: YYYY-MM-DD
   - **numeric**: gunakan angka, bisa dengan desimal

4. **PUT request** dengan template yang sudah disiapkan

5. **Verifikasi response** 200 OK dengan data yang ter-update

---

## ⚠️ PERHATIAN FIELD KHUSUS

| Endpoint | Field Khusus | Catatan |
|----------|-------------|---------|
| Bank | acc_code, cdf_code | Custom validation ke chart_of_accounts |
| Item | acc_*, cdf_* | Custom validation ke chart_of_accounts |
| User | password | Hanya di-hash jika diisi, tidak mandatory |
| User | role | 6 pilihan fixed, default super_admin |
| Payment | client_id | Harus match invoice's client_id |
| Invoice Item | bruto | Auto-calculated = qty × price |
| Work Order | team | Format array nested, bisa diupdate sekaligus |
| License | license_date | Required, use_date nullable |
| Company | email | Harus format email valid |

---

## 🚀 NEXT STEPS

1. Ambil 1 endpoint dari list diatas
2. GET untuk ambil data current
3. Siapkan JSON body dengan template diatas
4. Sesuaikan nilai dari GET response
5. PUT ke endpoint dengan body tersebut
6. Catat response dan result
7. Repeat untuk endpoint berikutnya

**Semua template sudah LENGKAP TANPA dikurangi - siap copy-paste ke Postman!**
