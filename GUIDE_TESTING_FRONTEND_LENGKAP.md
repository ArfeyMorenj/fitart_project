# 🎯 GUIDE LENGKAP TESTING FRONTEND FITART

## 📋 Daftar Isi
1. [Data Master](#data-master)
2. [Struktur Database & Relasi](#struktur-database--relasi)
3. [Skenario Testing Per Fitur](#skenario-testing-per-fitur)
4. [Data Testing Lengkap](#data-testing-lengkap)
5. [Checklist Testing](#checklist-testing)

---

## 📊 DATA MASTER

### 1. USERS (6 User Total)
Database tersimpan di: `users` table

#### User 1: Super Admin
```json
{
  "id": 1,
  "username": "superadmin",
  "name": "Super Admin",
  "email": "superadmin@fitart.co.id",
  "role": "super_admin",
  "is_active": 1
}
```

#### User 2: Finance Admin
```json
{
  "id": 2,
  "username": "financeadmin",
  "name": "Finance Admin",
  "email": "financeadmin@fitart.co.id",
  "role": "finance_admin",
  "is_active": 1
}
```

#### User 3: AR Collector
```json
{
  "id": 3,
  "username": "arcollector",
  "name": "AR Collector",
  "email": "arcollector@fitart.co.id",
  "role": "ar_collector",
  "is_active": 1
}
```

**Catatan**: Total 6 user di database

---

### 2. TEAM MEMBERS (4 Total)
Database tersimpan di: `team_members` table

#### Field-field Team Member:
- `id`: ID Unik
- `code`: Kode Team Member (misal: "01", "04", "05")
- `name`: Nama Lengkap
- `position`: Posisi/Jabatan (misal: MANAGER AREA, PROGRAMMER, ADM.KEUANGAN)
- `status`: Status (misal: STATUS1)
- `is_active`: Status Aktif (true/false)
- `created_at`, `updated_at`, `deleted_at`

#### Sample Data Team Member:

| ID | Code | Name | Position | Status | Active |
|----|------|------|----------|--------|--------|
| 1 | 01 | IMAM S ARIFIN | MANAGER AREA | STATUS1 | true |
| 4 | 04 | ADIT | PROGRAMMER | STATUS1 | true |
| 5 | 05 | FITRI AMALIA | ADM.KEUANGAN | STATUS1 | true |
| (ada 1 lagi) | - | - | - | - | - |

---

### 3. PRODUCT GROUPS (3 Total)
Database tersimpan di: `product_groups` table

#### Field-field Product Group:
- `id`: ID Unik
- `code`: Kode Grup Produk (misal: "LSA", "LSATEST", "GPA")
- `name`: Nama Lengkap Grup Produk
- `acc_omzet`: Account Omzet (Chart of Account reference, misal: "5033")
- `cdf_piutang`: Account Piutang (Chart of Account reference, misal: "1535")
- `is_active`: Status Aktif (true/false)
- `created_at`, `updated_at`, `deleted_at`

#### Sample Data Product Group:

| ID | Code | Name | Acc Omzet | CDF Piutang | Active |
|----|------|------|-----------|------------|--------|
| 1 | LSA | LISENSI APLIKASI SIGMA | 5033 | 1535 | true |
| 3 | LSATEST | LISENSI TEST | 5033 | 1535 | true |
| 4 | GPA | REno | null | null | true |

**Relasi**: 
- Setiap Product Group bisa punya banyak Products
- Setiap Product Group terhubung dengan Chart of Accounts (via `acc_omzet` dan `cdf_piutang`)

---

### 4. PRODUCTS (2+ Total)
Database tersimpan di: `products` table

#### Field-field Product:
- `id`: ID Unik
- `code`: Kode Produk (misal: "04", "026724")
- `name`: Nama Produk
- `specification`: Spesifikasi/Tipe (misal: "ADS MANAGEMENT")
- `description`: Deskripsi Produk
- `author_code`: Kode Author (misal: "03")
- `author_name`: Nama Author (misal: "IMANG INDAH AYUNINGRUM")
- `compiler`: Compiler/Bahasa (misal: "DELPHI 6")
- `year`: Tahun (misal: "2006")
- `product_group_id`: **Foreign Key ke Product Group**
- `is_active`: Status Aktif (true/false)
- `created_at`, `updated_at`, `deleted_at`

#### Sample Data Product:

| ID | Code | Name | Specification | Author | Compiler | Year | Product Group ID |
|----|------|------|---------------|--------|----------|------|-----------------|
| 2 | 04 | IKLAN KOLOM (SIGMA KOLOM) | ADS MANAGEMENT | IMANG INDAH AYUNINGRUM | DELPHI 6 | 2006 | 1 |
| 4 | 026724 | Arfey Moreno Jazzua | null | - | - | - | - |

**Relasi**:
- Setiap Product milik 1 Product Group (`product_group_id`)
- Product terhubung dengan Team Member via `author_code`

---

### 5. CHART OF ACCOUNTS
Database tersimpan di: `chart_of_accounts` table

**Fungsi**: 
- Menyimpan daftar akun untuk pencatatan keuangan
- Digunakan oleh Product Group (`acc_omzet`, `cdf_piutang`)
- Digunakan dalam Journal Entry

**Field-field (estimasi)**:
- `id`: ID Unik
- `code`: Kode Akun (misal: "5033", "1535")
- `name`: Nama Akun
- `type`: Tipe Akun (misal: REVENUE, ASSET, LIABILITY)
- `balance`: Saldo
- `is_active`: Status Aktif

---

### 6. INVOICES
Database tersimpan di: `invoices` table

**Fungsi**: 
- Menyimpan dokumen invoice/faktur
- Dapat memiliki multiple Invoice Items

**Field-field (estimasi)**:
- `id`: ID Unik
- `invoice_number`: Nomor Invoice (misal: "INV-2026-001")
- `date`: Tanggal Invoice
- `customer_name`: Nama Customer
- `amount`: Jumlah Total
- `status`: Status Invoice (DRAFT, POSTED, PAID, CANCELLED)
- `created_at`, `updated_at`, `deleted_at`

---

### 7. INVOICE ITEMS
Database tersimpan di: `invoice_items` table

**Fungsi**: 
- Detail line items dari setiap invoice

**Field-field (estimasi)**:
- `id`: ID Unik
- `invoice_id`: **Foreign Key ke Invoice**
- `product_id`: **Foreign Key ke Product**
- `quantity`: Jumlah
- `unit_price`: Harga Satuan
- `amount`: Jumlah Total (quantity × unit_price)

---

### 8. JOURNAL ENTRIES
Database tersimpan di: `journal_entries` table

**Fungsi**: 
- Menyimpan entri akuntansi/jurnal
- Digunakan untuk posting keuangan

**Field-field (estimasi)**:
- `id`: ID Unik
- `entry_number`: Nomor Jurnal
- `date`: Tanggal Entry
- `account_id`: **Foreign Key ke Chart of Account**
- `debit`: Debit Amount
- `credit`: Credit Amount
- `description`: Deskripsi
- `status`: Status (DRAFT, POSTED)

---

## 🔗 STRUKTUR DATABASE & RELASI

### Entity Relationship Diagram (ERD)

```
┌──────────────────┐         ┌──────────────────┐
│      USERS       │         │  TEAM MEMBERS    │
├──────────────────┤         ├──────────────────┤
│ id               │         │ id               │
│ username         │         │ code             │
│ name             │         │ name             │
│ email            │         │ position         │
│ role             │         │ status           │
│ is_active        │         │ is_active        │
└──────────────────┘         └──────────────────┘


┌──────────────────────┐
│  PRODUCT GROUPS      │
├──────────────────────┤
│ id                   │
│ code                 │
│ name                 │
│ acc_omzet ──┐        │
│ cdf_piutang ├──→ CHART_OF_ACCOUNTS
│ is_active   │        │
└──────────────────────┘
          │
          │ 1:N
          │
    ┌─────▼──────────────┐
    │   PRODUCTS         │
    ├────────────────────┤
    │ id                 │
    │ code               │
    │ name               │
    │ product_group_id ──┘
    │ is_active          │
    └──────┬─────────────┘
           │ 1:N
           │
    ┌──────▼──────────────┐
    │  INVOICE ITEMS      │
    ├────────────────────┤
    │ id                 │
    │ product_id ────────┘
    │ invoice_id ──┐
    │ quantity     │
    │ unit_price   │
    └──────────────┤──────┐
                   │      │
                   │ N:1  │
              ┌────▼──────▼──────────┐
              │    INVOICES          │
              ├──────────────────────┤
              │ id                   │
              │ invoice_number       │
              │ customer_name        │
              │ amount               │
              │ status               │
              └──────────────────────┘


┌───────────────────────────┐
│ CHART_OF_ACCOUNTS         │
├───────────────────────────┤
│ id                        │
│ code (5033, 1535, etc)    │
│ name                      │
│ type                      │
│ balance                   │
└─────────────┬─────────────┘
              │ 1:N
              │
       ┌──────▼────────────────┐
       │  JOURNAL ENTRIES       │
       ├──────────────────────┤
       │ id                   │
       │ account_id ──────────┘
       │ date                 │
       │ debit                │
       │ credit               │
       │ status               │
       └──────────────────────┘
```

---

## 🧪 SKENARIO TESTING PER FITUR

### A. TESTING TEAM MEMBER MANAGEMENT

#### Skenario 1: Create Team Member
**Tujuan**: Test menambah team member baru

**Langkah**:
1. Login sebagai Super Admin
2. Navigasi ke Menu: **Team Members** (atau **Manajemen Tim**)
3. Klik tombol **+ Add New** atau **+ Tambah Baru**
4. Isi form:
   - **Code**: "06"
   - **Name**: "AGUS HERMAWAN"
   - **Position**: "MARKETING MANAGER"
   - **Status**: "STATUS1"
   - **Is Active**: Centang checkbox
5. Klik **Save** / **Simpan**
6. Verifikasi team member baru muncul di daftar

**Data Testing**:
```json
{
  "code": "06",
  "name": "AGUS HERMAWAN",
  "position": "MARKETING MANAGER",
  "status": "STATUS1",
  "is_active": true
}
```

**Expected Result**: ✅ Team member baru berhasil ditambahkan

---

#### Skenario 2: Read/View Team Members
**Tujuan**: Test melihat daftar team members

**Langkah**:
1. Login sebagai Super Admin
2. Navigasi ke **Team Members**
3. Lihat daftar: harus menampilkan minimal 4 team members:
   - IMAM S ARIFIN (01)
   - ADIT (04)
   - FITRI AMALIA (05)
   - AGUS HERMAWAN (06) ← dari skenario 1

**Expected Result**: ✅ Semua team members ditampilkan dengan benar

---

#### Skenario 3: Update Team Member
**Tujuan**: Test mengedit data team member

**Langkah**:
1. Navigasi ke **Team Members**
2. Klik **Edit** pada ADIT (Code: 04)
3. Ubah data:
   - **Position**: Dari "PROGRAMMER" → "SENIOR PROGRAMMER"
4. Klik **Save** / **Simpan**
5. Verifikasi perubahan

**Expected Result**: ✅ Posisi ADIT berubah menjadi "SENIOR PROGRAMMER"

---

#### Skenario 4: Delete/Deactivate Team Member
**Tujuan**: Test menonaktifkan team member

**Langkah**:
1. Navigasi ke **Team Members**
2. Pilih team member yang akan dihapus
3. Klik **Delete** / **Hapus** atau **Deactivate** / **Nonaktifkan**
4. Konfirmasi penghapusan
5. Verifikasi team member tidak lagi muncul (atau status aktif = false)

**Expected Result**: ✅ Team member berhasil dihapus/dinonaktifkan

---

### B. TESTING PRODUCT GROUP MANAGEMENT

#### Skenario 1: Create Product Group
**Tujuan**: Test menambah product group baru

**Langkah**:
1. Login sebagai Super Admin
2. Navigasi ke **Product Groups** (atau **Grup Produk**)
3. Klik **+ Add New** / **+ Tambah Baru**
4. Isi form:
   - **Code**: "SERVICE"
   - **Name**: "SERVICE DAN MAINTENANCE"
   - **Acc Omzet** (Account Revenue): Pilih dari Chart of Accounts (misal: "5033")
   - **CDF Piutang** (Account Receivable): Pilih dari Chart of Accounts (misal: "1535")
   - **Is Active**: Centang
5. Klik **Save** / **Simpan**

**Data Testing**:
```json
{
  "code": "SERVICE",
  "name": "SERVICE DAN MAINTENANCE",
  "acc_omzet": "5033",
  "cdf_piutang": "1535",
  "is_active": true
}
```

**Expected Result**: ✅ Product Group baru berhasil ditambahkan

---

#### Skenario 2: View Product Groups dengan Relasi
**Tujuan**: Test melihat daftar product groups dan relasinya

**Langkah**:
1. Navigasi ke **Product Groups**
2. Lihat daftar group:
   - LSA (LISENSI APLIKASI SIGMA) - Acc: 5033, Piutang: 1535
   - LSATEST (LISENSI TEST) - Acc: 5033, Piutang: 1535
   - GPA (REno) - Acc: null, Piutang: null
   - SERVICE (SERVICE DAN MAINTENANCE) - Acc: 5033, Piutang: 1535 ← dari skenario 1
3. Klik pada **LSA** untuk melihat detail:
   - Harus menampilkan informasi group
   - Harus menampilkan produk-produk yang termasuk di dalamnya

**Expected Result**: ✅ Semua product groups ditampilkan dengan relasi yang benar

---

#### Skenario 3: Update Product Group
**Tujuan**: Test mengubah data product group

**Langkah**:
1. Navigasi ke **Product Groups**
2. Klik **Edit** pada GPA
3. Ubah data:
   - **Name**: "REno" → "Reno Complete Edition"
   - **Acc Omzet**: null → "5033"
   - **CDF Piutang**: null → "1535"
4. Klik **Save**
5. Verifikasi perubahan

**Expected Result**: ✅ Product Group GPA berhasil diupdate

---

### C. TESTING PRODUCT MANAGEMENT

#### Skenario 1: Create Product
**Tujuan**: Test menambah produk baru

**Langkah**:
1. Login sebagai Super Admin
2. Navigasi ke **Products** (atau **Produk**)
3. Klik **+ Add New** / **+ Tambah Baru**
4. Isi form:
   - **Code**: "05"
   - **Name**: "INVOICE MANAGEMENT SYSTEM"
   - **Specification**: "BILLING SYSTEM"
   - **Description**: "Sistem manajemen invoice terintegrasi"
   - **Author Code**: "01" (IMAM S ARIFIN)
   - **Author Name**: "IMAM S ARIFIN"
   - **Compiler**: "LARAVEL 10"
   - **Year**: "2026"
   - **Product Group**: Pilih "LSA" (ID: 1)
   - **Is Active**: Centang
5. Klik **Save** / **Simpan**

**Data Testing**:
```json
{
  "code": "05",
  "name": "INVOICE MANAGEMENT SYSTEM",
  "specification": "BILLING SYSTEM",
  "description": "Sistem manajemen invoice terintegrasi",
  "author_code": "01",
  "author_name": "IMAM S ARIFIN",
  "compiler": "LARAVEL 10",
  "year": "2026",
  "product_group_id": 1,
  "is_active": true
}
```

**Expected Result**: ✅ Produk baru berhasil ditambahkan

---

#### Skenario 2: View Products dengan Relasi
**Tujuan**: Test melihat daftar produk dengan relasi ke Product Group

**Langkah**:
1. Navigasi ke **Products**
2. Lihat daftar produk:
   - IKLAN KOLOM (SIGMA KOLOM) - Group: LSA (ID: 1), Code: 04
   - Arfey Moreno Jazzua - Code: 026724
   - INVOICE MANAGEMENT SYSTEM - Group: LSA (ID: 1), Code: 05 ← dari skenario 1
3. Klik detail produk untuk melihat:
   - Nama Product Group
   - Informasi author
   - Specification dan description

**Expected Result**: ✅ Semua produk ditampilkan dengan Product Group yang benar

---

#### Skenario 3: Update Product
**Tujuan**: Test mengubah data produk

**Langkah**:
1. Navigasi ke **Products**
2. Klik **Edit** pada "IKLAN KOLOM (SIGMA KOLOM)" (ID: 2)
3. Ubah data:
   - **Name**: Tambahkan " - UPDATED"
   - **Compiler**: "DELPHI 6" → "DELPHI 10"
4. Klik **Save**

**Expected Result**: ✅ Produk berhasil diupdate

---

### D. TESTING INVOICE MANAGEMENT

#### Skenario 1: Create Invoice
**Tujuan**: Test membuat invoice baru

**Langkah**:
1. Login sebagai Finance Admin
2. Navigasi ke **Invoices** (atau **Faktur/Invoice**)
3. Klik **+ Create Invoice** / **+ Buat Invoice Baru**
4. Isi form:
   - **Invoice Number**: "INV-2026-001"
   - **Date**: Tanggal hari ini
   - **Customer Name**: "PT INDO JAYA MAKMUR"
   - **Status**: "DRAFT"
   - **Is Active**: Centang
5. Klik **Next** / **Lanjutkan** untuk menambah items

**Data Testing**:
```json
{
  "invoice_number": "INV-2026-001",
  "date": "2026-04-25",
  "customer_name": "PT INDO JAYA MAKMUR",
  "amount": 0,
  "status": "DRAFT",
  "is_active": true
}
```

**Expected Result**: ✅ Invoice baru berhasil dibuat

---

#### Skenario 2: Add Invoice Items
**Tujuan**: Test menambah item ke invoice

**Langkah**:
1. Dari invoice yang baru dibuat (INV-2026-001)
2. Klik **+ Add Item** / **+ Tambah Item**
3. Isi form item:
   - **Product**: Pilih "IKLAN KOLOM (SIGMA KOLOM)" (ID: 2)
   - **Quantity**: 5
   - **Unit Price**: 1.000.000
   - **Amount**: Auto calculate = 5.000.000
4. Klik **Add**
5. Tambah item kedua:
   - **Product**: Pilih "INVOICE MANAGEMENT SYSTEM" (ID: 5)
   - **Quantity**: 2
   - **Unit Price**: 2.000.000
   - **Amount**: Auto calculate = 4.000.000
6. Klik **Add**

**Data Testing Item 1**:
```json
{
  "invoice_id": 1,
  "product_id": 2,
  "quantity": 5,
  "unit_price": 1000000,
  "amount": 5000000
}
```

**Data Testing Item 2**:
```json
{
  "invoice_id": 1,
  "product_id": 5,
  "quantity": 2,
  "unit_price": 2000000,
  "amount": 4000000
}
```

**Expected Result**: 
- ✅ Items berhasil ditambahkan
- ✅ Total invoice terupdate = 9.000.000

---

#### Skenario 3: View Invoice dengan Items
**Tujuan**: Test melihat detail invoice dan items-nya

**Langkah**:
1. Navigasi ke **Invoices**
2. Klik invoice "INV-2026-001"
3. Verifikasi data yang ditampilkan:
   - Invoice Number: INV-2026-001
   - Customer: PT INDO JAYA MAKMUR
   - Date: Tanggal hari ini
   - Status: DRAFT
   - **Items**:
     - Item 1: IKLAN KOLOM (SIGMA KOLOM) × 5 @ 1.000.000 = 5.000.000
     - Item 2: INVOICE MANAGEMENT SYSTEM × 2 @ 2.000.000 = 4.000.000
   - **Total**: 9.000.000

**Expected Result**: ✅ Invoice detail ditampilkan dengan benar beserta semua items

---

#### Skenario 4: Update Invoice
**Tujuan**: Test mengubah data invoice

**Langkah**:
1. Navigasi ke **Invoices**
2. Klik **Edit** pada "INV-2026-001"
3. Ubah:
   - **Customer Name**: "PT INDO JAYA MAKMUR" → "PT MAJU JAYA BERSAMA"
4. Klik **Save**
5. Verifikasi perubahan

**Expected Result**: ✅ Nama customer berhasil diupdate

---

#### Skenario 5: Post Invoice (Change Status)
**Tujuan**: Test mengubah status invoice dari DRAFT ke POSTED

**Langkah**:
1. Navigasi ke **Invoices**
2. Buka invoice "INV-2026-001"
3. Klik tombol **Post** / **Post Invoice** / **Posting**
4. Verifikasi status berubah dari DRAFT menjadi POSTED

**Expected Result**: ✅ Invoice berhasil di-post, status menjadi POSTED

---

### E. TESTING JOURNAL ENTRY / POSTING ACCOUNTING

#### Skenario 1: Create Journal Entry
**Tujuan**: Test membuat jurnal akuntansi

**Langkah**:
1. Login sebagai Finance Admin
2. Navigasi ke **Journal Entries** (atau **Jurnal Akuntansi**)
3. Klik **+ Create Entry** / **+ Buat Entry Baru**
4. Isi form:
   - **Entry Number**: "JE-2026-001"
   - **Date**: Tanggal hari ini
   - **Description**: "Posting invoice INV-2026-001"
   - **Status**: "DRAFT"
5. Klik **Next** untuk menambah detail akun

**Data Testing**:
```json
{
  "entry_number": "JE-2026-001",
  "date": "2026-04-25",
  "description": "Posting invoice INV-2026-001",
  "status": "DRAFT"
}
```

**Expected Result**: ✅ Journal Entry baru berhasil dibuat

---

#### Skenario 2: Add Journal Entry Details (Debit/Credit)
**Tujuan**: Test menambah detail debit dan credit ke jurnal

**Langkah**:
1. Dari journal entry "JE-2026-001" yang baru dibuat
2. Klik **+ Add Detail** / **+ Tambah Detail**
3. Tambah entry Debit:
   - **Account**: Pilih "1535" (CDF Piutang / Account Receivable)
   - **Description**: "Piutang dari invoice"
   - **Debit**: 9.000.000
   - **Credit**: 0
4. Klik **Add**
5. Tambah entry Credit:
   - **Account**: Pilih "5033" (Acc Omzet / Revenue)
   - **Description**: "Pendapatan dari penjualan"
   - **Debit**: 0
   - **Credit**: 9.000.000
6. Klik **Add**

**Data Testing Debit**:
```json
{
  "journal_entry_id": 1,
  "account_id": "1535",
  "description": "Piutang dari invoice",
  "debit": 9000000,
  "credit": 0
}
```

**Data Testing Credit**:
```json
{
  "journal_entry_id": 1,
  "account_id": "5033",
  "description": "Pendapatan dari penjualan",
  "debit": 0,
  "credit": 9000000
}
```

**Expected Result**:
- ✅ Detail berhasil ditambahkan
- ✅ Total Debit = Total Credit = 9.000.000 (Balanced)

---

#### Skenario 3: View Journal Entry
**Tujuan**: Test melihat detail jurnal dengan debit/credit

**Langkah**:
1. Navigasi ke **Journal Entries**
2. Klik "JE-2026-001"
3. Verifikasi data:
   - Entry Number: JE-2026-001
   - Date: Tanggal hari ini
   - Status: DRAFT
   - **Details**:
     - Debit: 1535 (CDF Piutang) = 9.000.000
     - Credit: 5033 (Acc Omzet) = 9.000.000
   - **Total**: Debit = Credit = 9.000.000 ✅ Balanced

**Expected Result**: ✅ Journal Entry detail ditampilkan dengan benar dan balanced

---

#### Skenario 4: Post Journal Entry
**Tujuan**: Test posting journal entry (change status to POSTED)

**Langkah**:
1. Navigasi ke **Journal Entries**
2. Buka "JE-2026-001"
3. Klik **Post** / **Posting**
4. Verifikasi status berubah menjadi POSTED

**Expected Result**: ✅ Journal Entry berhasil di-post

---

## 📊 DATA TESTING LENGKAP

### Data yang Sudah Ada di Database

#### Team Members
```json
[
  {
    "id": 1,
    "code": "01",
    "name": "IMAM S ARIFIN",
    "position": "MANAGER AREA",
    "status": "STATUS1",
    "is_active": true
  },
  {
    "id": 4,
    "code": "04",
    "name": "ADIT",
    "position": "PROGRAMMER",
    "status": "STATUS1",
    "is_active": true
  },
  {
    "id": 5,
    "code": "05",
    "name": "FITRI AMALIA",
    "position": "ADM.KEUANGAN",
    "status": "STATUS1",
    "is_active": true
  }
]
```

#### Product Groups
```json
[
  {
    "id": 1,
    "code": "LSA",
    "name": "LISENSI APLIKASI SIGMA",
    "acc_omzet": "5033",
    "cdf_piutang": "1535",
    "is_active": true
  },
  {
    "id": 3,
    "code": "LSATEST",
    "name": "LISENSI TEST",
    "acc_omzet": "5033",
    "cdf_piutang": "1535",
    "is_active": true
  },
  {
    "id": 4,
    "code": "GPA",
    "name": "REno",
    "acc_omzet": null,
    "cdf_piutang": null,
    "is_active": true
  }
]
```

#### Products
```json
[
  {
    "id": 2,
    "code": "04",
    "name": "IKLAN KOLOM (SIGMA KOLOM)",
    "specification": "ADS MANAGEMENT",
    "description": "Iklan kolom module",
    "author_code": "03",
    "author_name": "IMANG INDAH AYUNINGRUM",
    "compiler": "DELPHI 6",
    "year": "2006",
    "product_group_id": 1,
    "is_active": true
  },
  {
    "id": 4,
    "code": "026724",
    "name": "Arfey Moreno Jazzua",
    "specification": null,
    "description": null,
    "product_group_id": null,
    "is_active": true
  }
]
```

---

## ✅ CHECKLIST TESTING

### A. Team Member Management
- [ ] **Create Team Member**: Buat team member baru dengan code "06"
- [ ] **Read Team Members**: Lihat daftar 4+ team members
- [ ] **Update Team Member**: Edit posisi ADIT
- [ ] **Delete Team Member**: Hapus/nonaktifkan team member
- [ ] **Validation**: Test input validation (duplikasi code, field required, dll)

### B. Product Group Management
- [ ] **Create Product Group**: Buat group baru "SERVICE"
- [ ] **Read Product Groups**: Lihat semua 4 group
- [ ] **Update Product Group**: Edit GPA (add accounting codes)
- [ ] **Delete Product Group**: Hapus/nonaktifkan group
- [ ] **Validation**: Test validation untuk accounting code

### C. Product Management
- [ ] **Create Product**: Buat produk "INVOICE MANAGEMENT SYSTEM"
- [ ] **Read Products**: Lihat semua produk dengan Product Group
- [ ] **Update Product**: Edit produk (compiler, name, dll)
- [ ] **Delete Product**: Hapus/nonaktifkan produk
- [ ] **Relation Test**: Verifikasi Product Group relation ditampilkan

### D. Invoice Management
- [ ] **Create Invoice**: Buat INV-2026-001
- [ ] **Add Invoice Items**: Tambah 2 items dengan produk berbeda
- [ ] **Read Invoice**: Lihat detail invoice + items + total
- [ ] **Update Invoice**: Edit customer name
- [ ] **Post Invoice**: Ubah status DRAFT → POSTED
- [ ] **Validation**: Test total calculation, item validation

### E. Journal Entry / Accounting
- [ ] **Create Journal Entry**: Buat JE-2026-001
- [ ] **Add Debit/Credit Details**: Tambah debit (1535) dan credit (5033)
- [ ] **Verify Balanced**: Cek debit = credit
- [ ] **Read Journal Entry**: Lihat detail lengkap
- [ ] **Post Entry**: Ubah status DRAFT → POSTED
- [ ] **Account Code Validation**: Test gunakan account codes dari product group

### F. Cross-Feature Testing
- [ ] **Team Member dalam Product**: Author dari product adalah team member
- [ ] **Product Group dalam Product**: Setiap product punya product group
- [ ] **Product dalam Invoice Item**: Invoice item menggunakan product dari daftar
- [ ] **Account Codes dalam Journal**: Account codes dari product group digunakan di journal

### G. Validation & Error Handling
- [ ] **Duplicate Code**: Test input code yang sudah ada
- [ ] **Required Fields**: Test submit tanpa field required
- [ ] **Invalid References**: Test referensi yang tidak exist
- [ ] **Number Format**: Test input number dengan format salah
- [ ] **Date Validation**: Test input tanggal yang invalid

### H. UI/UX Testing
- [ ] **Form Layout**: Form harus responsive dan user-friendly
- [ ] **Error Messages**: Error message jelas dan helpful
- [ ] **Success Messages**: Notifikasi sukses setelah save
- [ ] **Table Display**: Tabel menampilkan data dengan scrollable jika perlu
- [ ] **Button Visibility**: Button edit/delete visible hanya jika user punya akses

### I. Performance Testing
- [ ] **Load Time**: Halaman load dalam waktu < 3 detik
- [ ] **Large Data**: Test dengan 100+ records
- [ ] **Search/Filter**: Test pencarian dan filter responsif
- [ ] **Pagination**: Test pagination jika ada

---

## 🔍 TIPS TESTING

### 1. Sebelum Testing
- [ ] Login dengan akun yang sesuai (Super Admin, Finance Admin, etc)
- [ ] Siapkan data testing dari checklist di atas
- [ ] Bersihkan database atau gunakan test database terpisah

### 2. Saat Testing
- [ ] Test happy path (normal scenario) dulu
- [ ] Kemudian test error scenario (invalid input, dll)
- [ ] Cek relasi data antar table
- [ ] Verifikasi data di database sesuai form input

### 3. Tools untuk Membantu
```bash
# Lihat data di database
php artisan tinker

# Contoh: lihat semua team members
TeamMember::all();

# Contoh: lihat product dengan relasi
Product::with('productGroup')->get();

# Contoh: lihat invoice dengan items
Invoice::with('invoiceItems.product')->get();
```

### 4. Dokumentasi Result
- Catat setiap test result (Pass/Fail)
- Screenshot untuk test yang fail
- Catat error message yang muncul
- Report bug jika ada yang tidak sesuai

---

## 📞 Pertanyaan FAQ

**Q: Apakah saya bisa test dengan data dummy?**
A: Ya, gunakan data dari section "Data Testing Lengkap" di atas.

**Q: Bagaimana jika ada field yang tidak cocok?**
A: Check di database dengan `php artisan tinker`, lihat struktur actualnya.

**Q: Apa bedanya DRAFT dan POSTED status?**
A: DRAFT = belum posting, POSTED = sudah posting dan tidak bisa diubah (biasanya).

**Q: Bagaimana cara lihat error di backend?**
A: Check log di `storage/logs/laravel.log`

**Q: Apakah semua fitur harus ditest?**
A: Ya, sesuai checklist di atas untuk coverage lengkap.

---

**Last Updated**: 25 April 2026
**Version**: 1.0
**Status**: Ready for Frontend Testing
