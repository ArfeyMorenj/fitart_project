# FitArt JPAS Postman Guide (Synced With FINAL Testing Flow)

Tanggal: 2026-03-08

## 1) File Final
- Collection: `FitArt_JPAS_API_Complete.postman_collection.json`
- Environment: `FitArt_JPAS_Local.postman_environment.json`
- Guide utama testing berurutan: `docs/fitart_api_testing_guide_final.md`

## 2) Cara Import
1. Buka Postman.
2. Klik `Import`.
3. Import:
- `FitArt_JPAS_API_Complete.postman_collection.json`
- `FitArt_JPAS_Local.postman_environment.json`
4. Pilih environment: `FitArt JPAS Local (Final)`.

## 3) Variable Environment Wajib
- `base_url` = `http://localhost:8000`
- `token` = auto set dari endpoint login
- `company_id`, `client_id`, `product_id`, `work_order_id`, `invoice_id`, `postingPayment`, `taxSeries` default `1`

## 4) Urutan Jalankan di Postman (Sama Dengan Guide Final)
1. `POST /api/login`
2. `GET /api/me`
3. Lookup: `chart-of-accounts`, `team-members`, `product-groups`, `items`, `banks`, `invoice-types`, `master-item-products`, `clients`
4. Setup: `company/settings` dan `tax-series`
5. Master CRUD: `companies`, `clients`, `team-members`, `product-groups`, `products`, `items`, `master-item-products`, `banks`, `invoice-types`, `users`
6. Sales: `work-orders` -> `assign-team` -> `installations` -> `licenses` -> `stop-licenses`
7. Finance: `invoices` -> `invoice-items` -> `debit-credit-notes` -> `payments`
8. Posting: `posting/omzet` -> `posting/payments` -> `posting/payments/{postingPayment}/costs`
9. Receivable: `receivables` -> `receivables/process`
10. Reports: semua `/api/reports/*`
11. Print: `/api/print/invoices/batch`, `/api/print/invoices/{invoice}/pdf`, `/api/print/receipts/{posting_payment}/pdf`
12. Protection test (opsional): lock period lalu uji endpoint write agar kena 423

## 5) Struktur Folder Collection yang Disarankan
- `01 Auth`
- `02 Lookups`
- `03 Setup`
- `04 Master Data`
- `05 Sales`
- `06 Finance`
- `07 Posting`
- `08 Receivable`
- `09 Reports`
- `10 Print`
- `11 Protection`

## 6) Troubleshooting
- `401` -> token belum terpasang / expired
- `403` -> role user tidak sesuai middleware
- `422` -> body request tidak sesuai validasi
- `423` -> period terkunci
- `404` -> id/nomor resource tidak ditemukan

## 7) Catatan Penting
- Kalau endpoint gagal validasi COA, cek dulu `GET /api/chart-of-accounts/search?q=<kode>`.
- Jalankan test sesuai urutan supaya foreign key tidak gagal.
- Referensi payload dan response paling lengkap ada di:
  - `docs/fitart_api_testing_guide_final.md`
