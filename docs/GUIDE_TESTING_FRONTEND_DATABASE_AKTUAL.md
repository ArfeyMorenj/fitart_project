# GUIDE TESTING FRONTEND FITART - DATABASE AKTUAL

Dokumen ini dibuat dari snapshot database lokal FitArt per 27 April 2026.
Tujuannya supaya testing UI frontend selalu nyambung dengan backend asli, field-nya benar, dan contoh datanya memang ada di database.

## 1. Cara Pakai Guide Ini

Urutan aman:
1. Buka menu frontend yang mau dites.
2. Cek `GET` list dulu.
3. Cocokkan field form dengan response backend.
4. Pakai kode dan ID yang memang ada di database.
5. Submit create/update/delete.
6. Cek request dan response di tab `Network`.
7. Kalau muncul `422`, baca `errors` dari backend.

Aturan penting:
- jangan isi field berdasarkan asumsi
- jangan kirim field yang tidak diminta controller
- untuk relasi, pilih data yang memang sudah ada
- kalau periode terkunci, gunakan periode yang masih terbuka atau login sebagai role yang memang boleh

## 2. Snapshot Database Saat Ini

### Ringkasan Tabel

| Tabel | Count Aktif | Catatan |
|---|---:|---|
| `users` | 6 | Ada `super_admin`, `finance_admin`, `ar_collector`, `sales_operator`, `manager` |
| `team_members` | 5 | Kode aktif: `01`, `03`, `04`, `05`, `06` |
| `product_groups` | 3 | `LSA`, `LSATEST`, `SERVICE` |
| `products` | 2 | `026724`, `05` |
| `chart_of_accounts` | 39 | Banyak kode valid untuk test COA |
| `companies` | 4 | `FITART`, `FITNES`, `COMP01`, `CMP001` |
| `company_settings` | 1 | Setup operasional aktif |
| `banks` | 6 | `BM02`, `BM03`, `BM04`, `KH`, `BM01X`, `BK01` |
| `clients` | 3 | `CLTEST00`, `CLTEST000`, `CL001` |
| `invoice_types` | 5 | `501`, `503`, `504`, `S04`, `500` |
| `items` | 8 | `1`, `3`, `4`, `5`, `6`, `7`, `ITEM122`, `ITMTEST01` |
| `master_item_products` | 9 | `MIP-1` s/d `MIP-7`, `LSA`, `02`, `MIP001` |
| `work_orders` | 1 | Ada `WO-042026-0001` |
| `installations` | 0 | Belum ada data aktif |
| `licenses` | 0 | Belum ada data aktif |
| `stop_licenses` | 0 | Belum ada data aktif |
| `invoices` | 0 | Tidak ada invoice aktif saat snapshot |
| `invoice_items` | 0 | Belum ada detail invoice aktif |
| `payments` | 0 | Belum ada payment aktif |
| `debit_credit_notes` | 0 | Belum ada note aktif |
| `receivables` | 0 | Belum ada receivable aktif |
| `journals` | 5 | Sudah ada posting invoice dan posting payment |
| `protection_periods` | 1 | Periode `2026-03` sudah terkunci |
| `invoice_series` | 1 | Series pajak aktif untuk `03-2026` |
| `posting_payments` | 0 | Belum ada batch posting baru |
| `posting_payment_costs` | 0 | Belum ada cost detail aktif |
| `posting_omzet` | 0 | Belum ada posting omzet baru |

## 3. Data Aman Untuk Testing

### Users yang Bisa Dipakai

- `superadmin@fitart.co.id`
- `financeadmin@fitart.co.id`
- `arcollector@fitart.co.id`
- `salesoperator@fitart.co.id`
- `manager@fitart.co.id`
- `admin@fitart.co.id`

### Team Member Aktif

| ID | Code | Name | Position | Status |
|---|---|---|---|---|
| 1 | `01` | IMAM S ARIFIN | MANAGER AREA | `STATUS1` |
| 6 | `03` | IMANG INDAH AYUNINGRUM | ADMINISTRASI | `STATUS1` |
| 4 | `04` | ADIT | PROGRAMMER | `STATUS1` |
| 5 | `05` | FITRI AMALIA | ADM.KEUANGAN | `STATUS1` |
| 7 | `06` | AGUS HERMAWAN | MARKETING MANAGER | `STATUS1` |

Catatan:
- field backend team member adalah `code`, `name`, `position`, `status`, `is_active`
- status di database sekarang dipakai sebagai string, dan data existing memakai `STATUS1`

### Product Group Aktif

| ID | Code | Name | acc_omzet | cdf_piutang |
|---|---|---|---|---|
| 1 | `LSA` | LISENSI APLIKASI SIGMA | `5033` | `1535` |
| 3 | `LSATEST` | LISENSI TEST | `5033` | `1535` |
| 5 | `SERVICE` | SERVICE DAN MANTENANCE | `5033` | `1535` |

Catatan:
- `LSA` adalah group paling aman untuk relasi product
- backend validasi COA akan cek ke `chart_of_accounts`

### Products Aktif

| ID | Code | Name | author_code | author_name | product_group_id |
|---|---|---|---|---|---|
| 4 | `026724` | Arfey Moreno Jazzua | `03` | IMANG INDAH AYUNINGRUM | null |
| 5 | `05` | INVOICE MANAGEMENT SYSTEM | `03` | IMANG INDAH AYUNINGRUM | `1` |

Catatan:
- `author_code` harus kode team member, bukan ID
- `product_group_id` harus ID dari `product_groups`
- product ini cocok untuk test relasi ke invoice dan work order

### Company Records

| ID | Code | Name | City |
|---|---|---|---|
| 1 | `FITART` | PT. Fit Art Technology | Kedungkandang Sawojajar |
| 5 | `FITNES` | ELEK | Malang |
| 6 | `COMP01` | PT FITART NUSANTARA | SURABAYA |
| 7 | `CMP001` | PT FitArt Technology | Jakarta |

Catatan:
- menu `Companies` memakai tabel `companies`
- menu `Setup` memakai tabel `company_settings`
- jangan tertukar antara master company dan company settings

### Banks

| ID | Code | Name | type | acc_code | cdf_code |
|---|---|---|---|---|---|
| 3 | `BM02` | BANK MANDIRI 8358201-4 | `M` | `1202` | `1202` |
| 4 | `BM03` | BANK BNI 9457201-4 | `M` | `1203` | `1203` |
| 5 | `BM04` | BANK BRI 567567790 | `M` | `1204` | `1204` |
| 6 | `KH` | TUNAI/KAS | `H` | `1101` | `1101` |
| 8 | `BM01X` | BANK BCA TEST | `M` | `1201` | `1201` |
| 9 | `BK01` | BANK BCA | `M` | `1201` | `1201` |

Catatan:
- `type` hanya `M` atau `H`
- field `acc_code` dan `cdf_code` harus valid di `chart_of_accounts`

### Clients

| ID | Code | Name | Status | City |
|---|---|---|---|---|
| 5 | `CLTEST00` | PT CUSTOMER TESTING | `NON GROUP` | Jakarta |
| 6 | `CLTEST000` | PT CUSTOMER TESTING | `NON GROUP` | Jakarta |
| 7 | `CL001` | PT INDO JAYA MAKMUR | `NON GROUP` | SURABAYA |

### Invoice Types

| ID | Code | Name | is_license | auto_create_number | is_active |
|---|---|---|---|---|---|
| 1 | `501` | PENJUALAN HARDWARE | false | true | true |
| 3 | `503` | PENDAPATAN JASA | false | true | true |
| 4 | `504` | PENDAPATAN LICENSE | true | true | true |
| 6 | `S04` | PENDAPATAN LICENSE | true | true | false |
| 7 | `500` | Arfey Moreno Jazzua | true | true | true |

Catatan:
- `504` paling aman untuk invoice license
- `S04` ada tetapi saat ini nonaktif

### Items

| ID | Code | Name | acc_omzet | acc_piutang | cdf_omzet | cdf_piutang |
|---|---|---|---|---|---|---|
| 1 | `1` | INSTALL | `5032` | `1532` | `5032` | `1532` |
| 3 | `3` | MAINTENANCE | `5035` | `1535` | `5035` | `1535` |
| 4 | `4` | HARDWARE | `5011` | `1511` | `5011` | `1511` |
| 5 | `5` | DEVELOPMENT SYSTEM | `5019` | `1519` | `5019` | `1519` |
| 6 | `6` | NETWORK SERVICE | `5032` | `1532` | `5032` | `1532` |
| 7 | `7` | PRODUK LAINNYA | `5032` | `1532` | `5032` | `1532` |
| 9 | `ITEM122` | REno | `5033` | `1535` | `5033` | `1535` |
| 10 | `ITMTEST01` | LICENSE TEST | `5033` | `1535` | `5033` | `1535` |

Catatan:
- item cocok untuk testing form work order dan invoice item
- backend item validasi COA ke `chart_of_accounts`

### Master Item Products

| ID | Code | Name | Unit | Price | acc_omzet | acc_omzet_np | acc_piutang | cdf_piutang | is_active |
|---|---|---|---|---:|---|---|---|---|---|
| 1 | `MIP-1` | INSTALL | UNIT | 0 | `5032` | `5032` | `1532` | `1532` | false |
| 3 | `MIP-3` | MAINTENANCE | UNIT | 0 | `5035` | `5035` | `1535` | `1535` | false |
| 4 | `MIP-4` | HARDWARE | UNIT | 0 | `5011` | `5011` | `1511` | `1511` | true |
| 5 | `MIP-5` | DEVELOPMENT SYSTEM | UNIT | 0 | `5019` | `5019` | `1519` | `1519` | true |
| 6 | `MIP-6` | NETWORK SERVICE | UNIT | 0 | `5032` | `5032` | `1532` | `1532` | false |
| 7 | `MIP-7` | PRODUK LAINNYA | UNIT | 0 | `5032` | `5032` | `1532` | `1532` | false |
| 8 | `LSA` | LISENSI APLIKASI SIGMA | UNIT | 0 | `5033` | `5033` | `1535` | `1535` | true |
| 9 | `02` | ELEK | PCS | 100000 | `5033` | `5033` | `1535` | `1535` | false |
| 12 | `MIP001` | LICENSI APLIKASI | PCS | 1000000 | `5033` | `5033` | `1535` | `1535` | true |

Catatan:
- controller backend untuk master item product menerima `acc_omzet`, `acc_omzet_np`, `acc_piutang`, `cdf_piutang`
- `cdf_omzet` ada di tabel, tetapi tidak dikirim oleh controller create/update
- untuk testing form, gunakan `MIP001` atau `LSA` sebagai data aktif

### Current Setup Data

#### Company Settings

Record aktif saat ini:

- `company_name`: `PT FitArt Technology`
- `address`: `Jakarta`
- `city`: `Jakarta`
- `phone`: `0211234567`
- `period_start`: `2026-01-01`
- `acc_ppn_kes`: `3213`
- `acc_ppn_mas`: `3214`
- `acc_discount`: `5090`
- `bank1`: `BCA`
- `bank1_sn`: `Cabang Utama`
- `bank1_ac`: `1234567890`
- `bank2`: `Mandiri`
- `bank2_sn`: `Cabang Pusat`
- `bank2_ac`: `0987654321`

#### Invoice Series

Record aktif saat ini:

- `tax_period`: `03-2026`
- `tax_code`: `010`
- `sequence`: `1`
- `start_number`: `04239178`
- `end_number`: `04239205`
- `last_number`: `04239179`
- `ppn_percentage`: `11`
- `dpp_percentage`: `1.11`

Makna untuk testing:
- kalau tombol `next number` dipakai, angka berikutnya harus lanjut dari `04239180`

#### Protection Period

Record aktif saat ini:

- `period`: `2026-03`
- `is_protected`: true
- `protected_at`: `2026-03-31`
- `protected_by`: `1`

Makna untuk testing:
- transaksi write di periode `2026-03` kemungkinan akan ditolak
- untuk uji create/update, pakai periode `2026-04` atau periode lain yang belum terkunci

#### Work Order

Record aktif saat ini:

- `id`: `4`
- `number`: `WO-042026-0001`
- `client_id`: `7`
- `product_id`: `5`
- `item_id`: `1`
- `status`: `AKTIF`
- `amount`: `1000000.00`

Ini adalah data paling aman untuk testing:
- client: `CL001`
- product: `05`
- item: `1`

#### Journals

Record jurnal yang sudah ada:

- Invoice posting untuk `001/504/LP/1025`
  - debit `1532` = `3330000.00`
  - credit `5032` = `3000000.00`
  - credit `3213` = `330000.00`
- Receipt posting untuk `BP-202603-0001`
  - debit `1111` = `1054500.00`
  - credit `1532` = `1054500.00`

Ini berguna untuk testing:
- `Journals`
- `AR Ledger`
- `Sales Report`
- `Cash Report`
- `Print Receipt`

## 4. Peta Menu Frontend ke Backend

### Auth

- `POST /api/login`
- `POST /api/logout`
- `GET /api/me`

### Setup

- `GET /api/company/settings`
- `POST /api/company/settings`
- `GET /api/tax-series`
- `POST /api/tax-series`
- `POST /api/tax-series/{taxSeries}/next`
- `DELETE /api/tax-series/{taxSeries}`

### Master Data

- `GET/POST/PUT/DELETE /api/users`
- `GET/POST/PUT/DELETE /api/companies`
- `GET/POST/PUT/DELETE /api/clients`
- `GET/POST/PUT/DELETE /api/team-members`
- `GET/POST/PUT/DELETE /api/product-groups`
- `GET/POST/PUT/DELETE /api/products`
- `GET/POST/PUT/DELETE /api/items`
- `GET/POST/PUT/DELETE /api/banks`
- `GET/POST/PUT/DELETE /api/invoice-types`
- `GET/POST/PUT/DELETE /api/master-item-products`

### Sales

- `GET/POST/PUT/DELETE /api/work-orders`
- `POST /api/work-orders/{work_order}/assign-team`
- `GET/POST/PUT/DELETE /api/installations`
- `GET/POST/PUT/DELETE /api/licenses`
- `GET/POST/PUT/DELETE /api/stop-licenses`

### Finance

- `GET/POST/PUT/DELETE /api/invoices`
- `GET/POST/PUT/DELETE /api/invoice-items`
- `GET /api/invoice-license`
- `GET /api/invoice-license/{invoice}`
- `GET /api/invoice-license/{invoice}/summary-journal`
- `GET /api/invoice-products`
- `GET /api/invoice-products/{invoice}`
- `GET /api/invoice-products/{invoice}/summary-journal`
- `GET/POST/PUT/DELETE /api/payments`
- `GET/POST/PUT/DELETE /api/debit-credit-notes`
- `GET /api/debit-credit-notes/{debitCreditNote}/summary-journal`
- `GET /api/receivables`
- `GET /api/receivables/{receivable}`
- `POST /api/receivables/process`

### Posting

- `GET /api/posting/payments`
- `GET /api/posting/payments/{postingPayment}`
- `GET /api/posting/payments/{postingPayment}/summary-journal`
- `GET /api/posting/payments/{postingPayment}/costs`
- `POST /api/posting/payments`
- `POST /api/posting/payments/{postingPayment}/costs`
- `PUT /api/posting/payments/{postingPayment}/costs/{cost}`
- `DELETE /api/posting/payments/{postingPayment}/costs/{cost}`
- `POST /api/posting/omzet`

### Reports

- `GET /api/reports/dashboard`
- `GET /api/reports/fiscal-commercial`
- `GET /api/reports/sales`
- `GET /api/reports/sales/summary`
- `GET /api/reports/sales/client`
- `GET /api/reports/ar/ledger`
- `GET /api/reports/ar/summary`
- `GET /api/reports/cash`
- `GET /api/reports/invoices/register`
- `GET /api/reports/invoices/history`
- `GET /api/reports/tax`
- `GET /api/reports/tax/summary`
- `GET /api/reports/tax/vat`
- `GET /api/reports/receivable/detail`
- `GET /api/reports/receivable/summary`
- `GET /api/reports/receivable/by-item`
- `GET /api/reports/receivable/data`
- `GET /api/reports/receivable/process-detail`

### Print

- `GET /api/print/invoices/batch`
- `POST /api/print/invoices/batch`
- `GET /api/print/invoices/{invoice}/pdf`
- `GET /api/print/receipts/{posting_payment}/pdf`

### Protection

- `GET/POST/PUT/DELETE /api/protections`

## 5. Skenario Testing Per Modul

## 5.1 Auth

Yang dicek:
- login sukses
- token tersimpan
- `GET /api/me` mengembalikan user yang sedang login
- logout menghapus session/token

Flow:
1. Login dengan `superadmin@fitart.co.id`.
2. Refresh halaman.
3. Pastikan user tetap login.
4. Logout.
5. Pastikan kembali ke halaman login.

## 5.2 Team Member

Field backend:
- `code`
- `name`
- `position`
- `status`
- `is_active`

Data aman:
- `01`
- `03`
- `04`
- `05`
- `06`

Contoh payload create:
```json
{
  "code": "07",
  "name": "BUDI SETIAWAN",
  "position": "SUPPORT",
  "status": "STATUS1",
  "is_active": true
}
```

Yang dicek:
1. List tampil dulu.
2. Create team member baru.
3. Edit `ADIT` atau `AGUS HERMAWAN`.
4. Search by code atau name.

## 5.3 Product Groups

Field backend:
- `code`
- `name`
- `acc_omzet`
- `cdf_piutang`
- `is_active`

Data aman:
- `LSA`
- `LSATEST`
- `SERVICE`

Contoh payload create:
```json
{
  "code": "SUPPORT",
  "name": "SUPPORT DAN MAINTENANCE",
  "acc_omzet": "5033",
  "cdf_piutang": "1535",
  "is_active": true
}
```

Yang dicek:
1. Dropdown COA harus ambil data dari `chart_of_accounts`.
2. Kode group harus uppercase / disimpan uppercase.
3. Relasi product ke group harus muncul setelah refresh.

## 5.4 Products

Field backend:
- `code`
- `name`
- `specification`
- `description`
- `author_code`
- `author_name`
- `compiler`
- `year`
- `product_group_id`
- `is_active`

Data aman:
- product existing `05`
- author code valid `03`
- product group valid `1` (`LSA`)

Contoh payload create:
```json
{
  "code": "06",
  "name": "INVOICE PORTAL",
  "specification": "BILLING SYSTEM",
  "description": "Testing product frontend",
  "author_code": "03",
  "compiler": "LARAVEL 10",
  "year": "2026",
  "product_group_id": 1,
  "is_active": true
}
```

Yang dicek:
1. `author_code` harus code team member, bukan ID.
2. `product_group_id` harus ID product group yang valid.
3. Backend akan mengisi `author_name` otomatis dari code.

## 5.5 Companies

Field backend:
- `code`
- `name`
- `address`
- `address_invoice`
- `city`
- `city_invoice`
- `phone`
- `fax`
- `email`
- `website`
- `npwp`
- `npkp`
- `tax_name`
- `tax_position`
- `invoice_name`
- `invoice_position`
- `invoice_name_2`
- `invoice_position_2`
- `invoice_tolerance_days`
- `upgrade_days`
- `letterhead_top`
- `letterhead_bottom`

Data aman:
- `FITART`
- `CMP001`

Contoh payload create:
```json
{
  "code": "CMP002",
  "name": "PT FitArt Demo",
  "address": "Jl. Sudirman No. 1",
  "address_invoice": "Jl. Sudirman No. 1",
  "city": "Jakarta",
  "city_invoice": "Jakarta",
  "phone": "021-1234567",
  "fax": "021-1234568",
  "email": "finance@fitart.co.id",
  "website": "https://fitart.co.id",
  "npwp": "01.234.567.8-901.000",
  "npkp": "PKP-CMP002",
  "tax_name": "PT FitArt Demo",
  "tax_position": "Direktur",
  "invoice_name": "Andi Pratama",
  "invoice_position": "Finance Manager",
  "invoice_name_2": "Sinta Lestari",
  "invoice_position_2": "Supervisor AR",
  "invoice_tolerance_days": "60",
  "upgrade_days": "30",
  "letterhead_top": "PT FitArt Demo",
  "letterhead_bottom": "Head Office"
}
```

## 5.6 Banks

Field backend:
- `code`
- `name`
- `account_number`
- `account_name`
- `type`
- `acc_code`
- `cdf_code`
- `is_active`

Data aman:
- `BK01`
- `BM01X`
- `BM02`
- `BM03`
- `BM04`
- `KH`

Contoh payload create:
```json
{
  "code": "BK02",
  "name": "BANK BCA 2",
  "account_number": "1234567891",
  "account_name": "PT FITART NUSANTARA",
  "type": "M",
  "acc_code": "1201",
  "cdf_code": "1201",
  "is_active": true
}
```

Yang dicek:
- `type` hanya `M` atau `H`
- `acc_code` dan `cdf_code` harus ada di COA

## 5.7 Clients

Field backend:
- `code`
- `status`
- `name`
- `address`
- `city`
- `phone`
- `fax`
- `npwp`
- `npkp`
- `tax_name`
- `tax_address`
- `credit_term_days`
- `is_active`

Data aman:
- `CLTEST00`
- `CLTEST000`
- `CL001`

Contoh payload create:
```json
{
  "code": "CL002",
  "status": "NON GROUP",
  "name": "PT CUSTOMER DEMO",
  "address": "Jl. Raya No. 10",
  "city": "SURABAYA",
  "phone": "0311234567",
  "fax": "0311234568",
  "npwp": "00.000.000.0-000.000",
  "npkp": "01.234.567.8",
  "tax_name": "PT CUSTOMER DEMO",
  "tax_address": "Jl. Raya No. 10",
  "credit_term_days": 30,
  "is_active": true
}
```

## 5.8 Invoice Types

Field backend:
- `code`
- `name`
- `is_license`
- `auto_create_number`
- `is_active`

Data aman:
- `504` untuk license
- `501` atau `503` untuk non-license
- `500` data test aktif

Contoh payload create:
```json
{
  "code": "506",
  "name": "PENDAPATAN DEMO",
  "is_license": false,
  "auto_create_number": true,
  "is_active": true
}
```

Catatan:
- `S04` ada tetapi saat ini nonaktif
- menu frontend harus cek list aktif dulu supaya tidak memilih data yang sudah nonaktif

## 5.9 Items

Field backend:
- `code`
- `name`
- `acc_omzet`
- `acc_piutang`
- `cdf_omzet`
- `cdf_piutang`
- `is_active`

Data aman:
- `1` INSTALL
- `3` MAINTENANCE
- `4` HARDWARE
- `5` DEVELOPMENT SYSTEM
- `6` NETWORK SERVICE
- `7` PRODUK LAINNYA
- `ITEM122`
- `ITMTEST01`

Contoh payload create:
```json
{
  "code": "ITMTEST02",
  "name": "MODULE TEST",
  "acc_omzet": "5033",
  "acc_piutang": "1535",
  "cdf_omzet": "5033",
  "cdf_piutang": "1535",
  "is_active": true
}
```

## 5.10 Master Item Products

Field backend yang benar dari controller:
- `code`
- `name`
- `unit`
- `price`
- `acc_omzet`
- `acc_omzet_np`
- `acc_piutang`
- `cdf_piutang`
- `is_active`

Catatan penting:
- `cdf_omzet` ada di tabel lama, tetapi controller create/update tidak memakainya
- jadi frontend jangan kirim `cdf_omzet` kalau mau aman

Data aman:
- `MIP001`
- `LSA`
- `MIP-4`
- `MIP-5`

Contoh payload create:
```json
{
  "code": "MIP002",
  "name": "LICENSI DEMO",
  "unit": "PCS",
  "price": 1000000,
  "acc_omzet": "5033",
  "acc_omzet_np": "5033",
  "acc_piutang": "1535",
  "cdf_piutang": "1535",
  "is_active": true
}
```

Yang dicek:
1. price harus numeric.
2. COA harus valid di `chart_of_accounts`.
3. List aktif di frontend harus hanya menampilkan yang `is_active = true`.

## 5.11 Users

Field backend:
- `username`
- `name`
- `email`
- `password`
- `role`
- `status` atau `is_active` di response

Role valid:
- `super_admin`
- `finance_admin`
- `sales_operator`
- `ar_collector`
- `auditor_viewer`
- `manager`

Data login aman:
- `superadmin@fitart.co.id`
- `financeadmin@fitart.co.id`
- `arcollector@fitart.co.id`
- `salesoperator@fitart.co.id`
- `manager@fitart.co.id`
- `admin@fitart.co.id`

Contoh payload create:
```json
{
  "name": "QA Tester",
  "email": "qa.tester@fitart.co.id",
  "password": "secret123",
  "role": "manager",
  "status": "active"
}
```

Catatan:
- username boleh digenerate dari email kalau tidak diisi
- frontend harus mengikuti role yang valid dari backend

## 5.12 Setup

### Company Settings

Field backend:
- `logo`
- `company_name`
- `address`
- `city`
- `phone`
- `npwp`
- `period_start`
- `acc_ppn_kes`
- `acc_ppn_mas`
- `acc_discount`
- `bank1`
- `bank1_sn`
- `bank1_ac`
- `bank2`
- `bank2_sn`
- `bank2_ac`

Data existing:
- `company_name = PT FitArt Technology`
- `acc_ppn_kes = 3213`
- `acc_ppn_mas = 3214`
- `acc_discount = 5090`

### Tax Series

Field backend:
- `period`
- `filled_date`
- `start_number`
- `end_number`
- `tax_code`
- `ppn_percentage`
- `dpp_percentage`

Data existing:
- `period = 03-2026`
- `tax_code = 010`
- `current_number = 04239179`

Catatan:
- `current_number` muncul di response, bukan field request utama saat create

Yang dicek:
1. `GET` dulu sebelum update.
2. `next number` harus naik satu angka.
3. Jangan test create tax series di periode yang sudah terkunci kecuali memang ingin cek error.

### Protection

Field backend:
- `period`
- `is_protected`
- `protected_at`
- `protected_by`

Data existing:
- `period = 2026-03`
- `is_protected = true`

Yang dicek:
1. create proteksi periode baru.
2. pastikan write request ke periode terkunci ditolak.
3. `protected_by` harus user valid.

## 5.13 Work Order

Field backend:
- `number`
- `date`
- `date_install`
- `start_license`
- `client_id`
- `product_id`
- `item_id`
- `status`
- `amount`
- `description`
- `item_count`
- `per_unit`
- `notes`
- `team` array

Role team valid:
- `implementor_1`
- `implementor_2`
- `programmer`
- `system_analyst`
- `supervisor`
- `client_spv`

Data aman:
- client `CL001` => `client_id = 7`
- product `05` => `product_id = 5`
- item `1` => `item_id = 1`

Contoh payload create:
```json
{
  "number": "WO-042026-0002",
  "date": "2026-04-27",
  "date_install": "2026-04-28",
  "start_license": "2026-04-28",
  "client_id": 7,
  "product_id": 5,
  "item_id": 1,
  "status": "AKTIF",
  "amount": 1000000,
  "description": "Implementasi modul keuangan",
  "item_count": 1,
  "per_unit": "UNIT",
  "notes": "Testing frontend",
  "team": [
    { "role": "implementor_1", "team_member_id": 1 },
    { "role": "programmer", "team_member_id": 4 },
    { "role": "system_analyst", "team_member_id": 6 },
    { "role": "supervisor", "team_member_id": 5 }
  ]
}
```

Yang dicek:
1. `team` harus array of role + team_member_id.
2. frontend jangan kirim field team terpisah seperti `programmer_id` atau `system_analyst_id`.
3. cari work order existing `WO-042026-0001` untuk validasi detail.

## 5.14 Installation

Field backend:
- `work_order_id`
- `client_id`
- `install_date`
- `implementor_1`
- `implementor_2`
- `implementor_3`
- `notes`

Contoh payload create:
```json
{
  "work_order_id": 4,
  "client_id": 7,
  "install_date": "2026-04-28",
  "implementor_1": "ADIT",
  "implementor_2": "IMANG INDAH AYUNINGRUM",
  "implementor_3": "AGUS HERMAWAN",
  "notes": "Go live"
}
```

Yang dicek:
- `work_order_id` wajib ada
- `client_id` wajib valid
- data existing installation saat snapshot masih kosong, jadi ini test create dulu

## 5.15 Stop License

Field backend:
- `number`
- `date`
- `stop_date`
- `work_order_id`
- `client_id`
- `product_id`
- `client_spv_id`
- `client_spv_code`
- `notes`
- `is_stopped`

Contoh payload create:
```json
{
  "number": "SL-202604-0001",
  "date": "2026-04-27",
  "stop_date": "2026-04-27",
  "work_order_id": 4,
  "client_spv_code": "03",
  "notes": "BERHENTI",
  "is_stopped": true
}
```

Yang dicek:
- backend akan ambil `client_id` dan `product_id` dari work order kalau belum diisi
- `client_spv_code` akan di-resolve menjadi `client_spv_id` dan nama team member

## 5.16 Invoice, Invoice Items, Payment, Debit/Credit Note

### Invoice

Field backend:
- `number`
- `invoice_type_id`
- `client_id`
- `bank_id`
- `work_order_id`
- `period`
- `date`
- `due_date`
- `tax_date`
- `tax_number`
- `tax_note`
- `invoice_note`
- `client_address`
- `description`
- `invoice_category`
- `invoice_mode`
- `tax_type`
- `instance`
- `invoice_bm_km`
- `invoice_bm_km_date`
- `bruto`
- `discount`
- `dpp`
- `ppn`
- `ppn_percentage`
- `dp`
- `other`
- `total`
- `include_ppn`
- `use_old_letterhead`
- `without_payment_posting`
- `stamp_and_signature`
- `auto_journal`
- `pass_protelasi`
- `is_paid`

Catatan penting:
- create invoice hanya boleh untuk `finance_admin` dan `super_admin`
- periode `2026-03` sudah locked, jadi pakai `2026-04` untuk testing create

Contoh payload create:
```json
{
  "number": "INV-202604-0001",
  "invoice_type_id": 4,
  "client_id": 7,
  "bank_id": 9,
  "work_order_id": 4,
  "period": "2026-04",
  "date": "2026-04-27",
  "due_date": "2026-05-27",
  "description": "Testing invoice frontend",
  "invoice_category": "LICENSE",
  "invoice_mode": "NORMAL",
  "include_ppn": true,
  "auto_journal": true,
  "is_paid": false
}
```

### Invoice Items

Field backend:
- `invoice_id`
- `master_item_product_id`
- `item_code`
- `item_name`
- `description`
- `qty`
- `unit`
- `price`
- `bruto`
- `months`

Contoh payload create:
```json
{
  "invoice_id": "<id invoice baru>",
  "master_item_product_id": 12,
  "item_code": "MIP001",
  "item_name": "LICENSI APLIKASI",
  "description": "Biaya lisensi bulanan",
  "qty": 1,
  "unit": "PCS",
  "price": 1000000,
  "months": 1
}
```

Catatan:
- kalau `bruto` tidak dikirim, backend akan hitung dari `qty x price`
- invoice item tidak boleh diubah jika invoice sudah posted

### Payments

Field backend:
- `number`
- `date`
- `invoice_id`
- `client_id`
- `description`
- `amount`

Catatan penting:
- `client_id` harus sama dengan client invoice
- create payment hanya boleh `finance_admin`, `ar_collector`, atau `super_admin`

### Debit/Credit Note

Field backend:
- `type`
- `number`
- `date`
- `invoice_id`
- `client_id`
- `description`
- `dpp_amount`
- `ppn_amount`
- `total_amount`
- `auto_journal`
- `is_posted`
- `items[]`

Contoh payload create:
```json
{
  "type": "D",
  "number": "DCN-202604-0001",
  "date": "2026-04-27",
  "invoice_id": "<id invoice baru>",
  "client_id": 7,
  "description": "Adjustment test",
  "dpp_amount": 100000,
  "ppn_amount": 11000,
  "total_amount": 111000,
  "auto_journal": true,
  "is_posted": false,
  "items": [
    {
      "sequence": 1,
      "item_code": "ADJ001",
      "item_name": "Adjustment Item",
      "description": "Penyesuaian nilai",
      "dpp_amount": 100000,
      "ppn_amount": 11000
    }
  ]
}
```

Yang dicek:
1. `type` hanya `D` atau `C`.
2. total amount harus konsisten dengan item.
3. summary journal harus balance.

## 5.17 Reports, Journals, Print

### Journals

Data yang sudah ada:
- invoice posting `001/504/LP/1025`
- posting payment `BP-202603-0001`

Yang dicek:
- debit dan kredit balance
- filter tanggal dan document number jalan

### Reports

Yang paling penting untuk diverifikasi:
- `Dashboard`
- `AR Ledger`
- `Sales Reports`
- `Invoice Register`
- `Invoice History`
- `Fiscal Report`
- `Cash Report`

Tips:
- kalau report kosong, cek dulu apakah transaksi sudah ada dan/atau sudah diposting

### Print Center

Yang dicek:
- print invoice PDF
- print receipt PDF
- batch print jalan

Tips:
- pastikan invoice atau receipt yang dipilih memang ada

## 6. Payload Cepat Yang Aman

### Team Member
```json
{
  "code": "07",
  "name": "BUDI SETIAWAN",
  "position": "SUPPORT",
  "status": "STATUS1",
  "is_active": true
}
```

### Product Group
```json
{
  "code": "SUPPORT",
  "name": "SUPPORT DAN MAINTENANCE",
  "acc_omzet": "5033",
  "cdf_piutang": "1535",
  "is_active": true
}
```

### Product
```json
{
  "code": "06",
  "name": "INVOICE PORTAL",
  "specification": "BILLING SYSTEM",
  "description": "Testing product frontend",
  "author_code": "03",
  "compiler": "LARAVEL 10",
  "year": "2026",
  "product_group_id": 1,
  "is_active": true
}
```

### Item
```json
{
  "code": "ITMTEST02",
  "name": "MODULE TEST",
  "acc_omzet": "5033",
  "acc_piutang": "1535",
  "cdf_omzet": "5033",
  "cdf_piutang": "1535",
  "is_active": true
}
```

### Master Item Product
```json
{
  "code": "MIP002",
  "name": "LICENSI DEMO",
  "unit": "PCS",
  "price": 1000000,
  "acc_omzet": "5033",
  "acc_omzet_np": "5033",
  "acc_piutang": "1535",
  "cdf_piutang": "1535",
  "is_active": true
}
```

### Client
```json
{
  "code": "CL002",
  "status": "NON GROUP",
  "name": "PT CUSTOMER DEMO",
  "address": "Jl. Raya No. 10",
  "city": "SURABAYA",
  "phone": "0311234567",
  "fax": "0311234568",
  "npwp": "00.000.000.0-000.000",
  "npkp": "01.234.567.8",
  "tax_name": "PT CUSTOMER DEMO",
  "tax_address": "Jl. Raya No. 10",
  "credit_term_days": 30,
  "is_active": true
}
```

### Bank
```json
{
  "code": "BK02",
  "name": "BANK BCA 2",
  "account_number": "1234567891",
  "account_name": "PT FITART NUSANTARA",
  "type": "M",
  "acc_code": "1201",
  "cdf_code": "1201",
  "is_active": true
}
```

### Work Order
```json
{
  "number": "WO-042026-0002",
  "date": "2026-04-27",
  "date_install": "2026-04-28",
  "start_license": "2026-04-28",
  "client_id": 7,
  "product_id": 5,
  "item_id": 1,
  "status": "AKTIF",
  "amount": 1000000,
  "description": "Implementasi modul keuangan",
  "item_count": 1,
  "per_unit": "UNIT",
  "notes": "Testing frontend",
  "team": [
    { "role": "implementor_1", "team_member_id": 1 },
    { "role": "programmer", "team_member_id": 4 },
    { "role": "system_analyst", "team_member_id": 6 }
  ]
}
```

### Stop License
```json
{
  "number": "SL-202604-0001",
  "date": "2026-04-27",
  "stop_date": "2026-04-27",
  "work_order_id": 4,
  "client_spv_code": "03",
  "notes": "BERHENTI",
  "is_stopped": true
}
```

### Invoice Series
```json
{
  "period": "04-2026",
  "tax_code": "010",
  "start_number": "04239206",
  "end_number": "04239250",
  "ppn_percentage": 11,
  "dpp_percentage": 1.11
}
```

### Protection Period
```json
{
  "period": "2026-04",
  "is_protected": true,
  "protected_at": "2026-04-27",
  "protected_by": 1
}
```

## 7. Cara Membaca Error

### 422
Validasi gagal.

Penyebab umum:
- field wajib kosong
- nama field salah
- ID atau code relasi tidak ada
- code duplikat
- nilai tidak sesuai enum

### 401
Token atau login bermasalah.

### 403
Role tidak punya akses ke endpoint.

### 404
Endpoint salah atau ID yang dikirim tidak ada.

### 409
Data sudah posted / terkunci / tidak boleh diubah.

### 423
Periode terkunci oleh protection.

## 8. Checklist Final

- [ ] Login pakai user valid
- [ ] `GET` list tampil sebelum create
- [ ] Team Member CRUD dites
- [ ] Product Group CRUD dites
- [ ] Product CRUD dites
- [ ] Company CRUD dites
- [ ] Bank CRUD dites
- [ ] Client CRUD dites
- [ ] Invoice Type CRUD dites
- [ ] Item CRUD dites
- [ ] Master Item Product CRUD dites
- [ ] Company Settings simpan berhasil
- [ ] Tax Series bisa create dan next number
- [ ] Protection period mengunci periode
- [ ] Work Order create dan assign team jalan
- [ ] Installation create jalan
- [ ] Stop License create jalan
- [ ] Invoice create jalan di periode yang tidak terkunci
- [ ] Invoice Item create jalan
- [ ] Payment validasi client matching invoice
- [ ] Debit/Credit Note summary journal balance
- [ ] Journals, reports, dan print bisa dibuka

## 9. Catatan Penting

- Guide ini disusun dari data database lokal yang benar-benar ada saat 27 April 2026.
- `Companies` dan `Setup > Company Settings` adalah dua tabel yang berbeda.
- `author_code` pada Product harus mengarah ke `team_members.code`.
- `client_spv_code` pada Stop License juga mengarah ke `team_members.code`.
- `Master Item Product` tidak perlu mengirim `cdf_omzet` dari frontend, karena controller tidak memakainya.
- Periode `2026-03` sudah terkunci, jadi pakai periode lain untuk testing create/update finance.
