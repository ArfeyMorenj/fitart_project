# 🌳 POSTMAN COLLECTION TREE STRUCTURE - COMPLETE GUIDE

**Status:** Clear explanation of WHERE each endpoint should be placed  
**Based on:** routes/api.php yang benar

---

## ⚠️ PERBEDAAN 4 INVOICE ENDPOINTS

Sebelum bikin tree, ingat ada **4 endpoint BERBEDA** untuk invoice:

| Route | Purpose | Type | Folder |
|-------|---------|------|--------|
| `/api/invoice-license` | License invoices only | Read-only (4 GET) | `invoice-license` ← NEW |
| `/api/invoice-products` | Product invoices only | Read-only (4 GET) | `invoice-products` |
| `/api/invoices` | All invoices CRUD | Full CRUD (5) | `invoices` |
| `/api/invoice-items` | Invoice line items | Full CRUD (5) | `invoice-items` |

---

## 🌲 CORRECT TREE STRUCTURE (LENGKAP)

```
FitArt JPAS API Complete.postman_collection.json
│
├─── 01 Auth (3 items)
│    ├─ POST /api/login
│    ├─ POST /api/logout
│    └─ GET /api/me
│
├─── 02 Lookups (10 items)
│    ├─ GET /api/banks/search
│    ├─ GET /api/chart-of-accounts
│    ├─ GET /api/chart-of-accounts/{id}
│    ├─ GET /api/chart-of-accounts/search
│    ├─ GET /api/clients/search
│    ├─ GET /api/companies/search
│    ├─ GET /api/invoice-types/search
│    ├─ GET /api/items/search
│    ├─ GET /api/master-item-products/search
│    ├─ GET /api/product-groups/search
│    └─ GET /api/team-members/search
│
├─── 03 Setup (2 items)
│    ├─ GET /api/company/settings
│    ├─ POST /api/company/settings
│    ├─ GET /api/tax-series
│    ├─ POST /api/tax-series
│    ├─ POST /api/tax-series/{id}/next
│    └─ DELETE /api/tax-series/{id}
│
├─── 04 Master Data (10 sections - 50+ items)
│    ├─ 📁 banks (6 items)
│    │  ├─ GET /api/banks
│    │  ├─ GET /api/banks/{id}
│    │  ├─ GET /api/banks/search
│    │  ├─ POST /api/banks
│    │  ├─ PUT /api/banks/{id}
│    │  └─ DELETE /api/banks/{id}
│    │
│    ├─ 📁 clients (6 items)
│    │  ├─ GET /api/clients
│    │  ├─ GET /api/clients/{id}
│    │  ├─ GET /api/clients/search
│    │  ├─ POST /api/clients
│    │  ├─ PUT /api/clients/{id}
│    │  └─ DELETE /api/clients/{id}
│    │
│    ├─ 📁 companies (5 items)
│    │  ├─ GET /api/companies
│    │  ├─ GET /api/companies/{id}
│    │  ├─ POST /api/companies
│    │  ├─ PUT /api/companies/{id}
│    │  └─ DELETE /api/companies/{id}
│    │
│    ├─ 📁 invoice-types (7 items)
│    │  ├─ GET /api/invoice-types
│    │  ├─ GET /api/invoice-types/{id}
│    │  ├─ GET /api/invoice-types/search
│    │  ├─ POST /api/invoice-types
│    │  ├─ PUT /api/invoice-types/{id}
│    │  ├─ DELETE /api/invoice-types/{id}
│    │  └─ POST /api/invoice-types/{id}/toggle-status
│    │
│    ├─ 📁 items (6 items)
│    │  ├─ GET /api/items
│    │  ├─ GET /api/items/{id}
│    │  ├─ GET /api/items/search
│    │  ├─ POST /api/items
│    │  ├─ PUT /api/items/{id}
│    │  └─ DELETE /api/items/{id}
│    │
│    ├─ 📁 master-item-products (7 items)
│    │  ├─ GET /api/master-item-products
│    │  ├─ GET /api/master-item-products/{id}
│    │  ├─ GET /api/master-item-products/search
│    │  ├─ POST /api/master-item-products
│    │  ├─ PUT /api/master-item-products/{id}
│    │  ├─ DELETE /api/master-item-products/{id}
│    │  └─ POST /api/master-item-products/{id}/toggle-status
│    │
│    ├─ 📁 product-groups (5 items)
│    │  ├─ GET /api/product-groups
│    │  ├─ GET /api/product-groups/{id}
│    │  ├─ POST /api/product-groups
│    │  ├─ PUT /api/product-groups/{id}
│    │  └─ DELETE /api/product-groups/{id}
│    │
│    ├─ 📁 products (6 items)
│    │  ├─ GET /api/products
│    │  ├─ GET /api/products/{id}
│    │  ├─ GET /api/products/search
│    │  ├─ POST /api/products
│    │  ├─ PUT /api/products/{id}
│    │  └─ DELETE /api/products/{id}
│    │
│    ├─ 📁 team-members (6 items)
│    │  ├─ GET /api/team-members
│    │  ├─ GET /api/team-members/{id}
│    │  ├─ GET /api/team-members/search
│    │  ├─ POST /api/team-members
│    │  ├─ PUT /api/team-members/{id}
│    │  └─ DELETE /api/team-members/{id}
│    │
│    └─ 📁 users (9 items)
│       ├─ GET /api/users
│       ├─ GET /api/users/{id}
│       ├─ POST /api/users
│       ├─ PUT /api/users/{id}
│       ├─ DELETE /api/users/{id}
│       ├─ POST /api/users/{id}/assign-role
│       ├─ POST /api/users/{id}/toggle-status
│       ├─ GET /api/me
│       └─ [GET /api/me]
│
├─── 05 Sales (4 sections)
│    ├─ 📁 installations (5 items)
│    │  ├─ GET /api/installations
│    │  ├─ GET /api/installations/{id}
│    │  ├─ POST /api/installations
│    │  ├─ PUT /api/installations/{id}
│    │  └─ DELETE /api/installations/{id}
│    │
│    ├─ 📁 licenses (5 items)
│    │  ├─ GET /api/licenses
│    │  ├─ GET /api/licenses/{id}
│    │  ├─ POST /api/licenses
│    │  ├─ PUT /api/licenses/{id}
│    │  └─ DELETE /api/licenses/{id}
│    │
│    ├─ 📁 stop-licenses (6 items)
│    │  ├─ GET /api/stop-licenses
│    │  ├─ GET /api/stop-licenses/{id}
│    │  ├─ GET /api/stop-licenses/search
│    │  ├─ POST /api/stop-licenses
│    │  ├─ PUT /api/stop-licenses/{id}
│    │  └─ DELETE /api/stop-licenses/{id}
│    │
│    └─ 📁 work-orders (7 items)
│       ├─ GET /api/work-orders
│       ├─ GET /api/work-orders/{id}
│       ├─ GET /api/work-orders/search
│       ├─ POST /api/work-orders
│       ├─ PUT /api/work-orders/{id}
│       ├─ DELETE /api/work-orders/{id}
│       └─ POST /api/work-orders/{id}/assign-team
│
├─── 06 Finance (7 sections)  ← 🔴 PERLU PERBAIKAN!
│    ├─ 📁 debit-credit-notes (6 items) ← +1 search endpoint
│    │  ├─ GET /api/debit-credit-notes
│    │  ├─ GET /api/debit-credit-notes/{id}
│    │  ├─ GET /api/debit-credit-notes/summary-journal
│    │  ├─ GET /api/debit-credit-notes/search              ← 🆕 NEW (MISSING)
│    │  ├─ POST /api/debit-credit-notes
│    │  ├─ PUT /api/debit-credit-notes/{id}
│    │  └─ DELETE /api/debit-credit-notes/{id}
│    │
│    ├─ 📁 invoice-items (5 items)
│    │  ├─ GET /api/invoice-items
│    │  ├─ GET /api/invoice-items/{id}
│    │  ├─ POST /api/invoice-items
│    │  ├─ PUT /api/invoice-items/{id}
│    │  └─ DELETE /api/invoice-items/{id}
│    │
│    ├─ 📁 invoice-license (4 items) ← 🆕 NEW FOLDER!
│    │  ├─ GET /api/invoice-license                       ← 🆕 NEW (MISSING)
│    │  ├─ GET /api/invoice-license/{id}                  ← 🆕 NEW (MISSING)
│    │  ├─ GET /api/invoice-license/search                (if exists)
│    │  └─ GET /api/invoice-license/{id}/summary-journal  (if exists)
│    │
│    ├─ 📁 invoice-products (4 items)
│    │  ├─ GET /api/invoice-products
│    │  ├─ GET /api/invoice-products/{id}
│    │  ├─ GET /api/invoice-products/search
│    │  └─ GET /api/invoice-products/{id}/summary-journal
│    │
│    ├─ 📁 invoices (5 items)
│    │  ├─ GET /api/invoices
│    │  ├─ GET /api/invoices/{id}
│    │  ├─ POST /api/invoices
│    │  ├─ PUT /api/invoices/{id}
│    │  └─ DELETE /api/invoices/{id}
│    │
│    ├─ 📁 payments (5 items)
│    │  ├─ GET /api/payments
│    │  ├─ GET /api/payments/{id}
│    │  ├─ POST /api/payments
│    │  ├─ PUT /api/payments/{id}
│    │  └─ DELETE /api/payments/{id}
│    │
│    └─ 📁 receivables (2 items)
│       ├─ GET /api/receivables
│       └─ GET /api/receivables/{id}
│
├─── 07 Posting (3 sections) ← 🔴 PERLU PERBAIKAN!
│    ├─ 📁 journals (1 item)
│    │  └─ GET /api/journals
│    │
│    ├─ 📁 posting-omzet (1 item)
│    │  └─ POST /api/posting/omzet
│    │
│    └─ 📁 posting-payments (8 items) ← +3 GET endpoints
│       ├─ 🆕 GET /api/posting/payments                   ← 🆕 NEW (MISSING)
│       ├─ 🆕 GET /api/posting/payments/search            ← 🆕 NEW (MISSING)
│       ├─ 🆕 GET /api/posting/payments/{id}              ← 🆕 NEW (MISSING)
│       ├─ GET /api/posting/payments/{id}/summary-journal (if exists)
│       ├─ POST /api/posting/payments
│       ├─ GET /api/posting/payments/{id}/costs
│       ├─ POST /api/posting/payments/{id}/costs
│       ├─ PUT /api/posting/payments/{id}/costs/{cost}
│       └─ DELETE /api/posting/payments/{id}/costs/{cost}
│
├─── 08 Receivable (1 section)
│    └─ 📁 receivables
│       ├─ GET /api/receivables
│       ├─ GET /api/receivables/{id}
│       ├─ GET /api/receivables/search (dari POST route)
│       └─ POST /api/receivables/process
│
├─── 09 Reports (8 sections)
│    ├─ 📁 dashboard
│    │  └─ GET /api/reports/dashboard
│    │
│    ├─ 📁 ar-summary
│    │  ├─ GET /api/reports/ar/summary
│    │  └─ GET /api/reports/ar/ledger
│    │
│    ├─ 📁 invoices
│    │  ├─ GET /api/reports/invoices/register
│    │  └─ GET /api/reports/invoices/history
│    │
│    ├─ 📁 tax
│    │  ├─ GET /api/reports/tax
│    │  ├─ GET /api/reports/tax/vat
│    │  └─ GET /api/reports/tax/summary
│    │
│    ├─ 📁 sales
│    │  ├─ GET /api/reports/sales
│    │  ├─ GET /api/reports/sales/summary
│    │  └─ GET /api/reports/sales/client
│    │
│    ├─ 📁 cash
│    │  └─ GET /api/reports/cash
│    │
│    ├─ 📁 fiscal-commercial
│    │  └─ GET /api/reports/fiscal-commercial
│    │
│    └─ 📁 receivable-reports
│       ├─ GET /api/reports/receivable/by-item
│       ├─ GET /api/reports/receivable/data
│       ├─ GET /api/reports/receivable/process-detail
│       ├─ GET /api/reports/receivable/summary
│       └─ GET /api/reports/receivable/detail
│
├─── 10 Print (2 sections)
│    ├─ 📁 invoices
│    │  ├─ GET /api/print/invoices/batch
│    │  ├─ POST /api/print/invoices/batch
│    │  └─ GET /api/print/invoices/{id}/pdf
│    │
│    └─ 📁 receipts
│       └─ GET /api/print/receipts/{id}/pdf
│
└─── 11 Protection (1 section)
     └─ 📁 protections (5 items)
        ├─ GET /api/protections
        ├─ GET /api/protections/{id}
        ├─ POST /api/protections
        ├─ PUT /api/protections/{id}
        └─ DELETE /api/protections/{id}
```

---

## 🔴 YANG PERLU DIBENAHI (6 ENDPOINT MISSING)

### 06 FINANCE

```
SAAT INI (SALAH):
📁 finance
  └─ 📁 invoices
     ├─ GET /api/invoices
     ├─ GET /api/invoice-license  ❌ SALAH TEMPAT!
     ├─ GET /api/invoice-products ❌ SALAH TEMPAT!
     └─ ...

SEHARUSNYA (BENAR):
📁 finance
  ├─ 📁 invoices               ← Untuk /api/invoices
  │  ├─ GET /api/invoices
  │  ├─ GET /api/invoices/{id}
  │  ├─ POST /api/invoices
  │  ├─ PUT /api/invoices/{id}
  │  └─ DELETE /api/invoices/{id}
  │
  ├─ 📁 invoice-items          ← Untuk /api/invoice-items
  │  ├─ GET /api/invoice-items
  │  ├─ GET /api/invoice-items/{id}
  │  ├─ POST /api/invoice-items
  │  ├─ PUT /api/invoice-items/{id}
  │  └─ DELETE /api/invoice-items/{id}
  │
  ├─ 📁 invoice-license        ← 🆕 NEW FOLDER! Untuk /api/invoice-license
  │  ├─ GET /api/invoice-license          ← 🆕 TAMBAH
  │  └─ GET /api/invoice-license/{id}     ← 🆕 TAMBAH
  │
  ├─ 📁 invoice-products       ← Untuk /api/invoice-products (PINDAHKAN DARI INVOICES!)
  │  ├─ GET /api/invoice-products
  │  ├─ GET /api/invoice-products/{id}
  │  └─ GET /api/invoice-products/search
  │
  ├─ 📁 debit-credit-notes
  │  ├─ GET /api/debit-credit-notes
  │  ├─ GET /api/debit-credit-notes/{id}
  │  ├─ GET /api/debit-credit-notes/search     ← 🆕 TAMBAH
  │  ├─ POST /api/debit-credit-notes
  │  ├─ PUT /api/debit-credit-notes/{id}
  │  └─ DELETE /api/debit-credit-notes/{id}
  │
  ├─ 📁 payments
  └─ 📁 receivables
```

### 07 POSTING

```
SAAT INI (INCOMPLETE):
📁 posting
  └─ 📁 posting-payments
     ├─ POST /api/posting/payments
     ├─ GET /api/posting/payments/{id}/costs
     ├─ POST /api/posting/payments/{id}/costs
     ├─ PUT /api/posting/payments/{id}/costs/{cost}
     └─ DELETE /api/posting/payments/{id}/costs/{cost}
     ❌ MISSING 3 GET endpoints!

SEHARUSNYA (LENGKAP):
📁 posting
  ├─ 📁 journals
  │  └─ GET /api/journals
  │
  ├─ 📁 posting-omzet
  │  └─ POST /api/posting/omzet
  │
  └─ 📁 posting-payments
     ├─ GET /api/posting/payments                    ← 🆕 TAMBAH
     ├─ GET /api/posting/payments/search             ← 🆕 TAMBAH
     ├─ GET /api/posting/payments/{id}               ← 🆕 TAMBAH
     ├─ POST /api/posting/payments
     ├─ GET /api/posting/payments/{id}/costs
     ├─ POST /api/posting/payments/{id}/costs
     ├─ PUT /api/posting/payments/{id}/costs/{cost}
     └─ DELETE /api/posting/payments/{id}/costs/{cost}
```

---

## ✅ AKSI YANG PERLU DILAKUKAN

### 1️⃣ DI FINANCE SECTION - REORGANIZE

**Step 1: DELETE** (yang salah tempat dari invoices folder):
- ❌ DELETE `/api/invoice-license` endpoints (out of place)
- ❌ DELETE `/api/invoice-products` endpoints (out of place - pindahkan!)

**Step 2: CREATE** new folder di finance:
- ✅ CREATE `📁 invoice-license` folder
  - ✅ ADD `GET /api/invoice-license`
  - ✅ ADD `GET /api/invoice-license/{id}`

**Step 3: CREATE** new folder (if not exists):
- ✅ CREATE `📁 invoice-products` folder (MOVE existing endpoints here if they exist)
  - GET /api/invoice-products
  - GET /api/invoice-products/{id}
  - GET /api/invoice-products/search

**Step 4: ADD** search endpoint:
- ✅ ADD `GET /api/debit-credit-notes/search` to `debit-credit-notes` folder

---

### 2️⃣ DI POSTING SECTION - ADD 3 ENDPOINTS

**Step 1: ADD** to posting-payments folder:
- ✅ ADD `GET /api/posting/payments` (list)
- ✅ ADD `GET /api/posting/payments/search` (search)
- ✅ ADD `GET /api/posting/payments/{id}` (detail)

**Step 2: REORDER** (optional tapi recommended):
- Move 3 GET endpoints ke ATAS
- Kemudian POST/PUT/DELETE
- Hierarchy: READ (GET) → WRITE (POST/PUT/DELETE)

---

## 📋 CLEAR SUMMARY

| Action | Location | Endpoint | Status |
|--------|----------|----------|--------|
| **CREATE NEW FOLDER** | 06 Finance | `invoice-license` | 🆕 NEW |
| **ADD** | 06 Finance > invoice-license | GET `/api/invoice-license` | 🆕 NEW |
| **ADD** | 06 Finance > invoice-license | GET `/api/invoice-license/{id}` | 🆕 NEW |
| **ADD** | 06 Finance > debit-credit-notes | GET `/api/debit-credit-notes/search` | 🆕 NEW |
| **PINDAHKAN** | 06 Finance > invoice-products | invoice-products endpoints | ⚠️ ORGANIZE |
| **ADD** | 07 Posting > posting-payments | GET `/api/posting/payments` | 🆕 NEW |
| **ADD** | 07 Posting > posting-payments | GET `/api/posting/payments/search` | 🆕 NEW |
| **ADD** | 07 Posting > posting-payments | GET `/api/posting/payments/{id}` | 🆕 NEW |
| **REORDER** | 07 Posting > posting-payments | GET endpoints first | ⚠️ ORGANIZE |

---

**TOTAL:** 6 endpoints to add + organization cleanup

Sudah jelas? 🎯
