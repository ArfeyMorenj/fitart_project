# FitArt API Testing Guide - FINAL COMPLETE (Single File)

Dokumen ini disusun ulang total agar testing bisa dari satu file dan sinkron 1:1 dengan collection FitArt_JPAS_API_Complete.postman_collection.json.

## 0) Setup Umum
- Base URL: http://localhost:8000
- Header private endpoint:
- Accept: application/json
- Content-Type: application/json (POST/PUT/PATCH)
- Authorization: Bearer {{token}}

## 0.1) Environment yang Dipakai
- Import file environment: `FitArt_JPAS_Local.postman_environment.json`
- Import collection: `FitArt_JPAS_API_Complete.postman_collection.json`
- Variabel penting yang sudah disiapkan:
- `base_url`, `token`
- ID chaining: `client`, `product`, `item`, `invoice`, `postingPayment`, `posting_payment`, dll.
- Jika URL masih pakai placeholder path style Postman (contoh `:client`, `:invoice`), isi nilainya dari ID data hasil create sebelumnya.

## 1) Urutan Jalankan di Postman (FINAL)

### A. Persiapan
1. Migrate + seed fresh.
2. Import collection `FitArt_JPAS_API_Complete.postman_collection.json`.
3. Import environment `FitArt_JPAS_Local.postman_environment.json`.
4. Pilih environment itu di Postman.

### B. Auth (wajib dulu)
1. `POST /api/login`
2. `GET /api/me`
3. Pastikan `{{token}}` terisi.

### C. Lookup Check (sanity)
1. `GET /api/chart-of-accounts/search?q=...`
2. `GET /api/team-members/search?q=...`
3. `GET /api/product-groups/search?q=...`
4. `GET /api/items/search?q=...`
5. `GET /api/banks/search?q=...`
6. `GET /api/invoice-types/search?q=...`
7. `GET /api/master-item-products/search?q=...`
8. `GET /api/clients/search?q=...`

### D. Setup
1. `GET /api/company/settings`
2. `POST /api/company/settings`
3. `GET /api/tax-series`
4. `POST /api/tax-series`
5. `POST /api/tax-series/{taxSeries}/next`
6. `DELETE /api/tax-series/{taxSeries}` opsional cleanup setup test

### E. Master Data
1. `POST /api/team-members`
2. `POST /api/product-groups`
3. `POST /api/clients`
4. `POST /api/items`
5. `POST /api/banks`
6. `POST /api/invoice-types`
7. `POST /api/master-item-products`
8. `POST /api/products`
9. `POST /api/companies`
10. `POST /api/users`
11. `POST /api/users/{user}/assign-role`
12. `POST /api/users/{user}/toggle-status`
13. `POST /api/invoice-types/{invoiceType}/toggle-status`
14. `POST /api/master-item-products/{masterItemProduct}/toggle-status`
15. Setelah tiap POST, jalankan GET list + GET show endpoint itu.

### F. Sales Flow
1. `POST /api/work-orders`
2. `POST /api/work-orders/{work_order}/assign-team`
3. `POST /api/installations`
4. `POST /api/licenses`
5. `POST /api/stop-licenses`
6. Verifikasi GET list/show masing-masing.

### G. Finance Flow
1. `POST /api/invoices`
2. `POST /api/invoice-items`
3. `POST /api/debit-credit-notes`
4. `POST /api/payments`
5. `POST /api/recurring/invoices/generate`

### H. Posting Flow
1. `POST /api/posting/omzet`
2. `POST /api/posting/payments`
3. `GET /api/posting/payments/{postingPayment}/costs`
4. `POST /api/posting/payments/{postingPayment}/costs`
5. `PUT /api/posting/payments/{postingPayment}/costs/{cost}`
6. `DELETE /api/posting/payments/{postingPayment}/costs/{cost}`
7. `GET /api/journals`

### I. Receivable
1. `GET /api/receivables`
2. `GET /api/receivables/{receivable}`
3. `POST /api/receivables/process`

### J. Reports
1. `GET /api/reports/dashboard`
2. `GET /api/reports/ar/summary`
3. `GET /api/reports/ar/ledger`
4. `GET /api/reports/invoices/register`
5. `GET /api/reports/invoices/history`
6. `GET /api/reports/tax`
7. `GET /api/reports/tax/vat`
8. `GET /api/reports/tax/summary`
9. `GET /api/reports/sales`
10. `GET /api/reports/sales/summary`
11. `GET /api/reports/sales/client`
12. `GET /api/reports/cash`
13. `GET /api/reports/fiscal-commercial`
14. `GET /api/reports/receivable/by-item`
15. `GET /api/reports/receivable/data`
16. `GET /api/reports/receivable/process-detail`
17. `GET /api/reports/receivable/summary`
18. `GET /api/reports/receivable/detail`

### K. Print
1. `GET /api/print/invoices/batch`
2. `POST /api/print/invoices/batch`
3. `GET /api/print/invoices/{invoice}/pdf`
4. `GET /api/print/receipts/{posting_payment}/pdf`

### L. Update Cycle
1. Jalankan PUT lalu PATCH untuk resource CRUD utama:
   `companies`, `clients`, `team-members`, `product-groups`, `products`, `items`, `master-item-products`, `banks`, `invoice-types`, `users`
2. Lanjut:
   `work-orders`, `installations`, `licenses`, `stop-licenses`
3. Lanjut:
   `invoices`, `invoice-items`, `debit-credit-notes`, `payments`
4. Setiap update langsung cek GET show.

### M. Negative + Protection
1. Coba payload salah untuk 1 endpoint per modul, expect `422`.
2. Test tanpa token, expect `401`.
3. Test role tidak sesuai, expect `403`.
4. `POST /api/protections` lock period.
5. Coba endpoint write saat lock, expect `423`.
6. `PUT/PATCH/DELETE /api/protections/{protection}` sesuai skenario unlock.

### N. Cleanup
1. Hapus child transaksi dulu.
2. Hapus master setelah child bersih.
3. Jangan hapus parent sebelum child selesai.

## 1.1) Catatan Eksekusi Penting
- Kalau kamu sudah testing sampai `Finance Flow`, lanjut saja dari bagian `G` lalu terus ke `H`, `I`, `J`, `K`, `L`, `M`, `N`.
- Kamu tidak perlu import ulang Postman collection kalau request yang sekarang masih ada dan token masih valid.
- Kamu hanya perlu fresh migrate kalau data sebelumnya sudah ambigu, salah relasi, atau banyak toggle/update yang membuat hasil test tidak lagi bersih.
- Kalau kamu mau lanjut tanpa reset, cek dulu data inti ini masih ada dan valid:
  `client_id`, `product_id`, `item_id`, `bank_id`, `invoice_type_id`, `master_item_product_id`, `work_order_id`, `invoice_id`.
- Kalau salah satu ID inti itu sudah berubah atau terhapus, lebih efisien fresh migrate lalu ulang dari `A`.

## 1.2) Setelah POST, Lanjut GET Apa
Bagian ini dipakai kalau kamu bingung setelah create data harus cek endpoint mana.

### Setup
- Setelah `POST /api/company/settings`:
  - `GET /api/company/settings`
- Setelah `POST /api/tax-series`:
  - `GET /api/tax-series`
  - `POST /api/tax-series/{taxSeries}/next`
  - `GET /api/tax-series`

### Master Data
- Setelah `POST /api/team-members`:
  - `GET /api/team-members`
  - `GET /api/team-members/{team_member}`
  - `GET /api/team-members/search?q=<code atau name>`
- Setelah `POST /api/product-groups`:
  - `GET /api/product-groups`
  - `GET /api/product-groups/{productGroup}`
  - `GET /api/product-groups/search?q=<code atau name>`
- Setelah `POST /api/clients`:
  - `GET /api/clients`
  - `GET /api/clients/{client}`
  - `GET /api/clients/search?q=<code atau name>`
- Setelah `POST /api/items`:
  - `GET /api/items`
  - `GET /api/items/{item}`
  - `GET /api/items/search?q=<code atau name>`
- Setelah `POST /api/banks`:
  - `GET /api/banks`
  - `GET /api/banks/{bank}`
  - `GET /api/banks/search?q=<code atau name>`
- Setelah `POST /api/invoice-types`:
  - `GET /api/invoice-types`
  - `GET /api/invoice-types/{invoice_type}`
  - `GET /api/invoice-types/search?q=<code atau name>`
  - opsional: `POST /api/invoice-types/{invoiceType}/toggle-status`
- Setelah `POST /api/master-item-products`:
  - `GET /api/master-item-products`
  - `GET /api/master-item-products/{master_item_product}`
  - `GET /api/master-item-products/search?q=<code atau name>`
  - opsional: `POST /api/master-item-products/{masterItemProduct}/toggle-status`
- Setelah `POST /api/products`:
  - `GET /api/products`
  - `GET /api/products/{product}`
  - `GET /api/products/search?code=<code>&name=<name>`
- Setelah `POST /api/companies`:
  - `GET /api/companies`
  - `GET /api/companies/{company}`
  - `GET /api/companies/search?q=<code atau name>` jika route search tersedia di collection kamu
- Setelah `POST /api/users`:
  - `GET /api/users`
  - `GET /api/users/{user}`
  - opsional: `POST /api/users/{user}/assign-role`
  - opsional: `POST /api/users/{user}/toggle-status`

### Sales
- Setelah `POST /api/work-orders`:
  - `GET /api/work-orders`
  - `GET /api/work-orders/{work_order}`
  - `GET /api/work-orders/search?q=<number atau client>`
  - lanjut: `POST /api/work-orders/{work_order}/assign-team`
- Setelah `POST /api/installations`:
  - `GET /api/installations`
  - `GET /api/installations/{installation}`
- Setelah `POST /api/licenses`:
  - `GET /api/licenses`
  - `GET /api/licenses/{license}`
- Setelah `POST /api/stop-licenses`:
  - `GET /api/stop-licenses`
  - `GET /api/stop-licenses/{stop_license}`
  - `GET /api/stop-licenses/search?number=<nomor>` atau `?client_name=<nama>` atau `?work_order_number=<wo>`

### Finance
- Setelah `POST /api/invoices`:
  - `GET /api/invoices`
  - `GET /api/invoices/{invoice}`
  - `GET /api/invoice-license/{invoice}` jika invoice category `LICENSE`
  - `GET /api/invoice-license/search?number=<nomor>` jika mau cek flow license
  - `GET /api/invoice-products/{invoice}` jika invoice category `NON_LICENSE` / produk
  - `GET /api/invoice-products/search?number=<nomor>` jika mau cek flow produk
  - `GET /api/invoice-license/{invoice}/summary-journal` atau `GET /api/invoice-products/{invoice}/summary-journal`
- Setelah `POST /api/invoice-items`:
  - `GET /api/invoice-items`
  - `GET /api/invoice-items/{invoice_item}`
  - ulang cek parent invoice:
  - `GET /api/invoices/{invoice}`
  - `GET /api/invoice-license/{invoice}` atau `GET /api/invoice-products/{invoice}`
- Setelah `POST /api/debit-credit-notes`:
  - `GET /api/debit-credit-notes`
  - `GET /api/debit-credit-notes/{debit_credit_note}`
  - `GET /api/debit-credit-notes/search?number=<nomor>` atau `?client_name=<nama>`
  - `GET /api/debit-credit-notes/{debitCreditNote}/summary-journal`
- Setelah `POST /api/payments`:
  - `GET /api/payments`
  - `GET /api/payments/{payment}`
  - lalu cek invoice terkait:
  - `GET /api/invoices/{invoice}`
- Setelah `POST /api/recurring/invoices/generate`:
  - `GET /api/invoices`
  - `GET /api/invoice-license`
  - `GET /api/invoice-license/search?date_from=<periode awal>&date_to=<periode akhir>`

### Posting
- Setelah `POST /api/posting/omzet`:
  - `GET /api/journals?document_number=<nomor invoice>`
- Setelah `POST /api/posting/payments`:
  - `GET /api/posting/payments`
  - `GET /api/posting/payments/{postingPayment}`
  - `GET /api/posting/payments/search?q=<number atau client>`
  - `GET /api/posting/payments/{postingPayment}/summary-journal`
  - `GET /api/posting/payments/{postingPayment}/costs`
- Setelah `POST /api/posting/payments/{postingPayment}/costs`:
  - `GET /api/posting/payments/{postingPayment}/costs`
  - `GET /api/posting/payments/{postingPayment}`

### Receivable
- Setelah `POST /api/receivables/process`:
  - `GET /api/receivables`
  - `GET /api/reports/receivable/data?period=<periode>`
  - `GET /api/reports/receivable/process-detail?period=<periode>`
  - `GET /api/reports/receivable/summary`

### Print dan Protection
- Setelah `POST /api/print/invoices/batch`:
  - `GET /api/print/invoices/batch`
  - `GET /api/print/invoices/{invoice}/pdf`
- Setelah `POST /api/protections`:
  - `GET /api/protections`
  - `GET /api/protections/{protection}`
  - lalu test 1 endpoint write untuk pastikan return `423`

## 1.3) Setelah GET, Lanjut Apa
Bagian ini dipakai saat kamu sudah buka data dan bingung langkah berikutnya.

### Pola Umum
- Setelah `GET list`, lanjut ke `GET show` untuk salah satu ID hasil list.
- Setelah `GET show`, lanjut ke:
  - `PUT/PATCH` kalau mau test update
  - `DELETE` kalau mau test hapus
  - endpoint turunan kalau resource itu parent dari flow lain
- Setelah `GET search`, lanjut ke `GET show` dari hasil search untuk verifikasi data yang sama.

### Parent ke Child
- Setelah `GET /api/clients/{client}`:
  - lanjut `POST /api/work-orders` atau `POST /api/invoices`
- Setelah `GET /api/products/{product}`:
  - lanjut `POST /api/work-orders`
- Setelah `GET /api/items/{item}`:
  - lanjut `POST /api/work-orders`
- Setelah `GET /api/banks/{bank}`:
  - lanjut `POST /api/invoices` atau `POST /api/posting/payments`
- Setelah `GET /api/invoice-types/{invoice_type}`:
  - lanjut `POST /api/invoices`
- Setelah `GET /api/master-item-products/{master_item_product}`:
  - lanjut `POST /api/invoice-items`
- Setelah `GET /api/work-orders/{work_order}`:
  - lanjut `POST /api/work-orders/{work_order}/assign-team`
  - lanjut `POST /api/installations`
  - lanjut `POST /api/licenses`
  - lanjut `POST /api/stop-licenses`
- Setelah `GET /api/invoices/{invoice}`:
  - lanjut `POST /api/invoice-items`
  - lanjut `POST /api/debit-credit-notes`
  - lanjut `POST /api/payments`
  - lanjut `POST /api/posting/omzet`
  - lanjut `POST /api/posting/payments`
- Setelah `GET /api/posting/payments/{postingPayment}`:
  - lanjut `GET /api/posting/payments/{postingPayment}/costs`
  - lanjut `POST /api/posting/payments/{postingPayment}/costs`
  - lanjut `GET /api/print/receipts/{posting_payment}/pdf`

## 2) Struktur Folder Collection
- 01 Auth
- 02 Lookups
- 03 Setup
- 04 Master Data
- 05 Sales
- 06 Finance
- 07 Posting
- 08 Receivable
- 09 Reports
- 10 Print
- 11 Protection

## 3) Step-by-Step Endpoint Lengkap (Request + Response + Penjelasan)
Total endpoint: 154

### 01 Auth

#### login

##### POST /api/login
Deskripsi: Login user dan ambil bearer token untuk semua endpoint private.
Request Body:
```json
{
  "email": "superadmin@fitart.co.id",
  "password": "superadmin123"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Login successful",
  "token": "1|xxxxxxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "superadmin@fitart.co.id",
    "role": "super_admin",
    "status": "active"
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": [
      "The provided credentials are incorrect."
    ]
  }
}
```

#### logout

##### POST /api/logout
Deskripsi: Logout token aktif. Endpoint ini tidak butuh request body.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### me

##### GET /api/me
Deskripsi: Ambil profil user yang sedang login.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "superadmin@fitart.co.id",
    "role": "super_admin",
    "status": "active"
  }
}
```
Response Error Auth (contoh 401):
```json
{
  "message": "Unauthenticated."
}
```

### 02 Lookups

#### banks

##### GET /api/banks/search?q=BM01
Deskripsi: Lookup bank berdasarkan kode bank, nama bank, atau nomor rekening.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "BM01",
      "name": "BANK BCA 5600247257",
      "account_number": "5600247257",
      "account_name": "PT Integrasi Media Nusantara",
      "type": "M",
      "acc_code": "1201",
      "acc_code_name": "Bank BCA",
      "cdf_code": "1201",
      "cdf_code_name": "Bank BCA",
      "is_active": true
    }
  ]
}
```

#### chart-of-accounts

##### GET /api/chart-of-accounts
Deskripsi: Ambil data endpoint chart-of-accounts.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/chart-of-accounts/:chartOfAccount
Deskripsi: Ambil data endpoint chart-of-accounts.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/chart-of-accounts/search?q=1532
Deskripsi: Ambil data endpoint chart-of-accounts.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### clients

##### GET /api/clients/search
Deskripsi: Ambil data endpoint clients.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### companies

##### GET /api/companies/search
Deskripsi: Ambil data endpoint companies.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### invoice-types

##### GET /api/invoice-types/search?q=504
Deskripsi: Ambil data endpoint invoice-types.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### items

##### GET /api/items/search?q=1
Deskripsi: Ambil data endpoint items.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### master-item-products

##### GET /api/master-item-products/search
Deskripsi: Ambil data endpoint master-item-products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### product-groups

##### GET /api/product-groups/search?q=LSA
Deskripsi: Ambil data endpoint product-groups.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### products

##### GET /api/products/search
Deskripsi: Ambil data endpoint products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### team-members

##### GET /api/team-members/search?q=03
Deskripsi: Ambil data endpoint team-members.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 03 Setup

#### company-settings

##### GET /api/company/settings
Deskripsi: Ambil data endpoint company.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/company/settings
Deskripsi: Buat/proses data pada endpoint company.
Request Body:
```json
{
    "company_name":  "PT.INTEGRASI MEDIA NUSANTARA",
    "address":  "Puri Regency Business Center Jl. Puri Jembangan Baru III/16",
    "city":  "Surabaya",
    "phone":  "031-8479035",
    "npwp":  "68.900.534.1-609.000",
    "period_start":  "2026-01-01",
    "acc_ppn_kes":  "3213",
    "acc_ppn_mas":  "3214",
    "acc_discount":  "5090",
    "bank1":  "BANK CENTRAL ASIA (BCA)",
    "bank1_sn":  "PT Integrasi Media Nusantara",
    "bank1_ac":  "5600247257",
    "bank2":  "BANK MANDIRI",
    "bank2_sn":  "PT Integrasi Media Nusantara",
    "bank2_ac":  "9358201-4"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### tax-series

##### DELETE /api/tax-series/:taxSeries
Deskripsi: Hapus data pada endpoint tax-series.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/tax-series
Deskripsi: Ambil data endpoint tax-series.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/tax-series
Deskripsi: Buat/proses data pada endpoint tax-series.
Request Body:
```json
{
    "filled_date":  "2026-03-11",
    "period":  "03-2026",
    "tax_code":  "010",
    "start_number":  "04239178",
    "end_number":  "04239205",
    "current_number":  "04239178",
    "ppn_percentage":  11,
    "dpp_percentage":  1.11
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/tax-series/:taxSeries/next
Deskripsi: Buat/proses data pada endpoint tax-series.
Request Body:
```json
{
    "id":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 04 Master Data

#### banks

##### DELETE /api/banks/:bank
Deskripsi: Hapus data pada endpoint banks.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/banks
Deskripsi: Ambil data endpoint banks.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/banks/:bank
Deskripsi: Ambil data endpoint banks.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/banks
Deskripsi: Buat/proses data pada endpoint banks.
Request Body:
```json
{
    "code":  "BM01",
    "name":  "BANK BCA 5600247257",
    "account_number":  "5600247257",
    "account_name":  "PT Integrasi Media Nusantara",
    "type":  "M",
    "acc_code":  "1201",
    "cdf_code":  "1201",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/banks/:bank
Deskripsi: Update penuh data pada endpoint banks.
Request Body:
```json
{
    "code":  "BM01",
    "name":  "BANK BCA 5600247257 UPDATED",
    "account_number":  "5600247257",
    "account_name":  "PT Integrasi Media Nusantara",
    "type":  "M",
    "acc_code":  "1201",
    "cdf_code":  "1201",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### clients

##### DELETE /api/clients/:client
Deskripsi: Hapus data pada endpoint clients.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/clients
Deskripsi: Ambil data endpoint clients.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/clients/:client
Deskripsi: Ambil data endpoint clients.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/clients
Deskripsi: Buat/proses data pada endpoint clients.
Request Body:
```json
{
    "code":  "00054",
    "status":  "NON GROUP",
    "name":  "FITNESS PLUS INDONESIA",
    "address":  "Jl. Raya Darmo No. 88, Surabaya",
    "city":  "Surabaya",
    "phone":  "031-5678901",
    "fax":  "031-5678902",
    "npwp":  "02.345.678.9-012.000",
    "npkp":  "PKP-00054",
    "tax_name":  "FITNESS PLUS INDONESIA",
    "tax_address":  "Jl. Raya Darmo No. 88, Surabaya",
    "credit_term_days":  30,
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/clients/:client
Deskripsi: Update penuh data pada endpoint clients.
Request Body:
```json
{
    "code":  "00054",
    "status":  "NON GROUP",
    "name":  "FITNESS PLUS INDONESIA UPDATED",
    "address":  "Jl. Raya Darmo No. 88, Surabaya",
    "city":  "Surabaya",
    "phone":  "031-5678910",
    "fax":  "031-5678902",
    "npwp":  "02.345.678.9-012.000",
    "npkp":  "PKP-00054",
    "tax_name":  "FITNESS PLUS INDONESIA",
    "tax_address":  "Jl. Raya Darmo No. 88, Surabaya",
    "credit_term_days":  30,
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### companies

##### DELETE /api/companies/:company
Deskripsi: Hapus data pada endpoint companies.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/companies
Deskripsi: Ambil data endpoint companies.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/companies/:company
Deskripsi: Ambil data endpoint companies.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/companies
Deskripsi: Buat/proses data pada endpoint companies.
Request Body:
```json
{
    "code":  "IMN01",
    "name":  "PT.INTEGRASI MEDIA NUSANTARA",
    "address":  "Puri Regency Business Center Jl. Puri Jembangan Baru III/16",
    "address_invoice":  "Puri Regency Business Center Jl. Puri Jembangan Baru III/16",
    "city":  "Surabaya",
    "city_invoice":  "Surabaya",
    "phone":  "031-8479035",
    "fax":  "031-8479035",
    "email":  "info@imnn.co.id",
    "website":  "https://www.imnn.co.id",
    "npwp":  "68.900.534.1-609.000",
    "npkp":  "PKP-IMN01",
    "tax_name":  "PT.INTEGRASI MEDIA NUSANTARA",
    "tax_position":  "DIREKTUR",
    "invoice_name":  "EDY PRANOTO",
    "invoice_position":  "MANAGER",
    "invoice_name_2":  "IMANG INDAH AYUNINGRUM",
    "invoice_position_2":  "ADMINISTRASI",
    "invoice_tolerance_days":  "60",
    "upgrade_days":  "5",
    "letterhead_top":  "PT.INTEGRASI MEDIA NUSANTARA | Information Technology Consultant · Hardware · Software",
    "letterhead_bottom":  "Head Office: IX International Expo Groundfloor 3rd Suite JL. Ahmad Yani 99 Surabaya"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/companies/:company
Deskripsi: Update penuh data pada endpoint companies.
Request Body:
```json
{
    "code":  "IMN01",
    "name":  "PT.INTEGRASI MEDIA NUSANTARA",
    "address":  "Puri Regency Business Center Jl. Puri Jembangan Baru III/16",
    "address_invoice":  "Puri Regency Business Center Jl. Puri Jembangan Baru III/16",
    "city":  "Surabaya",
    "city_invoice":  "Surabaya",
    "phone":  "031-8479035",
    "fax":  "031-8479035",
    "email":  "info@imnn.co.id",
    "website":  "https://www.imnn.co.id",
    "npwp":  "68.900.534.1-609.000",
    "npkp":  "PKP-IMN01",
    "tax_name":  "PT.INTEGRASI MEDIA NUSANTARA",
    "tax_position":  "DIREKTUR",
    "invoice_name":  "EDY PRANOTO",
    "invoice_position":  "MANAGER",
    "invoice_name_2":  "IMANG INDAH AYUNINGRUM",
    "invoice_position_2":  "ADMINISTRASI",
    "invoice_tolerance_days":  "60",
    "upgrade_days":  "5",
    "letterhead_top":  "PT.INTEGRASI MEDIA NUSANTARA | Information Technology Consultant · Hardware · Software",
    "letterhead_bottom":  "Head Office: IX International Expo Groundfloor 3rd Suite JL. Ahmad Yani 99 Surabaya"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### invoice-types

##### DELETE /api/invoice-types/:invoice_type
Deskripsi: Hapus data pada endpoint invoice-types.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/invoice-types
Deskripsi: Ambil data endpoint invoice-types.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/invoice-types/:invoice_type
Deskripsi: Ambil data endpoint invoice-types.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/invoice-types
Deskripsi: Buat/proses data pada endpoint invoice-types.
Request Body:
```json
{
    "code":  "S04",
    "name":  "PENDAPATAN LICENSE",
    "is_license":  true,
    "auto_create_number":  true,
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/invoice-types/:invoiceType/toggle-status
Deskripsi: Buat/proses data pada endpoint invoice-types.
Request Body:
```json
{
    "id":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/invoice-types/:invoice_type
Deskripsi: Update penuh data pada endpoint invoice-types.
Request Body:
```json
{
    "code":  "S04",
    "name":  "PENDAPATAN LICENSE BULANAN",
    "is_license":  true,
    "auto_create_number":  true,
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### items

##### DELETE /api/items/:item
Deskripsi: Hapus data pada endpoint items.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/items
Deskripsi: Ambil data endpoint items.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/items/:item
Deskripsi: Ambil data endpoint items.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/items
Deskripsi: Buat/proses data pada endpoint items.
Request Body:
```json
{
    "code":  "2",
    "name":  "LICENSE",
    "acc_omzet":  "5033",
    "acc_piutang":  "1535",
    "cdf_omzet":  "5033",
    "cdf_piutang":  "1535",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/items/:item
Deskripsi: Update penuh data pada endpoint items.
Request Body:
```json
{
    "code":  "2",
    "name":  "LICENSE",
    "acc_omzet":  "5033",
    "acc_piutang":  "1535",
    "cdf_omzet":  "5033",
    "cdf_piutang":  "1535",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### master-item-products

##### DELETE /api/master-item-products/:master_item_product
Deskripsi: Hapus data pada endpoint master-item-products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/master-item-products
Deskripsi: Ambil data endpoint master-item-products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/master-item-products/:master_item_product
Deskripsi: Ambil data endpoint master-item-products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/master-item-products
Deskripsi: Buat/proses data pada endpoint master-item-products.
Request Body:
```json
{
    "code":  "LSA",
    "name":  "LISENSI APLIKASI SIGMA",
    "unit":  "UNIT",
    "price":  0,
    "acc_omzet":  "5033",
    "acc_omzet_np":  "5033",
    "acc_piutang":  "1535",
    "cdf_piutang":  "1535",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/master-item-products/:masterItemProduct/toggle-status
Deskripsi: Buat/proses data pada endpoint master-item-products.
Request Body:
```json
{
    "id":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/master-item-products/:master_item_product
Deskripsi: Update penuh data pada endpoint master-item-products.
Request Body:
```json
{
    "code":  "LSA",
    "name":  "LISENSI APLIKASI SIGMA",
    "unit":  "UNIT",
    "price":  0,
    "acc_omzet":  "5033",
    "acc_omzet_np":  "5033",
    "acc_piutang":  "1535",
    "cdf_piutang":  "1535",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### product-groups

##### DELETE /api/product-groups/:productGroup
Deskripsi: Hapus data pada endpoint product-groups.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/product-groups
Deskripsi: Ambil data endpoint product-groups.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/product-groups/:productGroup
Deskripsi: Ambil data endpoint product-groups.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/product-groups
Deskripsi: Buat/proses data pada endpoint product-groups.
Request Body:
```json
{
    "code":  "LSA",
    "name":  "LISENSI APLIKASI SIGMA",
    "acc_omzet":  "5033",
    "cdf_piutang":  "1535",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/product-groups/:productGroup
Deskripsi: Update penuh data pada endpoint product-groups.
Request Body:
```json
{
    "code":  "LSA",
    "name":  "LISENSI APLIKASI SIGMA",
    "acc_omzet":  "5033",
    "cdf_piutang":  "1535",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### products

##### DELETE /api/products/:product
Deskripsi: Hapus data pada endpoint products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/products
Deskripsi: Ambil data endpoint products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/products/:product
Deskripsi: Ambil data endpoint products.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/products
Deskripsi: Buat/proses data pada endpoint products.
Request Body:
```json
{
    "code":  "01",
    "name":  "KEUANGAN (SIGMA FAST)",
    "specification":  "ACCOUNTING DAN KEUANGAN",
    "description":  "Produk lisensi aplikasi keuangan",
    "author_code":  "03",
    "compiler":  "DELPHI 6",
    "year":  "2006",
    "product_group_id":  1,
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/products/:product
Deskripsi: Update penuh data pada endpoint products.
Request Body:
```json
{
    "code":  "01",
    "name":  "KEUANGAN (SIGMA FAST)",
    "specification":  "ACCOUNTING DAN KEUANGAN",
    "description":  "Produk lisensi aplikasi keuangan updated",
    "author_code":  "03",
    "compiler":  "DELPHI 6",
    "year":  "2006",
    "product_group_id":  1,
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### team-members

##### DELETE /api/team-members/:team_member
Deskripsi: Hapus data pada endpoint team-members.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/team-members
Deskripsi: Ambil data endpoint team-members.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/team-members/:team_member
Deskripsi: Ambil data endpoint team-members.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/team-members
Deskripsi: Buat/proses data pada endpoint team-members.
Request Body:
```json
{
    "code":  "03",
    "name":  "IMANG INDAH AYUNINGRUM",
    "position":  "ADMINISTRASI",
    "status":  "STATUS1",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/team-members/:team_member
Deskripsi: Update penuh data pada endpoint team-members.
Request Body:
```json
{
    "code":  "03",
    "name":  "IMANG INDAH AYUNINGRUM",
    "position":  "ADMINISTRASI",
    "status":  "STATUS1",
    "is_active":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### users

##### DELETE /api/users/:user
Deskripsi: Hapus data pada endpoint users.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/users
Deskripsi: Ambil data endpoint users.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/users/:user
Deskripsi: Ambil data endpoint users.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/users
Deskripsi: Buat/proses data pada endpoint users.
Request Body:
```json
{
    "name":  "Finance Admin JPAS",
    "email":  "financeadmin@imnn.co.id",
    "password":  "secret123",
    "role":  "finance_admin",
    "status":  "active"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/users/:user/assign-role
Deskripsi: Buat/proses data pada endpoint users.
Request Body:
```json
{
    "role":  "sales_operator"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/users/:user/toggle-status
Deskripsi: Buat/proses data pada endpoint users.
Request Body:
```json
{
    "id":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/users/:user
Deskripsi: Update penuh data pada endpoint users.
Request Body:
```json
{
    "name":  "Finance Admin JPAS Updated",
    "email":  "financeadmin@imnn.co.id",
    "password":  "secret123",
    "role":  "finance_admin",
    "status":  "active"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 05 Sales

#### installations

##### DELETE /api/installations/:installation
Deskripsi: Hapus data pada endpoint installations.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/installations
Deskripsi: Ambil data endpoint installations.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/installations/:installation
Deskripsi: Ambil data endpoint installations.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/installations
Deskripsi: Buat/proses data pada endpoint installations.
Request Body:
```json
{
    "work_order_id":  1,
    "client_id":  1,
    "install_date":  "2026-03-10",
    "implementor_1":  "Budi",
    "implementor_2":  "Rina",
    "implementor_3":  "Dedi Pratama",
    "notes":  "Go live"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/installations/:installation
Deskripsi: Update penuh data pada endpoint installations.
Request Body:
```json
{
    "work_order_id":  1,
    "client_id":  1,
    "install_date":  "2026-03-10",
    "implementor_1":  "EDY PRANOTO",
    "implementor_2":  "ADIT",
    "implementor_3":  "Dedi Pratama",
    "notes":  "Instalasi modul keuangan berjalan lancar"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### licenses

##### DELETE /api/licenses/:license
Deskripsi: Hapus data pada endpoint licenses.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/licenses
Deskripsi: Ambil data endpoint licenses.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/licenses/:license
Deskripsi: Ambil data endpoint licenses.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/licenses
Deskripsi: Buat/proses data pada endpoint licenses.
Request Body:
```json
{
    "license_number":  "LSA-2026-0001",
    "client_id":  1,
    "work_order_id":  1,
    "license_date":  "2026-03-10",
    "due_date":  "2026-04-09",
    "subtotal":  375000,
    "discount":  0,
    "tax":  41250,
    "total":  416250,
    "status":  "unpaid",
    "notes":  "License modul keuangan Maret 2026"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/licenses/:license
Deskripsi: Update penuh data pada endpoint licenses.
Request Body:
```json
{
    "license_number":  "LSA-2026-0001",
    "client_id":  1,
    "work_order_id":  1,
    "license_date":  "2026-03-10",
    "due_date":  "2026-04-09",
    "subtotal":  375000,
    "discount":  0,
    "tax":  41250,
    "total":  416250,
    "status":  "unpaid",
    "notes":  "License modul keuangan Maret 2026"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### stop-licenses

##### DELETE /api/stop-licenses/:stop_license
Deskripsi: Hapus data pada endpoint stop-licenses.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/stop-licenses
Deskripsi: Ambil data endpoint stop-licenses.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/stop-licenses/:stop_license
Deskripsi: Ambil data endpoint stop-licenses.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/stop-licenses
Deskripsi: Buat/proses data pada endpoint stop-licenses.
Request Body:
```json
{
    "number":  "001/MIS-03/26",
    "date":  "2026-03-20",
    "stop_date":  "2026-03-01",
    "work_order_id":  1,
    "client_spv_code":  "03",
    "notes":  "BERHENTI",
    "is_stopped":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/stop-licenses/:stop_license
Deskripsi: Update penuh data pada endpoint stop-licenses.
Request Body:
```json
{
    "number":  "001/MIS-03/26",
    "date":  "2026-03-20",
    "stop_date":  "2026-03-01",
    "work_order_id":  1,
    "client_spv_code":  "03",
    "notes":  "BERHENTI",
    "is_stopped":  true
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### work-orders

##### DELETE /api/work-orders/:work_order
Deskripsi: Hapus data pada endpoint work-orders.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/work-orders
Deskripsi: Ambil data endpoint work-orders.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/work-orders/:work_order
Deskripsi: Ambil data endpoint work-orders.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/work-orders
Deskripsi: Buat/proses data pada endpoint work-orders.
Request Body:
```json
{
    "number":  "001/MIS-JP/0326",
    "date":  "2026-03-06",
    "date_install":  "2026-03-10",
    "start_license":  "2026-03-10",
    "client_id":  1,
    "product_id":  1,
    "version":  "6.0",
    "item_id":  1,
    "status":  "AKTIF",
    "amount":  375000,
    "description":  "Implementasi modul keuangan",
    "item_count":  1,
    "per_unit":  "UNIT",
    "notes":  "SIGMA FAST FITNESS PLUS INDONESIA",
    "team":  [
                 {
                     "role":  "implementor_1",
                     "team_member_id":  1
                 },
                 {
                     "role":  "programmer",
                     "team_member_id":  2
                 }
             ]
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/work-orders/:work_order/assign-team
Deskripsi: Buat/proses data pada endpoint work-orders.
Request Body:
```json
{
    "team":  [
                 {
                     "role":  "implementor_1",
                     "team_member_id":  1
                 },
                 {
                     "role":  "programmer",
                     "team_member_id":  2
                 }
             ]
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/work-orders/:work_order
Deskripsi: Update penuh data pada endpoint work-orders.
Request Body:
```json
{
    "number":  "001/MIS-JP/0326",
    "date":  "2026-03-06",
    "date_install":  "2026-03-10",
    "start_license":  "2026-03-10",
    "client_id":  1,
    "product_id":  1,
    "version":  "6.0",
    "item_id":  1,
    "status":  "AKTIF",
    "amount":  375000,
    "description":  "Implementasi modul keuangan",
    "item_count":  1,
    "per_unit":  "UNIT",
    "notes":  "SIGMA FAST FITNESS PLUS INDONESIA",
    "team":  [
                 {
                     "role":  "implementor_1",
                     "team_member_id":  1
                 },
                 {
                     "role":  "programmer",
                     "team_member_id":  2
                 }
             ]
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 06 Finance

#### debit-credit-notes
##### GET /api/debit-credit-notes
Deskripsi: List `Nota Debet/Kredit` sesuai tab `List` JPAS. Filter mendukung tanggal, nomor nota, nama klien, dan `q`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "filter": {
    "date_from": null,
    "date_to": null,
    "number": null,
    "client_name": null,
    "q": null
  },
  "total": 1,
  "data": [
    {
      "note_id": 1,
      "type": "D",
      "number": "001/NDK/1025",
      "date": "2025-10-22",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "description": "PENYESUAIAN BIAYA TAMBAHAN SUPPORT",
      "nominal_dpp": 100000,
      "nominal_ppn": 11000
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "date_from": ["The date from is not a valid date."]
  }
}
```

##### GET /api/debit-credit-notes/search
Deskripsi: Search `Nota Debet/Kredit` sesuai field design JPAS.
Request Body: none
Contoh Query:
```text
/api/debit-credit-notes/search?date_from=2025-10-01&date_to=2025-10-31
/api/debit-credit-notes/search?number=001/NDK/1025
/api/debit-credit-notes/search?client_name=Prambanan
```
Response Sukses (contoh):
```json
{
  "success": true,
  "filter": {
    "date_from": "2025-10-01",
    "date_to": "2025-10-31",
    "number": null,
    "client_name": null,
    "q": null
  },
  "total": 1,
  "data": [
    {
      "note_id": 1,
      "type": "D",
      "number": "001/NDK/1025",
      "date": "2025-10-22",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "description": "PENYESUAIAN BIAYA TAMBAHAN SUPPORT",
      "nominal_dpp": 100000,
      "nominal_ppn": 11000
    }
  ]
}
```

##### GET /api/debit-credit-notes/:debit_credit_note
Deskripsi: Detail `Nota Debet/Kredit` sesuai tab `Detail`, termasuk item lines.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": {
    "header": {
      "id": 1,
      "jenis": "D",
      "number": "001/NDK/1025",
      "date": "2025-10-22",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "description": "PENYESUAIAN BIAYA TAMBAHAN SUPPORT",
      "auto_journal": true,
      "detail_invoice": "001/504/LP/1025",
      "total_dpp": 100000,
      "total_ppn": 11000,
      "total_amount": 111000,
      "is_posted": false
    },
    "items": [
      {
        "id": 1,
        "sequence": 1,
        "item_code": "006",
        "item_name": "PRODUK LAIN-LAIN",
        "description": "Tambahan support server",
        "dpp_nota": 100000,
        "ppn_nota": 11000
      }
    ]
  }
}
```

##### GET /api/debit-credit-notes/:debitCreditNote/summary-journal
Deskripsi: Summary jurnal `Nota Debet/Kredit`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "note_number": "001/NDK/1025",
  "total_debit": 111000,
  "total_credit": 111000,
  "data": [
    {
      "no_bukti": "001/NDK/1025",
      "tanggal": "2025-10-22",
      "acc": "1513",
      "keterangan": "Nota debet tambahan support",
      "debet": 111000,
      "kredit": 0
    }
  ]
}
```

##### POST /api/debit-credit-notes
Deskripsi: Buat `Nota Debet/Kredit` dengan item lines seperti form JPAS `Add Item`.
Setelah test POST ini, lanjutkan:
- `GET /api/debit-credit-notes`
- `GET /api/debit-credit-notes/{debit_credit_note}`
- `GET /api/debit-credit-notes/search?number=<nomor nota>`
- `GET /api/debit-credit-notes/{debitCreditNote}/summary-journal`
Request Body:
```json
{
  "type": "D",
  "number": "001/NDK/1025",
  "date": "2025-10-22",
  "invoice_id": 1,
  "client_id": 1,
  "description": "PENYESUAIAN BIAYA TAMBAHAN SUPPORT",
  "auto_journal": true,
  "is_posted": false,
  "items": [
    {
      "sequence": 1,
      "item_code": "006",
      "item_name": "PRODUK LAIN-LAIN",
      "description": "Tambahan support server",
      "dpp_amount": 100000,
      "ppn_amount": 11000
    }
  ]
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Debit/Credit Note created successfully",
  "data": {
    "header": {
      "id": 1,
      "jenis": "D",
      "number": "001/NDK/1025",
      "date": "2025-10-22",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "description": "PENYESUAIAN BIAYA TAMBAHAN SUPPORT",
      "auto_journal": true,
      "detail_invoice": "001/504/LP/1025",
      "total_dpp": 100000,
      "total_ppn": 11000,
      "total_amount": 111000,
      "is_posted": false
    },
    "items": [
      {
        "id": 1,
        "sequence": 1,
        "item_code": "006",
        "item_name": "PRODUK LAIN-LAIN",
        "description": "Tambahan support server",
        "dpp_nota": 100000,
        "ppn_nota": 11000
      }
    ]
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "items.0.item_name": ["The items.0.item name field is required with items."]
  }
}
```

##### PUT /api/debit-credit-notes/:debit_credit_note
Deskripsi: Update penuh `Nota Debet/Kredit` beserta item lines.
Request Body:
```json
{
  "type": "D",
  "number": "001/NDK/1025",
  "date": "2025-10-22",
  "invoice_id": 1,
  "client_id": 1,
  "description": "PENYESUAIAN BIAYA TAMBAHAN SUPPORT - REVISI",
  "auto_journal": true,
  "is_posted": false,
  "items": [
    {
      "id": 1,
      "sequence": 1,
      "item_code": "006",
      "item_name": "PRODUK LAIN-LAIN",
      "description": "Tambahan support server - revisi",
      "dpp_amount": 125000,
      "ppn_amount": 13750
    }
  ]
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Debit/Credit Note updated successfully",
  "data": {
    "header": {
      "id": 1,
      "jenis": "D",
      "number": "001/NDK/1025",
      "date": "2025-10-22",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "description": "PENYESUAIAN BIAYA TAMBAHAN SUPPORT - REVISI",
      "auto_journal": true,
      "detail_invoice": "001/504/LP/1025",
      "total_dpp": 125000,
      "total_ppn": 13750,
      "total_amount": 138750,
      "is_posted": false
    },
    "items": [
      {
        "id": 1,
        "sequence": 1,
        "item_code": "006",
        "item_name": "PRODUK LAIN-LAIN",
        "description": "Tambahan support server - revisi",
        "dpp_nota": 125000,
        "ppn_nota": 13750
      }
    ]
  }
}
```

##### DELETE /api/debit-credit-notes/:debit_credit_note
Deskripsi: Hapus data pada endpoint debit-credit-notes.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Debit/Credit Note deleted successfully"
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### invoice-items

##### DELETE /api/invoice-items/:invoice_item
Deskripsi: Hapus data pada endpoint invoice-items.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/invoice-items
Deskripsi: Ambil data endpoint invoice-items.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "invoice_id": 1,
      "master_item_product_id": 1,
      "item_code": "01",
      "item_name": "LICENSE MODUL",
      "description": "LICENSE MODUL Biaya Server, License & Maintenance",
      "qty": 1,
      "unit": "UNIT",
      "price": "3000000.00",
      "bruto": "3000000.00",
      "months": 1
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/invoice-items/:invoice_item
Deskripsi: Ambil data endpoint invoice-items.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "invoice_id": 1,
    "master_item_product_id": 1,
    "item_code": "01",
    "item_name": "LICENSE MODUL",
    "description": "LICENSE MODUL Biaya Server, License & Maintenance",
    "qty": 1,
    "unit": "UNIT",
    "price": "3000000.00",
    "bruto": "3000000.00",
    "months": 1
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/invoice-items
Deskripsi: Tambah baris item ke invoice yang sudah dibuat.
Setelah test POST ini, lanjutkan:
- `GET /api/invoice-items`
- `GET /api/invoice-items/{invoice_item}`
- `GET /api/invoices/{invoice}`
- `GET /api/invoice-license/{invoice}` atau `GET /api/invoice-products/{invoice}`
Request Body:
```json
{
    "invoice_id":  1,
    "master_item_product_id":  1,
    "item_code":  "01",
    "item_name":  "LICENSE MODUL",
    "description":  "LICENSE MODUL Biaya Server, License & Maintenance",
    "qty":  1,
    "unit":  "UNIT",
    "price":  3000000,
    "months":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Invoice Item created successfully",
  "data": {
    "id": 1,
    "invoice_id": 1,
    "master_item_product_id": 1,
    "item_code": "01",
    "item_name": "LICENSE MODUL",
    "description": "LICENSE MODUL Biaya Server, License & Maintenance",
    "qty": 1,
    "unit": "UNIT",
    "price": "3000000.00",
    "bruto": "3000000.00",
    "months": 1
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/invoice-items/:invoice_item
Deskripsi: Update penuh data pada endpoint invoice-items.
Request Body:
```json
{
    "invoice_id":  1,
    "master_item_product_id":  1,
    "item_code":  "01",
    "item_name":  "LICENSE MODUL",
    "description":  "LICENSE MODUL Biaya Server, License & Maintenance - REVISI",
    "qty":  1,
    "unit":  "UNIT",
    "price":  3000000,
    "months":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Invoice Item updated successfully",
  "data": {
    "id": 1,
    "invoice_id": 1,
    "master_item_product_id": 1,
    "item_code": "01",
    "item_name": "LICENSE MODUL",
    "description": "LICENSE MODUL Biaya Server, License & Maintenance - REVISI",
    "qty": 1,
    "unit": "UNIT",
    "price": "3000000.00",
    "bruto": "3000000.00",
    "months": 1
  },
  "was_changed": true,
  "changes": {
    "description": "LICENSE MODUL Biaya Server, License & Maintenance - REVISI"
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### invoices

##### GET /api/invoice-license
Deskripsi: List/Daftar `INVOICE LICENSE` sesuai tab `List / Daftar` pada design JPAS. Filter yang mengikuti design: tanggal invoice, nomor invoice, nama klien.
Request Body: none
Contoh Query:
```text
/api/invoice-license?date_from=2025-10-01&date_to=2025-10-22
/api/invoice-license?number=001/504/LP/1025
/api/invoice-license?client_name=Prambanan
```
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_license",
  "mode": "list",
  "filter": {
    "date_from": "2025-10-01",
    "date_to": "2025-10-22",
    "number": null,
    "client_name": null,
    "q": null
  },
  "total": 3,
  "data": [
    {
      "invoice_id": 1,
      "number": "001/504/LP/1025",
      "date": "2025-10-11",
      "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
      "client": "PT Prambanan Dwipaka",
      "jumlah": 3000000,
      "ppn": 330000,
      "total": 3330000,
      "invoice_type_code": "504",
      "invoice_type_name": "PENDAPATAN LICENSE"
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "date_from": ["The date from is not a valid date."]
  }
}
```

##### GET /api/invoice-license/search
Deskripsi: Search `INVOICE LICENSE` dengan field yang sama seperti tab list JPAS ditambah `q` untuk pencarian umum nomor/keterangan/nama klien.
Request Body: none
Contoh Query:
```text
/api/invoice-license/search?q=Prambanan
/api/invoice-license/search?number=001/504/LP/1025
/api/invoice-license/search?client_name=PUBLIC
```
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_license",
  "mode": "search",
  "filter": {
    "date_from": null,
    "date_to": null,
    "number": null,
    "client_name": null,
    "q": "Prambanan"
  },
  "total": 1,
  "data": [
    {
      "invoice_id": 1,
      "number": "001/504/LP/1025",
      "date": "2025-10-11",
      "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
      "client": "PT Prambanan Dwipaka",
      "jumlah": 3000000,
      "ppn": 330000,
      "total": 3330000,
      "invoice_type_code": "504",
      "invoice_type_name": "PENDAPATAN LICENSE"
    }
  ]
}
```

##### GET /api/invoice-license/:invoice
Deskripsi: Detail `INVOICE LICENSE` sesuai tab `Detail` pada design JPAS, termasuk sub-form item dan data pajak/header.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_license",
  "form": {
    "title": "Entri Invoice [Edit] - INVOICE LICENSE",
    "tabs": ["Detail", "List / Daftar", "Summary Jurnal"],
    "sub_tabs": ["Detail Item", "Jurnal"]
  },
  "data": {
    "header": {
      "invoice_id": 1,
      "jenis_invoice_code": "504",
      "jenis_invoice_name": "PENDAPATAN LICENSE",
      "number": "001/504/LP/1025",
      "date": "2025-10-11",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "address": "Jl. Ngagel Jaya Tengah No.26 Surabaya",
      "due_date": "2025-10-11",
      "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
      "jenis_faktur": "T",
      "instansi": "U",
      "no_f_pajak": "010.000-25.12345678",
      "tgl_f_pajak": "2025-10-11",
      "no_bm_km": "BM-KM4",
      "tgl_bm_km": "2025-10-11",
      "jumlah": 3000000,
      "discount": 0,
      "dpp": 3000000,
      "ppn_percentage": 11,
      "ppn": 330000,
      "dp": 0,
      "other": 0,
      "total": 3330000,
      "include_ppn": true,
      "auto_journal": true,
      "use_old_letterhead": false,
      "is_paid": false,
      "is_posted": false
    },
    "detail_items": [
      {
        "invoice_item_id": 1,
        "kd_item": "01",
        "nama_item": "LICENSE MODUL",
        "keterangan": "LICENSE MODUL Biaya Server, License & Maintenance",
        "qty": 1,
        "satuan": "UNIT",
        "harga": 3000000,
        "bruto": 3000000,
        "bulan": 1
      }
    ],
    "pajak": {
      "no_faktur_pajak": "010.000-25.12345678",
      "tgl_faktur_pajak": "2025-10-11",
      "ppn": 330000,
      "ppn_percentage": 11
    },
    "summary_jurnal": [
      {
        "no_bukti": "001/504/LP/1025",
        "tgl": "2025-10-11",
        "seq": "001",
        "kode_acc": "1535",
        "keterangan": "Piutang License Oktober 2025",
        "debet": 3330000,
        "kredit": 0
      },
      {
        "no_bukti": "001/504/LP/1025",
        "tgl": "2025-10-11",
        "seq": "002",
        "kode_acc": "5033",
        "keterangan": "Omzet License Oktober 2025",
        "debet": 0,
        "kredit": 3000000
      }
    ],
    "summary_jurnal_totals": {
      "debet": 3330000,
      "kredit": 3330000
    },
    "flow_hint": "license"
  }
}
```

##### GET /api/invoice-license/:invoice/summary-journal
Deskripsi: Ambil tab `Summary Jurnal` khusus `INVOICE LICENSE`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_license",
  "invoice_number": "001/504/LP/1025",
  "total_debit": 3330000,
  "total_credit": 3330000,
  "data": [
    {
      "no_bukti": "001/504/LP/1025",
      "tgl": "2025-10-11",
      "seq": "001",
      "kode_acc": "1535",
      "keterangan": "Piutang License Oktober 2025",
      "debet": 3330000,
      "kredit": 0
    }
  ]
}
```

##### GET /api/invoice-products
Deskripsi: List/Daftar `INVOICE PRODUK / NON LICENSE` sesuai tab `List` pada design JPAS.
Request Body: none
Contoh Query:
```text
/api/invoice-products?date_from=2025-09-01&date_to=2025-09-30
/api/invoice-products?number=001/505/PP/0925
/api/invoice-products?client_name=Prambanan
```
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_product",
  "mode": "list",
  "filter": {
    "date_from": "2025-09-01",
    "date_to": "2025-09-30",
    "number": null,
    "client_name": null,
    "q": null
  },
  "total": 1,
  "data": [
    {
      "invoice_id": 2,
      "number": "001/505/PP/0925",
      "date": "2025-09-02",
      "description": "UPGRADE JARINGAN LAN",
      "client": "PT Prambanan Dwipaka",
      "netto": 3013874,
      "no_bm_km": "KM-001",
      "tgl_bm_km": "2025-09-02",
      "invoice_type_code": "505",
      "invoice_type_name": "PEROLEHAN LAINNYA"
    }
  ]
}
```

##### GET /api/invoice-products/search
Deskripsi: Search `INVOICE PRODUK / NON LICENSE` berdasarkan tanggal, nomor, nama klien, atau `q`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_product",
  "mode": "search",
  "filter": {
    "date_from": null,
    "date_to": null,
    "number": null,
    "client_name": null,
    "q": "UPGRADE"
  },
  "total": 1,
  "data": [
    {
      "invoice_id": 2,
      "number": "001/505/PP/0925",
      "date": "2025-09-02",
      "description": "UPGRADE JARINGAN LAN",
      "client": "PT Prambanan Dwipaka",
      "netto": 3013874,
      "no_bm_km": "KM-001",
      "tgl_bm_km": "2025-09-02",
      "invoice_type_code": "505",
      "invoice_type_name": "PEROLEHAN LAINNYA"
    }
  ]
}
```

##### GET /api/invoice-products/:invoice
Deskripsi: Detail `INVOICE PRODUK / NON LICENSE` sesuai tab `Detail`, termasuk sub-tab `Detail Item`, `Pajak`, dan `Jurnal`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_product",
  "form": {
    "title": "Entri Invoice Produk [Edit] - INVOICE PRODUK / NON LICENSE",
    "tabs": ["Detail", "List", "Summary Jurnal"],
    "sub_tabs": ["Detail Item", "Pajak", "Jurnal"]
  },
  "data": {
    "header": {
      "invoice_id": 2,
      "jenis_invoice_code": "505",
      "jenis_invoice_name": "PEROLEHAN LAINNYA",
      "number": "001/505/PP/0925",
      "date": "2025-09-02",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "address": "Jl. Ngagel Jaya Tengah No.26 Surabaya",
      "due_date": "2025-09-02",
      "description": "UPGRADE JARINGAN LAN",
      "jenis_faktur": "T",
      "instansi": "U",
      "no_f_pajak": "010.000-25.76543210",
      "tgl_f_pajak": "2025-09-02",
      "no_bm_km": "KM-001",
      "tgl_bm_km": "2025-09-02",
      "jumlah": 3345400,
      "discount": 0,
      "dpp": 3013874,
      "ppn_percentage": 11,
      "ppn": 331526,
      "dp": 0,
      "other": 0,
      "total": 3345400,
      "include_ppn": true,
      "auto_journal": true,
      "use_old_letterhead": false,
      "is_paid": false,
      "is_posted": false
    },
    "detail_items": [
      {
        "invoice_item_id": 2,
        "kd_item": "006",
        "nama_item": "PRODUK LAIN-LAIN",
        "keterangan": "kekurangan / Termin ke 2 (dua) upgrade",
        "qty": 1,
        "satuan": "UNIT",
        "harga": 3345400,
        "bruto": 3345400,
        "bulan": 1
      }
    ],
    "pajak": {
      "no_faktur_pajak": "010.000-25.76543210",
      "tgl_faktur_pajak": "2025-09-02",
      "ppn": 331526,
      "ppn_percentage": 11
    },
    "summary_jurnal": [
      {
        "no_bukti": "001/505/PP/0925",
        "tgl": "2025-09-02",
        "seq": "001",
        "kode_acc": "1513",
        "keterangan": "006 kekurangan / Termin ke 2 (dua) upgrade jaringan",
        "debet": 3345400,
        "kredit": 0
      },
      {
        "no_bukti": "001/505/PP/0925",
        "tgl": "2025-09-02",
        "seq": "002",
        "kode_acc": "5019",
        "keterangan": "006 kekurangan / Termin ke 2 (dua) upgrade jaringan",
        "debet": 0,
        "kredit": 3013874
      }
    ],
    "summary_jurnal_totals": {
      "debet": 3345400,
      "kredit": 3345400
    },
    "flow_hint": "non_license"
  }
}
```

##### GET /api/invoice-products/:invoice/summary-journal
Deskripsi: Ambil tab `Summary Jurnal` khusus `INVOICE PRODUK / NON LICENSE`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "flow": "invoice_product",
  "invoice_number": "001/505/PP/0925",
  "total_debit": 3345400,
  "total_credit": 3345400,
  "data": [
    {
      "no_bukti": "001/505/PP/0925",
      "tgl": "2025-09-02",
      "seq": "001",
      "kode_acc": "1513",
      "keterangan": "006 kekurangan / Termin ke 2 (dua) upgrade jaringan",
      "debet": 3345400,
      "kredit": 0
    }
  ]
}
```

##### DELETE /api/invoices/:invoice
Deskripsi: Hapus data pada endpoint invoices.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Invoice deleted successfully"
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/invoices
Deskripsi: Ambil data endpoint invoices.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "number": "001/504/LP/1025",
      "invoice_type_id": 1,
      "client_id": 1,
      "bank_id": 1,
      "work_order_id": 1,
      "period": "2025-10",
      "date": "2025-10-11",
      "due_date": "2025-10-11",
      "tax_date": "2025-10-11",
      "tax_number": "010.000-25.12345678",
      "invoice_bm_km": "BM-KM4",
      "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
      "invoice_category": "LICENSE",
      "tax_type": "T",
      "instance": "U",
      "bruto": "3000000.00",
      "discount": "0.00",
      "dpp": "3000000.00",
      "ppn": "330000.00",
      "ppn_percentage": "11.00",
      "dp": "0.00",
      "other": "0.00",
      "total": "3330000.00",
      "include_ppn": true,
      "use_old_letterhead": false,
      "auto_journal": true,
      "is_paid": false,
      "is_posted": false
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/invoices/:invoice
Deskripsi: Ambil data endpoint invoices.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "number": "001/504/LP/1025",
    "invoice_type_id": 1,
    "client_id": 1,
    "bank_id": 1,
    "work_order_id": 1,
    "period": "2025-10",
    "date": "2025-10-11",
    "due_date": "2025-10-11",
    "tax_date": "2025-10-11",
    "tax_number": "010.000-25.12345678",
    "invoice_bm_km": "BM-KM4",
    "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
    "invoice_category": "LICENSE",
    "tax_type": "T",
    "instance": "U",
    "bruto": "3000000.00",
    "discount": "0.00",
    "dpp": "3000000.00",
    "ppn": "330000.00",
    "ppn_percentage": "11.00",
    "dp": "0.00",
    "other": "0.00",
    "total": "3330000.00",
    "include_ppn": true,
    "use_old_letterhead": false,
    "auto_journal": true,
    "is_paid": false,
    "is_posted": false
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/invoices
Deskripsi: Create header invoice. Setelah header terbentuk, lanjutkan tambah `invoice-items`.
Setelah test POST ini, lanjutkan:
- `GET /api/invoices`
- `GET /api/invoices/{invoice}`
- `GET /api/invoice-license/{invoice}` jika invoice category `LICENSE`
- `GET /api/invoice-license/search?number=<nomor invoice>` jika flow license
- `GET /api/invoice-products/{invoice}` jika invoice category produk/non-license
- `GET /api/invoice-products/search?number=<nomor invoice>` jika flow produk
- `GET /api/invoice-license/{invoice}/summary-journal` atau `GET /api/invoice-products/{invoice}/summary-journal`
Request Body:
```json
{
    "number":  "001/504/LP/1025",
    "invoice_type_id":  1,
    "client_id":  1,
    "bank_id":  1,
    "work_order_id":  1,
    "period":  "2025-10",
    "date":  "2025-10-11",
    "due_date":  "2025-10-11",
    "tax_date":  "2025-10-11",
    "tax_number":  "010.000-25.12345678",
    "tax_note": "Note / Keterangan Faktur Pajak",
    "invoice_note": "Note / Keterangan Invoice / Nota",
    "invoice_bm_km": "BM-KM4",
    "invoice_bm_km_date": "2025-10-11",
    "client_address": "Jl. Ngagel Jaya Tengah No.26 Surabaya",
    "description":  "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
    "invoice_category":  "LICENSE",
    "invoice_mode": "NORMAL",
    "tax_type":  "T",
    "instance": "U",
    "bruto":  3000000,
    "discount":  0,
    "dpp":  3000000,
    "ppn":  330000,
    "ppn_percentage":  11,
    "dp":  0,
    "other":  0,
    "total":  3330000,
    "include_ppn":  true,
    "use_old_letterhead": false,
    "without_payment_posting": false,
    "stamp_and_signature": true,
    "auto_journal":  true,
    "is_paid":  false
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Invoice created successfully",
  "data": {
    "id": 1,
    "number": "001/504/LP/1025",
    "invoice_type_id": 1,
    "client_id": 1,
    "bank_id": 1,
    "work_order_id": 1,
    "period": "2025-10",
    "date": "2025-10-11",
    "due_date": "2025-10-11",
    "tax_date": "2025-10-11",
    "tax_number": "010.000-25.12345678",
    "tax_note": "Note / Keterangan Faktur Pajak",
    "invoice_note": "Note / Keterangan Invoice / Nota",
    "invoice_bm_km": "BM-KM4",
    "invoice_bm_km_date": "2025-10-11",
    "client_address": "Jl. Ngagel Jaya Tengah No.26 Surabaya",
    "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
    "invoice_category": "LICENSE",
    "invoice_mode": "NORMAL",
    "tax_type": "T",
    "instance": "U",
    "bruto": "3000000.00",
    "discount": "0.00",
    "dpp": "3000000.00",
    "ppn": "330000.00",
    "ppn_percentage": "11.00",
    "dp": "0.00",
    "other": "0.00",
    "total": "3330000.00",
    "include_ppn": true,
    "use_old_letterhead": false,
    "without_payment_posting": false,
    "stamp_and_signature": true,
    "auto_journal": true,
    "is_paid": false,
    "is_posted": false
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/invoices/:invoice
Deskripsi: Update penuh data pada endpoint invoices.
Request Body:
```json
{
    "number":  "001/504/LP/1025",
    "invoice_type_id":  1,
    "client_id":  1,
    "bank_id":  1,
    "work_order_id":  1,
    "period":  "2025-10",
    "date":  "2025-10-11",
    "due_date":  "2025-10-11",
    "tax_date":  "2025-10-11",
    "tax_number":  "010.000-25.12345678",
    "tax_note": "Note / Keterangan Faktur Pajak - Revisi",
    "invoice_note": "Note / Keterangan Invoice / Nota - Revisi",
    "invoice_bm_km": "BM-KM4",
    "invoice_bm_km_date": "2025-10-11",
    "client_address": "Jl. Ngagel Jaya Tengah No.26 Surabaya",
    "description":  "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25 - REVISI",
    "invoice_category":  "LICENSE",
    "invoice_mode": "NORMAL",
    "tax_type":  "T",
    "instance": "U",
    "bruto":  3000000,
    "discount":  0,
    "dpp":  3000000,
    "ppn":  330000,
    "ppn_percentage":  11,
    "dp":  0,
    "other":  0,
    "total":  3330000,
    "include_ppn":  true,
    "use_old_letterhead": false,
    "without_payment_posting": false,
    "stamp_and_signature": true,
    "auto_journal":  true,
    "is_paid":  false
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Invoice updated successfully",
  "data": {
    "id": 1,
    "number": "001/504/LP/1025",
    "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25 - REVISI"
  },
  "was_changed": true,
  "changes": {
    "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25 - REVISI"
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### payments

##### DELETE /api/payments/:payment
Deskripsi: Hapus data pada endpoint payments.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/payments
Deskripsi: Ambil data endpoint payments.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/payments/:payment
Deskripsi: Ambil data endpoint payments.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/payments
Deskripsi: Input pembayaran atas invoice. Endpoint ini berbeda dengan `posting/payments`.
Setelah test POST ini, lanjutkan:
- `GET /api/payments`
- `GET /api/payments/{payment}`
- `GET /api/invoices/{invoice}` untuk cek invoice terkait
Request Body:
```json
{
    "number":  "001/MIS/T026",
    "date":  "2026-03-15",
    "invoice_id":  1,
    "client_id":  1,
    "description":  "PEMBAYARAN TRANSFER BCA",
    "amount":  416250
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/payments/:payment
Deskripsi: Update penuh data pada endpoint payments.
Request Body:
```json
{
    "number":  "001/MIS/T026",
    "date":  "2026-03-15",
    "invoice_id":  1,
    "client_id":  1,
    "description":  "PEMBAYARAN TRANSFER BCA",
    "amount":  416250
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### recurring

##### POST /api/recurring/invoices/generate
Deskripsi: Generate invoice recurring bulanan dari parameter periode.
Setelah test POST ini, lanjutkan:
- `GET /api/invoices`
- `GET /api/invoice-license`
- `GET /api/invoice-license/search?date_from=<awal periode>&date_to=<akhir periode>`
Request Body:
```json
{
    "period":  "2026-03",
    "invoice_type_id":  1,
    "bank_id":  1,
    "ppn_percentage":  11
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 07 Posting

#### journals

##### GET /api/journals
Deskripsi: Ambil data endpoint journals.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### posting-omzet

##### POST /api/posting/omzet
Deskripsi: Posting omzet/jurnal dari invoice tertentu.
Setelah test POST ini, lanjutkan:
- `GET /api/journals?document_number=<nomor invoice>`
Request Body:
```json
{
    "invoice_id":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### posting-payments
##### GET /api/posting/payments
Deskripsi: List `Posting Pembayaran Invoice` sesuai tab `List` JPAS. Mendukung filter tanggal invoice, tanggal posting, no posting, keterangan, nama klien.
Request Body: none
Contoh Query:
```text
/api/posting/payments?posting_date_from=2025-09-01&posting_date_to=2025-09-30
/api/posting/payments?number=BM01-JA001/0925
/api/posting/payments?client_name=FITNESS
```
Response Sukses (contoh):
```json
{
  "success": true,
  "filter": {
    "invoice_date_from": null,
    "invoice_date_to": null,
    "posting_date_from": "2025-09-01",
    "posting_date_to": "2025-09-30",
    "number": null,
    "description": null,
    "client_name": null,
    "q": null
  },
  "total": 1,
  "data": [
    {
      "posting_id": 1,
      "number": "BM01-JA001/0925",
      "posting_date": "2025-09-01",
      "description": "PEMBAYARAN INSTALL SRK & PSM 007/503/PP/0825",
      "client_name": "FITNESS PLUS INDONESIA",
      "total_invoice": 2220000,
      "debet": 2220000,
      "kredit": 40000,
      "total_bayar": 2180000
    }
  ]
}
```

##### GET /api/posting/payments/search
Deskripsi: Search `Posting Pembayaran Invoice` sesuai field list design JPAS.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "filter": {
    "invoice_date_from": null,
    "invoice_date_to": null,
    "posting_date_from": null,
    "posting_date_to": null,
    "number": null,
    "description": null,
    "client_name": null,
    "q": "FITNESS"
  },
  "total": 1,
  "data": [
    {
      "posting_id": 1,
      "number": "BM01-JA001/0925",
      "posting_date": "2025-09-01",
      "description": "PEMBAYARAN INSTALL SRK & PSM 007/503/PP/0825",
      "client_name": "FITNESS PLUS INDONESIA",
      "total_invoice": 2220000,
      "debet": 2220000,
      "kredit": 40000,
      "total_bayar": 2180000
    }
  ]
}
```

##### GET /api/posting/payments/:postingPayment
Deskripsi: Detail `Posting Pembayaran Invoice` sesuai tab `Detail`, termasuk sub-tab `Detail Invoice`, `Detail Biaya`, dan `Jurnal`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "form": {
    "title": "Posting Invoice [Edit] - POSTING PEMBAYARAN INVOICE",
    "tabs": ["Detail", "List"],
    "sub_tabs": ["Detail Invoice", "Detail Biaya", "Jurnal"]
  },
  "data": {
    "header": {
      "id": 1,
      "bank_code": "BM01",
      "bank_name": "BANK BCA 5600247257",
      "acc_code": "1201",
      "cdf_code": "1201",
      "date": "2025-09-01",
      "number": "BM01-JA001/0925",
      "description": "PEMBAYARAN INSTALL SRK & PSM 007/503/PP/0825",
      "client_code": "00054",
      "client_name": "FITNESS PLUS INDONESIA",
      "total_invoice": 2220000,
      "debet": 2220000,
      "kredit": 40000,
      "total_bayar": 2180000,
      "cetak_kwitansi": true,
      "jurnal_otomatis": true,
      "tt_dan_stempel": true,
      "is_posted": true
    },
    "detail_invoice": [
      {
        "id": 1,
        "no_invoice": "001/S03/PP/0825",
        "tgl_invoice": "2025-08-01",
        "klien": "FITNESS PLUS INDONESIA",
        "nilai": 2000000,
        "ppn": 220000,
        "total": 2220000
      }
    ],
    "detail_biaya": [
      {
        "id": 1,
        "description": "Biaya admin bank",
        "amount": 40000
      }
    ],
    "jurnal": [
      {
        "no_bukti": "BM01-JA001/0925",
        "tanggal": "2025-09-01",
        "acc": "1532",
        "keterangan": "Receipt posting (AR) inv 001/S03/PP/0825",
        "debet": 0,
        "kredit": 2220000
      }
    ]
  }
}
```

##### GET /api/posting/payments/:postingPayment/summary-journal
Deskripsi: Summary jurnal khusus `Posting Pembayaran Invoice`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "posting_number": "BM01-JA001/0925",
  "total_debit": 2220000,
  "total_credit": 2220000,
  "data": [
    {
      "no_bukti": "BM01-JA001/0925",
      "tanggal": "2025-09-01",
      "acc": "1532",
      "keterangan": "Receipt posting (AR) inv 001/S03/PP/0825",
      "debet": 0,
      "kredit": 2220000
    }
  ]
}
```

##### GET /api/posting/payments/:postingPayment/costs
Deskripsi: Sub-tab `Detail Biaya` pada `Posting Pembayaran Invoice`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "posting_payment_id": 1,
      "description": "Biaya admin bank",
      "amount": "40000.00"
    }
  ],
  "total_cost": 40000
}
```

##### POST /api/posting/payments
Deskripsi: Buat posting pembayaran lengkap beserta invoice yang dibayar dan biaya tambahan.
Setelah test POST ini, lanjutkan:
- `GET /api/posting/payments`
- `GET /api/posting/payments/{postingPayment}`
- `GET /api/posting/payments/search?q=<number atau client>`
- `GET /api/posting/payments/{postingPayment}/summary-journal`
- `GET /api/posting/payments/{postingPayment}/costs`
Request Body:
```json
{
  "number": "BM01-JA001/0925",
  "date": "2025-09-01",
  "bank_id": 1,
  "client_id": 1,
  "description": "PEMBAYARAN INSTALL SRK & PSM 007/503/PP/0825",
  "print_receipt": true,
  "auto_journal": true,
  "without_stamp": false,
  "invoices": [
    {
      "invoice_id": 1,
      "amount": 2000000,
      "ppn": 220000
    }
  ],
  "costs": [
    {
      "description": "Biaya admin bank",
      "amount": 40000
    }
  ]
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Posting payment batch success",
  "data": {
    "header": {
      "id": 1,
      "bank_code": "BM01",
      "bank_name": "BANK BCA 5600247257",
      "acc_code": "1201",
      "cdf_code": "1201",
      "date": "2025-09-01",
      "number": "BM01-JA001/0925",
      "description": "PEMBAYARAN INSTALL SRK & PSM 007/503/PP/0825",
      "client_code": "00054",
      "client_name": "FITNESS PLUS INDONESIA",
      "total_invoice": 2220000,
      "debet": 2220000,
      "kredit": 40000,
      "total_bayar": 2180000,
      "cetak_kwitansi": true,
      "jurnal_otomatis": true,
      "tt_dan_stempel": true,
      "is_posted": true
    },
    "detail_invoice": [
      {
        "id": 1,
        "no_invoice": "001/S03/PP/0825",
        "tgl_invoice": "2025-08-01",
        "klien": "FITNESS PLUS INDONESIA",
        "nilai": 2000000,
        "ppn": 220000,
        "total": 2220000
      }
    ],
    "detail_biaya": [
      {
        "id": 1,
        "description": "Biaya admin bank",
        "amount": 40000
      }
    ],
    "jurnal": [
      {
        "no_bukti": "BM01-JA001/0925",
        "tanggal": "2025-09-01",
        "acc": "1532",
        "keterangan": "Receipt posting (AR) inv 001/S03/PP/0825",
        "debet": 0,
        "kredit": 2220000
      }
    ]
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "invoices": ["The invoices field must be an array."]
  }
}
```

##### POST /api/posting/payments/:postingPayment/costs
Deskripsi: Tambah biaya pada sub-tab `Detail Biaya`.
Setelah test POST ini, lanjutkan:
- `GET /api/posting/payments/{postingPayment}/costs`
- `GET /api/posting/payments/{postingPayment}`
Request Body:
```json
{
  "description": "Biaya transfer",
  "amount": 10000
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Posting payment cost created successfully",
  "data": {
    "id": 2,
    "posting_payment_id": 1,
    "description": "Biaya transfer",
    "amount": "10000.00"
  },
  "totals": {
    "total_invoice": 2220000,
    "debet": 2220000,
    "kredit": 50000,
    "total_paid": 2170000
  }
}
```

##### PUT /api/posting/payments/:postingPayment/costs/:cost
Deskripsi: Update biaya pada sub-tab `Detail Biaya`.
Request Body:
```json
{
  "description": "BIAYA ADMIN BANK",
  "amount": 5000
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Posting payment cost updated successfully",
  "data": {
    "id": 1,
    "posting_payment_id": 1,
    "description": "BIAYA ADMIN BANK",
    "amount": "5000.00"
  },
  "totals": {
    "total_invoice": 2220000,
    "debet": 2220000,
    "kredit": 5000,
    "total_paid": 2215000
  }
}
```

##### DELETE /api/posting/payments/:postingPayment/costs/:cost
Deskripsi: Hapus biaya pada sub-tab `Detail Biaya`.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Posting payment cost deleted successfully",
  "totals": {
    "total_invoice": 2220000,
    "debet": 2220000,
    "kredit": 0,
    "total_paid": 2220000
  }
}
```

### 08 Receivable

#### receivables

##### GET /api/receivables
Deskripsi: Ambil data endpoint receivables.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "period": "2025-10",
      "invoice_number": "001/504/LP/1025",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "beginning_balance": 0,
      "netto": 3000000,
      "ppn": 330000,
      "biaya": 0,
      "nota_debet": 0,
      "payment": 0,
      "ending_balance": 3330000,
      "status": "outstanding"
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/receivables/:receivable
Deskripsi: Ambil data endpoint receivables.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "period": "2025-10",
    "invoice_number": "001/504/LP/1025",
    "client_code": "0047",
    "client_name": "PT Prambanan Dwipaka",
    "beginning_balance": 0,
    "netto": 3000000,
    "ppn": 330000,
    "biaya": 0,
    "nota_debet": 0,
    "payment": 0,
    "ending_balance": 3330000,
    "status": "outstanding"
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/receivables/process
Deskripsi: Buat/proses data pada endpoint receivables.
Request Body:
```json
{
    "period":  "2026-03"
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Receivables processed for period 2026-03",
  "period": "2026-03",
  "summary": {
    "total_invoices": 1,
    "receivables_created": 1,
    "receivables_updated": 0,
    "errors_count": 0
  },
  "errors": []
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 09 Reports

#### ar

##### GET /api/reports/ar/ledger
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/ar/summary
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### cash

##### GET /api/reports/cash
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### dashboard

##### GET /api/reports/dashboard?period=2026-03
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### fiscal-commercial

##### GET /api/reports/fiscal-commercial
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "LAPORAN FISKAL & KOMERSIAL",
    "period": "2025-10",
    "excel": false
  },
  "summary": {
    "commercial_dpp": 20850000,
    "commercial_ppn": 2293500,
    "fiscal_dpp": 20850000,
    "fiscal_ppn": 2293500,
    "gap_dpp": 0,
    "gap_ppn": 0
  },
  "data": [
    {
      "invoice_number": "001/504/LP/1025",
      "invoice_date": "2025-10-11",
      "tax_date": "2025-10-11",
      "tax_number": "010.000-25.12345678",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "invoice_type": "PENDAPATAN LICENSE",
      "commercial_dpp": 3000000,
      "commercial_ppn": 330000,
      "fiscal_dpp": 3000000,
      "fiscal_ppn": 330000,
      "status": "MATCH"
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### invoices

##### GET /api/reports/invoices/history
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "HISTORIS INVOICE",
    "invoice_number": "001/504/LP/1025"
  },
  "data": {
    "invoice": {
      "number": "001/504/LP/1025",
      "date": "2025-10-11",
      "client": {
        "code": "0047",
        "name": "PT Prambanan Dwipaka"
      },
      "invoice_type": {
        "code": "504",
        "name": "PENDAPATAN LICENSE"
      }
    },
    "summary": {
      "invoice_total": 3330000,
      "payment_total": 0,
      "outstanding": 3330000,
      "is_paid": false,
      "is_posted": false
    },
    "posting_payments": [],
    "journals": [
      {
        "document_number": "001/504/LP/1025",
        "acc_code": "1535",
        "debit": "3330000.00",
        "credit": "0.00"
      }
    ]
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/invoices/register?period_from=2026-01&period_to=2026-03&detailed=1
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "filter": {
    "invoice_type": "all",
    "status": "all",
    "period_from": "2025-10-01",
    "period_to": "2025-10-31",
    "save_to_excel": false,
    "detailed": true
  },
  "mode": "detail",
  "total_invoices": 1,
  "total_dpp": 3000000,
  "total_ppn": 330000,
  "total_amount": 3330000,
  "data": [
    {
      "tgl": "2025-10-11",
      "no_invoice": "001/504/LP/1025",
      "jth_tempo": "2025-10-11",
      "no_fkt_pajak": "010.000-25.12345678",
      "total": 3330000,
      "no_km_bm": "BM-KM4",
      "tgl_km_bm": "2025-10-11",
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "items": [
        {
          "item": "LICENSE MODUL",
          "deskripsi": "LICENSE MODUL Biaya Server, License & Maintenance",
          "qty": 1,
          "unit": "UNIT",
          "price": 3000000,
          "jumlah": 3000000,
          "discount": 0,
          "dpp": 3000000,
          "ppn": 0
        }
      ]
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### receivable

##### GET /api/reports/receivable/by-item?period_from=2026-01&period_to=2026-06&item_code=1
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "filter": {
    "period_from": "2025-01",
    "period_to": "2025-06",
    "item_code": "2"
  },
  "months": ["2025-01", "2025-02", "2025-03", "2025-04", "2025-05", "2025-06"],
  "total_clients": 2,
  "totals": {
    "2025-01": 1110000,
    "2025-02": 1110000,
    "2025-03": 1665000,
    "2025-04": 1665000,
    "2025-05": 1665000,
    "2025-06": 1665000
  },
  "data": [
    {
      "client_id": 1,
      "client_code": "00016",
      "client_name": "PT. GEDUNG EXPO WIRA JATIM",
      "months": {
        "2025-01": { "nominal": 610500, "status": "B" },
        "2025-02": { "nominal": 610500, "status": "B" },
        "2025-03": { "nominal": 610500, "status": "B" },
        "2025-04": { "nominal": 610500, "status": "B" },
        "2025-05": { "nominal": 610500, "status": "B" },
        "2025-06": { "nominal": 610500, "status": "L" }
      }
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/receivable/data
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "DATA PIUTANG",
    "period": "2025-10"
  },
  "summary": {
    "saldo_awal": 0,
    "netto": 3000000,
    "ppn": 330000,
    "biaya": 0,
    "nota_debet": 0,
    "pembayaran": 0,
    "saldo_akhir": 3330000
  },
  "data": [
    {
      "no_invoice": "001/504/LP/1025",
      "kd_klien": "0047",
      "nama_klien": "PT Prambanan Dwipaka",
      "saldo_awal": 0,
      "netto": 3000000,
      "ppn": 330000,
      "biaya": 0,
      "nota_debet": 0,
      "pembayaran": 0,
      "saldo_akhir": 3330000
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/receivable/detail
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "LAPORAN DETAIL PIUTANG KLIEN",
    "period": {
      "from": "2025-09-01",
      "to": "2025-10-31"
    },
    "client_id": 1
  },
  "totals": {
    "tagihan": 3330000,
    "jumlah": 0,
    "nota_dk": 0,
    "saldo": 3330000
  },
  "data": [
    {
      "tanggal": "2025-10-11",
      "nomor": "001/504/LP/1025",
      "keterangan": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
      "tagihan": 3330000,
      "tgl_bukti": null,
      "no_bukti": null,
      "jumlah": 0,
      "nota_dk": 0,
      "saldo": 3330000,
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka"
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/receivable/process-detail
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "PROSES PIUTANG",
    "period": "2025-10",
    "next_period": "2025-11"
  },
  "summary": {
    "opening_balance": 0,
    "new_invoices": 3330000,
    "nota_debet": 0,
    "payments": 0,
    "closing_balance": 3330000
  },
  "rule": "saldo_akhir = saldo_awal + netto + ppn + biaya + nota_debet - pembayaran"
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/receivable/summary
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "RINGKASAN PIUTANG",
    "period": "2025-10"
  },
  "summary": {
    "saldo_akhir": 3330000
  },
  "data": [
    {
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "saldo_awal": 0,
      "netto": 3000000,
      "ppn": 330000,
      "biaya": 0,
      "nota_debet": 0,
      "pembayaran": 0,
      "saldo_akhir": 3330000
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### sales

##### GET /api/reports/sales
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/sales/client
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "LAPORAN PENJUALAN PER CLIENT",
    "period": {
      "from": "2025-10-01",
      "to": "2025-10-31"
    }
  },
  "data": [
    {
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "bruto": 3000000,
      "discount": 0,
      "dpp": 3000000,
      "ppn": 330000,
      "total": 3330000
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/sales/summary
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "LAPORAN REKAP PENJUALAN PER ITEM PRODUK",
    "period": {
      "from": "2025-10-01",
      "to": "2025-10-31"
    }
  },
  "totals": {
    "bruto": 20850000,
    "discount": 0,
    "dpp": 20850000,
    "ppn": 2293500,
    "total": 23143500
  },
  "data": [
    {
      "kode": "LSA",
      "nama": "LISENSI APLIKASI SIGMA",
      "bruto": 20550000,
      "discount": 0,
      "dpp": 20550000,
      "ppn": 2260500,
      "total": 22810500
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### tax

##### GET /api/reports/tax
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "period": "2025-10",
  "summary": {
    "total_invoices": 1,
    "total_dpp": 3000000,
    "total_ppn_collectible": 330000,
    "total_ppn_percentage": 11
  },
  "by_type": [
    {
      "type": "PENDAPATAN LICENSE",
      "count": 1,
      "dpp": 3000000,
      "ppn": 330000
    }
  ],
  "data": [
    {
      "date": "2025-10-11",
      "tax_date": "2025-10-11",
      "tax_number": "010.000-25.12345678",
      "number": "001/504/LP/1025",
      "client_name": "PT Prambanan Dwipaka",
      "dpp": 3000000,
      "ppn": 330000,
      "invoice_type": "PENDAPATAN LICENSE"
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/tax/summary
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "period": {
    "from": "2025-10-01",
    "to": "2025-10-31"
  },
  "summary": {
    "total_invoice": 1,
    "total_dpp": 3000000,
    "total_ppn": 330000
  },
  "data": [
    {
      "client_code": "0047",
      "client_name": "PT Prambanan Dwipaka",
      "npwp": "01.133.245.4-431.000",
      "dpp": 3000000,
      "ppn": 330000
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/reports/tax/vat
Deskripsi: Ambil data endpoint reports.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "report": {
    "title": "LAPORAN PAJAK STANDART",
    "period": {
      "from": "2025-10-01",
      "to": "2025-10-31"
    },
    "type": "standard",
    "excel": false
  },
  "total_dpp": 3000000,
  "total_ppn": 330000,
  "data": [
    {
      "no": 1,
      "no_transaksi": "001/504/LP/1025",
      "tgl": "2025-10-11",
      "nama_wajib_pajak": "PT Prambanan Dwipaka",
      "npwp": "01.133.245.4-431.000",
      "dpp": 3000000,
      "ppn": 330000,
      "no_pajak": "010.000-25.12345678",
      "tgl_pajak": "2025-10-11"
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 10 Print

#### invoices

##### GET /api/print/invoices/:invoice/pdf
Deskripsi: Ambil data endpoint print.
Request Body: none
Response Sukses (contoh):
```json
{
  "content_type": "application/pdf",
  "filename": "invoice-001-504-LP-1025.pdf",
  "stream": true
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/print/invoices/batch?date_from=2026-03-01&date_to=2026-03-31
Deskripsi: Ambil data endpoint print.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "form": {
    "title": "Cetak Invoice",
    "type": "batch_print",
    "filters": {
      "date_from": "2025-10-01",
      "date_to": "2025-10-31",
      "number": null,
      "client_name": null,
      "invoice_type": "license",
      "is_paid": null,
      "check_all": true
    }
  },
  "total": 1,
  "data": [
    {
      "checked": true,
      "invoice_id": 1,
      "number": "001/504/LP/1025",
      "date": "2025-10-11",
      "client_name": "PT Prambanan Dwipaka",
      "nominal": 3330000,
      "description": "BIAYA SERVER, LICENSE & MAINTENANCE OKT 25",
      "invoice_type_code": "504",
      "invoice_type_name": "PENDAPATAN LICENSE",
      "is_paid": false,
      "pdf_url": "http://localhost:8000/api/print/invoices/1/pdf"
    }
  ]
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/print/invoices/batch
Deskripsi: Buat/proses data pada endpoint print.
Request Body:
```json
{
    "invoice_ids":  [1],
    "note":  true,
    "use_old_letterhead":  false
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Batch print queue prepared",
  "data": {
    "count": 1,
    "note": true,
    "use_old_letterhead": false,
    "invoices": [
      {
        "invoice_id": 1,
        "number": "001/504/LP/1025",
        "date": "2025-10-11",
        "client_name": "PT Prambanan Dwipaka",
        "invoice_type": "PENDAPATAN LICENSE",
        "pdf_url": "http://localhost:8000/api/print/invoices/1/pdf"
      }
    ]
  }
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

#### receipts

##### GET /api/print/receipts/:posting_payment/pdf
Deskripsi: Ambil data endpoint print.
Request Body: none
Response Sukses (contoh):
```json
{
  "content_type": "application/pdf",
  "filename": "receipt-BM01-JA001-0925.pdf",
  "stream": true
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

### 11 Protection

#### protections

##### DELETE /api/protections/:protection
Deskripsi: Hapus data pada endpoint protections.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/protections
Deskripsi: Ambil data endpoint protections.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### GET /api/protections/:protection
Deskripsi: Ambil data endpoint protections.
Request Body: none
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### POST /api/protections
Deskripsi: Buat/proses data pada endpoint protections.
Request Body:
```json
{
    "period":  "2026-03",
    "is_protected":  true,
    "protected_at":  "2026-03-31",
    "protected_by":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

##### PUT /api/protections/:protection
Deskripsi: Update penuh data pada endpoint protections.
Request Body:
```json
{
    "period":  "2026-03",
    "is_protected":  false,
    "protected_at":  "2026-03-31",
    "protected_by":  1
}
```
Response Sukses (contoh):
```json
{
  "success": true,
  "message": "Request success",
  "data": {}
}
```
Response Error Validasi (contoh 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["validation message"]
  }
}
```

## 4) Error Matrix
- 401: token invalid / belum login
- 403: role tidak diizinkan
- 404: resource tidak ditemukan
- 409: konflik data
- 422: validasi input gagal
- 423: period terkunci

## 5) Checklist Eksekusi Per Endpoint
1. Jalankan request sukses.
2. Jalankan 1 skenario validasi error (422).
3. Save response di Postman.
4. Lanjut endpoint berikutnya sesuai urutan folder.



