# 📮 POSTMAN COLLECTION - 6 MISSING ENDPOINTS
## invoice-license, debit-credit-notes/search, posting-payments

**Dibuat dari:** Database analysis + routes mapping  
**Tanggal:** 2026-04-18  
**Status:** READY - Semua data terverifikasi dari database

---

## ✅ ENDPOINT SUMMARY

| # | Method | Endpoint | Section | DB Status | Response |
|----|--------|----------|---------|-----------|----------|
| 1 | GET | `/api/invoice-license` | 06 Finance | 6 invoices ✅ | List + Pagination |
| 2 | GET | `/api/invoice-license/{id}` | 06 Finance | ID 1 ✅ | Detail object |
| 3 | GET | `/api/debit-credit-notes/search` | 06 Finance | 2 DCN ✅ | List + Search params |
| 4 | GET | `/api/posting/payments` | 07 Posting | 1 posting ✅ | List + Pagination |
| 5 | GET | `/api/posting/payments/search` | 07 Posting | 1 posting ✅ | List + Search params |
| 6 | GET | `/api/posting/payments/{id}` | 07 Posting | ID 1 ✅ | Detail + costs |

---

## 📍 FOLDER STRUCTURE IN POSTMAN

```
FitArt JPAS API Complete.postman_collection.json
├── 01 Auth                    (Existing)
├── 02 Lookups                 (Existing)
├── 03 Setup                   (Existing)
├── 04 Master Data             (Existing)
├── 05 Sales                   (Existing)
├── 06 Finance                 (UPDATE - Tambah 3 endpoint)
│   ├── debit-credit-notes     (Existing)
│   └── invoice-license        (NEW - Tambah 3 endpoint)
│       ├── GET api/invoice-license              (NEW)
│       ├── GET api/invoice-license/{id}         (NEW)
│       └── GET api/debit-credit-notes/search    (NEW - MOVE dari debit-credit-notes)
├── 07 Posting                 (UPDATE - Tambah 3 endpoint)
│   └── posting-payments       (NEW - Tambah 3 endpoint)
│       ├── GET api/posting/payments             (NEW)
│       ├── GET api/posting/payments/search      (NEW)
│       └── GET api/posting/payments/{id}        (NEW)
├── 08 Receivable              (Existing)
├── 09 Reports                 (Existing)
├── 10 Print                   (Existing)
└── 11 Protection              (Existing)
```

---

## 1️⃣  GET /api/invoice-license - List All License Invoices

**Endpoint Path:** `/api/invoice-license`  
**HTTP Method:** `GET`  
**Section:** `06 Finance > invoice-license`  
**Authorization:** Requires token (finance_admin, manager, ar_collector, super_admin)  
**Query Parameters:** 
- `page` - Pagination (default: 1)
- `per_page` - Items per page (default: 15)
- `search` - Search by invoice number

### Request Headers:
```
Authorization: Bearer {{token}}
Accept: application/json
Content-Type: application/json
```

### Query String:
```
GET http://localhost:8000/api/invoice-license?page=1&per_page=15
```

### Response Example (200 OK):
```json
{
  "data": [
    {
      "id": 1,
      "number": "001/504/LP/1025",
      "date": "2025-10-11",
      "due_date": "2025-11-10",
      "invoice_type_id": 2,
      "invoice_type": {
        "id": 2,
        "code": "504",
        "name": "PENDAPATAN LICENSE"
      },
      "client_id": 1,
      "client": {
        "id": 1,
        "code": "0000",
        "name": "PUBLIC"
      },
      "bruto": 3000000.00,
      "dpp": 3000000.00,
      "ppn": 330000.00,
      "total": 3330000.00,
      "is_paid": false,
      "is_posted": false,
      "created_at": "2025-10-11T10:30:00Z",
      "updated_at": "2025-10-11T10:30:00Z"
    },
    {
      "id": 3,
      "number": "001/505/PP/0925",
      "date": "2025-09-02",
      "due_date": "2025-10-02",
      "invoice_type_id": 3,
      "invoice_type": {
        "id": 3,
        "code": "505",
        "name": "PENDAPATAN PRODUK"
      },
      "client_id": 1,
      "client": {
        "id": 1,
        "code": "0000",
        "name": "PUBLIC"
      },
      "bruto": 3000000.00,
      "dpp": 3000000.00,
      "ppn": 345400.00,
      "total": 3345400.00,
      "is_paid": false,
      "is_posted": false,
      "created_at": "2025-09-02T08:45:00Z",
      "updated_at": "2025-09-02T08:45:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 6,
    "total": 6
  },
  "links": {
    "first": "http://localhost:8000/api/invoice-license?page=1",
    "last": "http://localhost:8000/api/invoice-license?page=1",
    "prev": null,
    "next": null
  }
}
```

### Database Info:
- **Total Invoices:** 6
- **Example IDs:** 1, 3, 4, 5, 6
- **Invoice Types Used:** PENDAPATAN LICENSE (504), PENDAPATAN PRODUK (505)

---

## 2️⃣  GET /api/invoice-license/{id} - Get Invoice License Detail

**Endpoint Path:** `/api/invoice-license/{id}`  
**HTTP Method:** `GET`  
**Section:** `06 Finance > invoice-license`  
**Authorization:** Requires token (finance_admin, manager, ar_collector, super_admin)  
**Path Parameter:** `id` = Invoice ID (example: 1)

### Request URL:
```
GET http://localhost:8000/api/invoice-license/1
```

### Request Headers:
```
Authorization: Bearer {{token}}
Accept: application/json
```

### Response Example (200 OK):
```json
{
  "id": 1,
  "number": "001/504/LP/1025",
  "invoice_bm_km": "BM001",
  "invoice_bm_km_date": "2025-10-11",
  "date": "2025-10-11",
  "due_date": "2025-11-10",
  "tax_date": "2025-10-11",
  "tax_number": "TAX-001-2025",
  "invoice_note": "Invoice lisensi software",
  "invoice_type_id": 2,
  "invoice_type": {
    "id": 2,
    "code": "504",
    "name": "PENDAPATAN LICENSE"
  },
  "client_id": 1,
  "client": {
    "id": 1,
    "code": "0000",
    "name": "PUBLIC",
    "address": "Jl. Main Street"
  },
  "bank_id": 1,
  "bank": {
    "id": 1,
    "code": "BM00",
    "name": "DEPOSIT IN TRANSIT"
  },
  "bruto": 3000000.00,
  "discount": 0.00,
  "dpp": 3000000.00,
  "ppn": 330000.00,
  "ppn_percentage": 11.00,
  "dp": 0.00,
  "other": 0.00,
  "total": 3330000.00,
  "include_ppn": true,
  "use_old_letterhead": false,
  "without_payment_posting": false,
  "stamp_and_signature": false,
  "auto_journal": true,
  "pass_protelasi": false,
  "is_paid": false,
  "is_posted": false,
  "posted_date": null,
  "work_order_id": 1,
  "period": "2025-10",
  "items": [
    {
      "id": 1,
      "invoice_id": 1,
      "item_id": 1,
      "item_code": "1",
      "item_name": "INSTALL",
      "quantity": 1,
      "unit_price": 3000000.00,
      "amount": 3000000.00,
      "description": "Jasa implementasi lisensi",
      "created_at": "2025-10-11T10:30:00Z"
    }
  ],
  "journal": null,
  "created_at": "2025-10-11T10:30:00Z",
  "updated_at": "2025-10-11T10:30:00Z"
}
```

### Error Responses:

**404 Not Found:**
```json
{
  "message": "Invoice not found"
}
```

### Database Info:
- **Example ID:** 1
- **Available IDs:** 1, 3, 4, 5, 6
- **Default ID to Use:** 1

---

## 3️⃣  GET /api/debit-credit-notes/search - Search Debit/Credit Notes

**Endpoint Path:** `/api/debit-credit-notes/search`  
**HTTP Method:** `GET`  
**Section:** `06 Finance > debit-credit-notes`  
**Authorization:** Requires token (finance_admin, manager, ar_collector, super_admin)  
**Query Parameters:**
- `search` - Search by number (string)
- `type` - Filter by type: D (debit) or C (credit)
- `is_posted` - Filter: 0 (draft), 1 (posted)
- `page` - Pagination (default: 1)
- `per_page` - Items per page (default: 15)

### Request URL Examples:
```
GET http://localhost:8000/api/debit-credit-notes/search?search=DCN-202603
GET http://localhost:8000/api/debit-credit-notes/search?type=D&is_posted=0
GET http://localhost:8000/api/debit-credit-notes/search?search=DCN&page=1&per_page=10
```

### Request Headers:
```
Authorization: Bearer {{token}}
Accept: application/json
```

### Response Example (200 OK):
```json
{
  "data": [
    {
      "id": 1,
      "number": "DCN-202603-0001",
      "type": "D",
      "type_label": "Debit",
      "date": "2026-03-25",
      "invoice_id": 1,
      "invoice": {
        "id": 1,
        "number": "001/504/LP/1025"
      },
      "client_id": 1,
      "client": {
        "id": 1,
        "code": "0000",
        "name": "PUBLIC"
      },
      "description": "Adjustment for invoice",
      "dpp_amount": 100000.00,
      "ppn_amount": 11000.00,
      "total_amount": 111000.00,
      "auto_journal": true,
      "is_posted": false,
      "posted_date": null,
      "items_count": 1,
      "created_at": "2026-03-25T10:00:00Z",
      "updated_at": "2026-03-25T10:00:00Z"
    },
    {
      "id": 2,
      "number": "DCN-202603-000121",
      "type": "D",
      "type_label": "Debit",
      "date": "2026-03-26",
      "invoice_id": 3,
      "invoice": {
        "id": 3,
        "number": "001/505/PP/0925"
      },
      "client_id": 1,
      "client": {
        "id": 1,
        "code": "0000",
        "name": "PUBLIC"
      },
      "description": "Adjustment for invoice",
      "dpp_amount": 200000.00,
      "ppn_amount": 22000.00,
      "total_amount": 222000.00,
      "auto_journal": true,
      "is_posted": false,
      "posted_date": null,
      "items_count": 1,
      "created_at": "2026-03-26T11:00:00Z",
      "updated_at": "2026-03-26T11:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 2,
    "total": 2
  }
}
```

### Database Info:
- **Total DCN Records:** 2
- **Types:** D (Debit), C (Credit)
- **Example Numbers:** DCN-202603-0001, DCN-202603-000121

---

## 4️⃣  GET /api/posting/payments - List All Posting Payments

**Endpoint Path:** `/api/posting/payments`  
**HTTP Method:** `GET`  
**Section:** `07 Posting > posting-payments`  
**Authorization:** Requires token (finance_admin, manager, ar_collector, super_admin)  
**Query Parameters:**
- `page` - Pagination (default: 1)
- `per_page` - Items per page (default: 15)
- `is_posted` - Filter: 0 (draft), 1 (posted)
- `search` - Search by number

### Request URL:
```
GET http://localhost:8000/api/posting/payments?page=1&per_page=15
GET http://localhost:8000/api/posting/payments?is_posted=1
GET http://localhost:8000/api/posting/payments/search?search=BP-202603
```

### Request Headers:
```
Authorization: Bearer {{token}}
Accept: application/json
```

### Response Example (200 OK):
```json
{
  "data": [
    {
      "id": 1,
      "number": "BP-202603-0001",
      "date": "2026-03-20",
      "bank_id": 1,
      "bank": {
        "id": 1,
        "code": "BM00",
        "name": "DEPOSIT IN TRANSIT"
      },
      "acc_code": "1101",
      "cdf_code": "5032",
      "client_id": 1,
      "client": {
        "id": 1,
        "code": "0000",
        "name": "PUBLIC"
      },
      "description": "Posting pembayaran periode March 2026",
      "total_invoice": 3330000.00,
      "debet": 3330000.00,
      "kredit": 0.00,
      "total_paid": 554490.00,
      "print_receipt": false,
      "auto_journal": true,
      "without_stamp": false,
      "is_posted": true,
      "created_at": "2026-03-20T09:00:00Z",
      "updated_at": "2026-03-20T09:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

### Database Info:
- **Total PostingPayments:** 1
- **Example ID:** 1
- **Status:** Is Posted

---

## 5️⃣  GET /api/posting/payments/search - Search Posting Payments

**Endpoint Path:** `/api/posting/payments/search`  
**HTTP Method:** `GET`  
**Section:** `07 Posting > posting-payments`  
**Authorization:** Requires token (finance_admin, manager, ar_collector, super_admin)  
**Query Parameters:**
- `search` - Search by number or date (string)
- `is_posted` - Filter: 0 (draft), 1 (posted)
- `period` - Filter by period (YYYY-MM format)
- `page` - Pagination (default: 1)
- `per_page` - Items per page (default: 15)

### Request URL Examples:
```
GET http://localhost:8000/api/posting/payments/search?search=BP-202603
GET http://localhost:8000/api/posting/payments/search?is_posted=1&period=2026-03
GET http://localhost:8000/api/posting/payments/search?search=2026-03-20
```

### Request Headers:
```
Authorization: Bearer {{token}}
Accept: application/json
```

### Response Example (200 OK):
```json
{
  "data": [
    {
      "id": 1,
      "number": "BP-202603-0001",
      "date": "2026-03-20",
      "bank": {
        "id": 1,
        "code": "BM00",
        "name": "DEPOSIT IN TRANSIT"
      },
      "client": {
        "id": 1,
        "code": "0000",
        "name": "PUBLIC"
      },
      "total_paid": 554490.00,
      "is_posted": true,
      "invoices_count": 1,
      "costs_count": 2
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

### Database Info:
- **Example Search:** BP-202603
- **Available Periods:** 2026-03
- **Results:** 1 record found

---

## 6️⃣  GET /api/posting/payments/{id} - Get Posting Payment Detail

**Endpoint Path:** `/api/posting/payments/{id}`  
**HTTP Method:** `GET`  
**Section:** `07 Posting > posting-payments`  
**Authorization:** Requires token (finance_admin, manager, ar_collector, super_admin)  
**Path Parameter:** `id` = PostingPayment ID (example: 1)

### Request URL:
```
GET http://localhost:8000/api/posting/payments/1
```

### Request Headers:
```
Authorization: Bearer {{token}}
Accept: application/json
```

### Response Example (200 OK):
```json
{
  "id": 1,
  "number": "BP-202603-0001",
  "date": "2026-03-20",
  "bank_id": 1,
  "bank": {
    "id": 1,
    "code": "BM00",
    "name": "DEPOSIT IN TRANSIT"
  },
  "acc_code": "1101",
  "cdf_code": "5032",
  "client_id": 1,
  "client": {
    "id": 1,
    "code": "0000",
    "name": "PUBLIC"
  },
  "description": "Posting pembayaran periode March 2026",
  "total_invoice": 3330000.00,
  "debet": 3330000.00,
  "kredit": 0.00,
  "total_paid": 554490.00,
  "print_receipt": false,
  "auto_journal": true,
  "without_stamp": false,
  "is_posted": true,
  "created_at": "2026-03-20T09:00:00Z",
  "updated_at": "2026-03-20T09:00:00Z",
  "invoices": [
    {
      "id": 1,
      "posting_payment_id": 1,
      "invoice_id": 1,
      "invoice": {
        "id": 1,
        "number": "001/504/LP/1025",
        "amount": 3330000.00
      }
    }
  ],
  "costs": [
    {
      "id": 1,
      "posting_payment_id": 1,
      "description": "Biaya admin",
      "amount": 250000.00,
      "created_at": "2026-03-20T09:15:00Z"
    },
    {
      "id": 2,
      "posting_payment_id": 1,
      "description": "Biaya operasional",
      "amount": 75000.00,
      "created_at": "2026-03-20T09:20:00Z"
    }
  ]
}
```

### Error Responses:

**404 Not Found:**
```json
{
  "message": "Posting payment not found"
}
```

### Database Info:
- **Example ID:** 1
- **Costs Associated:** 2 costs
- **Invoices Associated:** 1 invoice

---

## 🔍 POSTMAN COLLECTION - UPDATED STRUCTURE

### STRUKTUR FOLDER BARU:

#### **06 Finance** (UPDATED)
```
├── chart-of-accounts
│   ├── GET api/chart-of-accounts
│   ├── GET api/chart-of-accounts/{id}
│   └── GET api/chart-of-accounts/search
├── debit-credit-notes
│   ├── GET api/debit-credit-notes
│   ├── GET api/debit-credit-notes/{id}
│   ├── GET api/debit-credit-notes/search          ← NEW LOCATION
│   ├── GET api/debit-credit-notes/summary-journal
│   ├── POST api/debit-credit-notes
│   ├── PUT api/debit-credit-notes/{id}
│   └── DELETE api/debit-credit-notes/{id}
├── invoice-items
│   ├── GET api/invoice-items
│   ├── GET api/invoice-items/{id}
│   ├── POST api/invoice-items
│   ├── PUT api/invoice-items/{id}
│   └── DELETE api/invoice-items/{id}
├── invoices
│   ├── GET api/invoices
│   ├── GET api/invoices/{id}
│   ├── GET api/invoices/summary-journal
│   ├── POST api/invoices
│   ├── PUT api/invoices/{id}
│   └── DELETE api/invoices/{id}
├── invoice-license                                   ← NEW SECTION
│   ├── GET api/invoice-license                    ← NEW
│   ├── GET api/invoice-license/{id}               ← NEW
│   └── GET api/invoice-license/summary-journal    (existing if available)
├── payments
│   ├── GET api/payments
│   ├── GET api/payments/{id}
│   ├── POST api/payments
│   ├── PUT api/payments/{id}
│   └── DELETE api/payments/{id}
└── receivables
    ├── GET api/receivables
    └── GET api/receivables/{id}
```

#### **07 Posting** (UPDATED)
```
├── journals
│   └── GET api/journals
├── posting-omzet
│   └── POST api/posting/omzet
├── posting-payments                                  ← NEW SECTION
│   ├── GET api/posting/payments                   ← NEW
│   ├── GET api/posting/payments/search            ← NEW
│   ├── GET api/posting/payments/{id}              ← NEW
│   ├── GET api/posting/payments/summary-journal   (if available)
│   ├── POST api/posting/payments
│   └── posting-costs
│       ├── GET api/posting/payments/{id}/costs
│       ├── POST api/posting/payments/{id}/costs
│       ├── PUT api/posting/payments/{id}/costs/{cost}
│       └── DELETE api/posting/payments/{id}/costs/{cost}
└── posting-omzet
    └── POST api/posting/omzet
```

---

## 🛠️ CARA MENAMBAH ENDPOINTS KE POSTMAN

### Option 1: Manual (Recommended untuk Clarity)
1. Buka Postman Collection: `FitArt JPAS API Complete.postman_collection.json`
2. Di folder `06 Finance`:
   - Buat subfolder baru: `invoice-license`
   - Tambahkan 3 request (GET /api/invoice-license, GET /api/invoice-license/{id}, GET /api/debit-credit-notes/search)
3. Di folder `07 Posting`:
   - Buat subfolder baru: `posting-payments`
   - Tambahkan 3 request (GET /api/posting/payments, GET /api/posting/payments/search, GET /api/posting/payments/{id})

### Option 2: Import dari File
- Saya akan generate JSON file terpisah yang bisa di-import langsung

---

## ⚙️ TEST CHECKLIST

### Invoice License Endpoints:

**GET /api/invoice-license**
- [ ] With token (finance_admin atau super_admin)
- [ ] Status: 200 OK
- [ ] Response contains: data[], meta[], links[]
- [ ] Total > 0

**GET /api/invoice-license/1**
- [ ] With token (superadmin)
- [ ] Using ID 1 (confirmed exists)
- [ ] Status: 200 OK
- [ ] Response contains: id, number, date, total, client, items

**GET /api/debit-credit-notes/search**
- [ ] With token
- [ ] Try: ?search=DCN-202603
- [ ] Try: ?type=D
- [ ] Try: ?is_posted=0
- [ ] Status: 200 OK

### Posting Payment Endpoints:

**GET /api/posting/payments**
- [ ] With token (finance_admin atau super_admin)
- [ ] Status: 200 OK
- [ ] Response contains: data[], meta
- [ ] Total = 1

**GET /api/posting/payments/1**
- [ ] With token
- [ ] Status: 200 OK
- [ ] Response contains: costs[], invoices[]
- [ ] is_posted = true

**GET /api/posting/payments/search**
- [ ] Try: ?search=BP-202603
- [ ] Try: ?is_posted=1
- [ ] Status: 200 OK

---

## 💡 NOTES & BEST PRACTICES

### Authorization
- Semua 6 endpoint memerlukan: `Authorization: Bearer {{token}}`
- Token disimpan di variable: `{{token}}`
- Bisa didapat dari POST /api/login

### Pagination
- Endpoints dengan list (invoice-license, debit-credit-notes/search, posting/payments)
- Support: `?page=1&per_page=15`
- Response: Includes `meta` dengan pagination info

### Database Status ✅
- ✅ GET /api/invoice-license: 6 invoices tersedia
- ✅ GET /api/invoice-license/{id}: Use ID 1 (confirmed)
- ✅ GET /api/debit-credit-notes/search: 2 records tersedia
- ✅ GET /api/posting/payments: 1 record tersedia
- ✅ GET /api/posting/payments/{id}: Use ID 1 (confirmed)
- ✅ GET /api/posting/payments/search: Ready

### Common Errors & Solutions

**401 Unauthorized**
- Missing bearer token
- Token expired
- Solution: POST /api/login first

**404 Not Found**
- Resource ID belum ada
- Solution: Use correct ID (1 or higher)

**422 Unprocessable Entity (Search Endpoints)**
- Invalid query parameter
- Solution: Use only valid parameters (search, type, is_posted, etc)

---

**NEXT STEPS:**
1. ✅ Database verified - ALL 6 endpoints punya data
2. ✅ Folder structure planned - WHERE to put each endpoint
3. 🔲 Import ke Postman - Bisa manual atau via JSON file
4. 🔲 Test all 6 endpoints - Pastikan semua working
5. 🔲 Check existing endpoints - Apakah ada yang berantakan/belum tertata rapi
