# JPAS v3.0 Alignment Report (Code vs Documentation)

Tanggal audit: 2026-03-08  
Baseline dokumen: `JPAS System Documentation v3.0 (32 halaman, final)`

## Ringkasan
- Status umum: `NEAR 1:1 MATCH`
- Modul core + report kritikal JPAS sudah tersedia sebagai endpoint API.
- Gap tersisa minor: output print batch masih berupa queue/list URL PDF (belum merge dokumen fisik jadi 1 file).

## Sudah Sesuai
- Auth: `login`, `logout`, `me`.
- Master: `companies`, `clients`, `team-members`, `products`, `items`, `banks`, `invoice-types`, `master-item-products`.
- Sales: `work-orders`, `assign-team`, `installations`, `licenses`, `stop-licenses`.
- Finance core: `invoices`, `invoice-items`, `payments`, `debit-credit-notes`, `receivables/process`.
- Posting: `posting/omzet`, `posting/payments`.
- Protection period: `protections`.
- Print dasar: `print/invoices/{invoice}/pdf`, `print/receipts/{posting_payment}/pdf`.
- Setup No. Seri Faktur Pajak:
  - `GET /api/tax-series`
  - `POST /api/tax-series`
  - `POST /api/tax-series/{taxSeries}/next`
  - `DELETE /api/tax-series/{taxSeries}`
- Report tambahan JPAS:
  - `GET /api/reports/invoices/history`
  - `GET /api/reports/fiscal-commercial`
  - `GET /api/reports/receivable/by-item`
  - `GET /api/reports/receivable/data`
  - `GET /api/reports/receivable/process-detail`
- Batch print workflow:
  - `GET /api/print/invoices/batch` (filter + check all style)
  - `POST /api/print/invoices/batch` (queue selected invoice)
- Posting Pembayaran Detail Biaya:
  - `GET /api/posting/payments/{postingPayment}/costs`
  - `POST /api/posting/payments/{postingPayment}/costs`
  - `PUT /api/posting/payments/{postingPayment}/costs/{cost}`
  - `DELETE /api/posting/payments/{postingPayment}/costs/{cost}`

## Catatan Implementasi
- Semua perubahan dilakukan bertahap tanpa `migrate:fresh`.
- Untuk fitur baru diperlukan migrasi tambahan:
  - `2026_03_08_120000_add_tax_series_fields_to_invoice_series_table.php`
  - `2026_03_08_130000_create_posting_payment_costs_table.php`

## Sisa Gap Minor (Non-Blocking)
1. Batch print invoice saat ini mengembalikan queue + daftar URL PDF per invoice (belum 1 dokumen gabungan).
2. Jika ingin 100% visual desktop, perlu service merge PDF per batch print.

## File Kode yang Disesuaikan pada Iterasi Ini
- `app/Http/Controllers/Api/ProductController.php`
- `app/Http/Controllers/Api/BankController.php`
- `app/Http/Controllers/Api/StopLicenseController.php`
- `app/Http/Controllers/Api/ItemController.php`
- `app/Http/Controllers/Api/ProductGroupController.php`
- `app/Http/Controllers/Api/CompanySettingController.php`
- `app/Http/Controllers/Api/MasterItemProductController.php`
- `app/Http/Controllers/Api/ChartOfAccountController.php`
- `app/Http/Controllers/Api/InvoiceSeriesController.php`
- `app/Http/Controllers/Api/JpasReportController.php`
- `app/Http/Controllers/Api/PostingPaymentCostController.php`
- `app/Http/Controllers/Api/PrintController.php`
- `app/Http/Controllers/Api/PostingPaymentController.php`
- `app/Http/Controllers/Api/ReceivableReportController.php`
- `app/Models/Product.php`
- `app/Models/StopLicense.php`
- `app/Models/InvoiceSeries.php`
- `app/Models/PostingPayment.php`
- `app/Models/PostingPaymentCost.php`
- `routes/api.php`
- `database/migrations/2026_03_08_080000_add_client_spv_id_to_stop_licenses_table.php`
- `database/migrations/2026_03_08_090000_cleanup_authors_artifacts.php`
- `database/migrations/2026_03_08_120000_add_tax_series_fields_to_invoice_series_table.php`
- `database/migrations/2026_03_08_130000_create_posting_payment_costs_table.php`
