# FitArt API - Testing Guide untuk Endpoints Yang Belum Ditest

**Document:** API Testing Guide - Missing Endpoints  
**Generated:** April 12, 2026  
**Total Missing Endpoints:** 13

---

## SKEMA TESTING

Setiap endpoint akan ditest dengan:
1. **VALID Request** - Dengan data yang benar
2. **INVALID Request** - Edge case dan error validation

---

## SECTION 1: USER STATUS MANAGEMENT

### 1.1 POST /api/users/{user}/assign-role
**Deskripsi:** Assign role baru ke user. User bisa punya multiple roles.

**Prerequisites:**
- User ID yang valid (bisa dari GET /users)
- Role yang valid, contoh: `super_admin`, `finance_admin`, `sales_operator`, `manager`, `ar_collector`

**Request Body:**
```json
{
  "role": "finance_admin"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/users/1/assign-role \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "role": "finance_admin"
  }'
```

**Postman Request:**
```json
{
  "method": "POST",
  "url": "{{base_url}}/api/users/1/assign-role",
  "auth": {
    "type": "bearer",
    "bearer": [{"key": "token", "value": "{{token}}", "type": "string"}]
  },
  "body": {
    "mode": "raw",
    "raw": "{\"role\": \"finance_admin\"}"
  }
}
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "Role assigned successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "finance_admin",
    "roles": ["sales_operator", "finance_admin"],
    "status": "active"
  }
}
```

**Response - Error 404 (User not found):**
```json
{
  "message": "No query results for model [App\\Models\\User] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
}
```

**Response - Error 422 (Invalid role):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "role": ["Invalid role value"]
  }
}
```

---

### 1.2 POST /api/users/{user}/toggle-status
**Deskripsi:** Toggle status user antara active <-> inactive.

**Request Body:**
```json
{}
```
(Empty body, hanya toggle existing status)

**cURL:**
```bash
curl -X POST http://localhost:8000/api/users/1/toggle-status \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json"
```

**Response - Success (200) - User Active → Inactive:**
```json
{
  "success": true,
  "message": "User status updated",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "status": "inactive",
    "updated_at": "2026-04-12T10:30:00Z"
  }
}
```

**Response - Success (200) - User Inactive → Active:**
```json
{
  "success": true,
  "message": "User status updated",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active",
    "updated_at": "2026-04-12T10:31:00Z"
  }
}
```

**Response - Error 404:**
```json
{
  "message": "No query results for model [App\\Models\\User] 999"
}
```

---

## SECTION 2: MASTER DATA TOGGLE STATUS

### 2.1 POST /api/invoice-types/{invoiceType}/toggle-status
**Deskripsi:** Toggle status invoice type antara active <-> inactive.

**Prerequisites:**
- Invoice Type ID yang valid (dari GET /invoice-types)

**Request Body:**
```json
{}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/invoice-types/1/toggle-status \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json"
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "Status updated successfully",
  "data": {
    "id": 1,
    "name": "INVOICE LICENSE",
    "description": "Invoice untuk penjualan lisensi",
    "status": "inactive",
    "updated_at": "2026-04-12T10:32:00Z"
  }
}
```

---

### 2.2 POST /api/master-item-products/{masterItemProduct}/toggle-status
**Deskripsi:** Toggle status master item product (aktif/nonaktif).

**Prerequisites:**
- Master Item Product ID yang valid (dari GET /master-item-products)

**Request Body:**
```json
{}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/master-item-products/1/toggle-status \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json"
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "Status updated successfully",
  "data": {
    "id": 1,
    "code": "MIP-1",
    "name": "Master Item Product 1",
    "acc_omzet": "5032",
    "acc_piutang": "1532",
    "status": "inactive",
    "updated_at": "2026-04-12T10:33:00Z"
  }
}
```

---

## SECTION 3: SEARCH ENDPOINTS

### 3.1 GET /api/work-orders/search
**Deskripsi:** Cari work order dengan query parameters: nomor, tanggal, klien, deskripsi.

**Query Parameters:**
```
?number=WO001
?client_name=Prambanan
?date_from=2026-01-01&date_to=2026-03-31
?q=searchterm
```

**cURL:**
```bash
# Search by number
curl -X GET "http://localhost:8000/api/work-orders/search?number=WO001" \
  -H "Authorization: Bearer TOKEN"

# Search by client
curl -X GET "http://localhost:8000/api/work-orders/search?client_name=Prambanan" \
  -H "Authorization: Bearer TOKEN"

# Search by date range
curl -X GET "http://localhost:8000/api/work-orders/search?date_from=2026-01-01&date_to=2026-03-31" \
  -H "Authorization: Bearer TOKEN"

# Search by general query
curl -X GET "http://localhost:8000/api/work-orders/search?q=maintenance" \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success (200):**
```json
{
  "success": true,
  "filter": {
    "number": "WO001",
    "client_name": null,
    "date_from": null,
    "date_to": null,
    "q": null
  },
  "total": 1,
  "data": [
    {
      "id": 1,
      "number": "WO001",
      "client_id": 1,
      "client_name": "PT Prambanan Dwipaka",
      "description": "Installation & Setup Server",
      "date": "2026-03-15",
      "status": "completed",
      "total_amount": 5000000
    }
  ]
}
```

**Response - Not Found (0 results):**
```json
{
  "success": true,
  "filter": {
    "number": "WO999",
    "client_name": null,
    "date_from": null,
    "date_to": null,
    "q": null
  },
  "total": 0,
  "data": []
}
```

---

### 3.2 POST /api/work-orders/{work_order}/assign-team
**Deskripsi:** Assign team members ke work order.

**Prerequisites:**
- Work Order ID yang valid
- Team Member IDs yang valid (dari GET /team-members/search)

**Request Body:**
```json
{
  "team_members": [1, 2, 3]
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/work-orders/1/assign-team \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "team_members": [1, 2]
  }'
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "Team assigned successfully",
  "data": {
    "id": 1,
    "number": "WO001",
    "client_name": "PT Prambanan Dwipaka",
    "description": "Installation & Setup Server",
    "team_members": [
      {
        "id": 1,
        "name": "Ahmad Rizki",
        "email": "ahmad@company.com",
        "role": "Senior Engineer"
      },
      {
        "id": 2,
        "name": "Budi Santoso",
        "email": "budi@company.com",
        "role": "Junior Engineer"
      }
    ]
  }
}
```

**Response - Error 422 (Invalid team member IDs):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "team_members": [
      "Team member with id 999 does not exist"
    ]
  }
}
```

---

### 3.3 GET /api/stop-licenses/search
**Deskripsi:** Cari stop license dengan query parameters.

**Query Parameters:**
```
?number=SL001
?client_name=Prambanan
?date_from=2026-01-01&date_to=2026-03-31
?q=searchterm
```

**cURL:**
```bash
# Search by number
curl -X GET "http://localhost:8000/api/stop-licenses/search?number=SL001" \
  -H "Authorization: Bearer TOKEN"

# Search by general query
curl -X GET "http://localhost:8000/api/stop-licenses/search?q=renovation" \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success (200):**
```json
{
  "success": true,
  "filter": {
    "number": "SL001",
    "client_name": null,
    "date_from": null,
    "date_to": null,
    "q": null
  },
  "total": 1,
  "data": [
    {
      "id": 1,
      "number": "SL001",
      "license_id": 5,
      "client_name": "PT Prambanan Dwipaka",
      "license_name": "CCTV Premium",
      "description": "Stop license for renovation",
      "date_from": "2026-04-01",
      "date_to": "2026-04-30",
      "status": "active"
    }
  ]
}
```

---

## SECTION 4: PRINT ENDPOINTS

### 4.1 GET /api/print/invoices/batch
**Deskripsi:** Get status print batch invoices.

**Query Parameters:**
```
?batch_id=uuid-here
```

**cURL:**
```bash
curl -X GET "http://localhost:8000/api/print/invoices/batch?batch_id=123e4567-e89b-12d3-a456-426614174000" \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success (200):**
```json
{
  "success": true,
  "batch_id": "123e4567-e89b-12d3-a456-426614174000",
  "status": "completed",
  "total_invoices": 3,
  "processed": 3,
  "failed": 0,
  "data": [
    {
      "invoice_id": 1,
      "invoice_number": "001/504/LP/1025",
      "status": "completed",
      "file_path": "storage/prints/invoices/001-504-LP-1025.pdf"
    },
    {
      "invoice_id": 3,
      "invoice_number": "001/505/PP/0925",
      "status": "completed",
      "file_path": "storage/prints/invoices/001-505-PP-0925.pdf"
    }
  ]
}
```

**Response - Batch in progress:**
```json
{
  "success": true,
  "batch_id": "123e4567-e89b-12d3-a456-426614174000",
  "status": "processing",
  "total_invoices": 5,
  "processed": 2,
  "failed": 0
}
```

---

### 4.2 POST /api/print/invoices/batch
**Deskripsi:** Queue batch invoices untuk diprint. Response berisi batch_id untuk tracking.

**Request Body:**
```json
{
  "invoice_ids": [1, 3, 4]
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/print/invoices/batch \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_ids": [1, 3, 4]
  }'
```

**Response - Success (202 - Accepted):**
```json
{
  "success": true,
  "message": "Print batch queued successfully",
  "batch_id": "123e4567-e89b-12d3-a456-426614174000",
  "invoice_ids": [1, 3, 4],
  "status": "queued",
  "instruction": "Use batch_id to track progress with GET /print/invoices/batch?batch_id=..."
}
```

**Response - Error 422 (Invalid invoice IDs):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "invoice_ids": [
      "The invoice_ids field must be an array.",
      "Invoice with id 999 not found"
    ]
  }
}
```

---

### 4.3 GET /api/print/invoices/{invoice}/pdf
**Deskripsi:** Download PDF invoice secara langsung.

**Prerequisites:**
- Invoice ID yang valid
- Invoice harus sudah posted untuk bisa diprint

**cURL:**
```bash
curl -X GET http://localhost:8000/api/print/invoices/1/pdf \
  -H "Authorization: Bearer TOKEN" \
  --output invoice-001-504-LP-1025.pdf
```

**Postman:**
- Method: GET
- URL: {{base_url}}/api/print/invoices/1/pdf
- Send and download: Check "Send and download" option
- Response akan berupa PDF file

**Response - Success (200 - PDF file):**
```
[Binary PDF content]
Content-Type: application/pdf
Content-Disposition: attachment; filename="001-504-LP-1025.pdf"
```

**Response - Error 404 (Invoice not found):**
```json
{
  "message": "No query results for model [App\\Models\\Invoice] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
}
```

**Response - Error 422 (Invoice not posted):**
```json
{
  "success": false,
  "message": "Invoice must be posted before printing",
  "invoice_id": 1
}
```

---

### 4.4 GET /api/print/receipts/{posting_payment}/pdf
**Deskripsi:** Download PDF receipt dari posting pembayaran.

**Prerequisites:**
- Posting Payment ID yang valid (dari GET /posting/payments)

**cURL:**
```bash
curl -X GET http://localhost:8000/api/print/receipts/1/pdf \
  -H "Authorization: Bearer TOKEN" \
  --output receipt-BM01-JA001-0925.pdf
```

**Response - Success (200 - PDF file):**
```
[Binary PDF content]
Content-Type: application/pdf
Content-Disposition: attachment; filename="receipt-BM01-JA001-0925.pdf"
```

**Response - Error 404:**
```json
{
  "message": "No query results for model [App\\Models\\PostingPayment] 999"
}
```

---

## SECTION 5: PROTECTION ENDPOINTS

### 5.1 GET /api/protections
**Deskripsi:** List semua protection records.

**Request Body:** none

**cURL:**
```bash
curl -X GET http://localhost:8000/api/protections \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Protection Rule 1",
      "description": "Protect certain accounts",
      "type": "account_protection",
      "rule": "PROTECTED_ACCOUNTS",
      "active": true,
      "created_at": "2026-03-01T08:00:00Z"
    },
    {
      "id": 2,
      "name": "Protection Rule 2",
      "description": "Protect posting period",
      "type": "period_protection",
      "rule": "LOCKED_PERIODS",
      "active": false,
      "created_at": "2026-03-02T09:15:00Z"
    }
  ]
}
```

---

### 5.2 GET /api/protections/{protection}
**Deskripsi:** Get detail protection record.

**Prerequisites:**
- Protection ID yang valid

**cURL:**
```bash
curl -X GET http://localhost:8000/api/protections/1 \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Protection Rule 1",
    "description": "Protect certain accounts",
    "type": "account_protection",
    "rule": "PROTECTED_ACCOUNTS",
    "details": {
      "protected_accounts": ["1100", "1200", "3100"],
      "protection_level": "high"
    },
    "active": true,
    "created_at": "2026-03-01T08:00:00Z",
    "updated_at": "2026-03-01T08:00:00Z"
  }
}
```

---

### 5.3 POST /api/protections
**Deskripsi:** Create protection rule baru.

**Request Body:**
```json
{
  "name": "New Protection Rule",
  "description": "Protect critical accounts",
  "type": "account_protection",
  "rule": "PROTECTED_ACCOUNTS",
  "details": {
    "protected_accounts": ["1100", "1200"],
    "protection_level": "high"
  },
  "active": true
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/protections \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Protection Rule",
    "description": "Protect critical accounts",
    "type": "account_protection",
    "rule": "PROTECTED_ACCOUNTS",
    "details": {
      "protected_accounts": ["1100", "1200"],
      "protection_level": "high"
    },
    "active": true
  }'
```

**Response - Success (201):**
```json
{
  "success": true,
  "message": "Protection rule created successfully",
  "data": {
    "id": 3,
    "name": "New Protection Rule",
    "description": "Protect critical accounts",
    "type": "account_protection",
    "rule": "PROTECTED_ACCOUNTS",
    "active": true,
    "created_at": "2026-04-12T10:35:00Z"
  }
}
```

**Response - Error 422 (Validation):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required"],
    "type": ["Invalid protection type"]
  }
}
```

---

### 5.4 PUT /api/protections/{protection}
**Deskripsi:** Update protection rule.

**Request Body:**
```json
{
  "name": "Updated Protection Rule",
  "description": "Updated description",
  "type": "account_protection",
  "rule": "PROTECTED_ACCOUNTS",
  "details": {
    "protected_accounts": ["1100", "1200", "1300"],
    "protection_level": "critical"
  },
  "active": true
}
```

**cURL:**
```bash
curl -X PUT http://localhost:8000/api/protections/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Protection Rule",
    "description": "Updated description",
    "type": "account_protection",
    "active": true
  }'
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "Protection rule updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Protection Rule",
    "description": "Updated description",
    "type": "account_protection",
    "rule": "PROTECTED_ACCOUNTS",
    "active": true,
    "updated_at": "2026-04-12T10:36:00Z"
  }
}
```

---

### 5.5 DELETE /api/protections/{protection}
**Deskripsi:** Delete protection rule.

**Request Body:** none

**cURL:**
```bash
curl -X DELETE http://localhost:8000/api/protections/1 \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "Protection rule deleted successfully",
  "deleted": true
}
```

**Response - Error 404:**
```json
{
  "message": "No query results for model [App\\Models\\Protection] 999"
}
```

---

## TESTING CHECKLIST

Copy list ini ke Postman atau testing tools:

### Users
- [ ] POST /api/users/{user}/assign-role (Valid)
- [ ] POST /api/users/{user}/assign-role (Invalid user)
- [ ] POST /api/users/{user}/toggle-status (Active → Inactive)
- [ ] POST /api/users/{user}/toggle-status (Inactive → Active)

### Master Data Toggle
- [ ] POST /api/invoice-types/{id}/toggle-status
- [ ] POST /api/master-item-products/{id}/toggle-status

### Search
- [ ] GET /api/work-orders/search?number=...
- [ ] GET /api/work-orders/search?client_name=...
- [ ] GET /api/work-orders/search?date_from=...&date_to=...
- [ ] POST /api/work-orders/{id}/assign-team (Valid)
- [ ] POST /api/work-orders/{id}/assign-team (Invalid team_member)
- [ ] GET /api/stop-licenses/search?number=...
- [ ] GET /api/stop-licenses/search?q=...

### Print
- [ ] POST /api/print/invoices/batch (Valid)
- [ ] POST /api/print/invoices/batch (Invalid invoice_id)
- [ ] GET /api/print/invoices/batch?batch_id=... (Processing)
- [ ] GET /api/print/invoices/batch?batch_id=... (Completed)
- [ ] GET /api/print/invoices/{id}/pdf (Valid PDF)
- [ ] GET /api/print/invoices/{id}/pdf (Not found)
- [ ] GET /api/print/receipts/{id}/pdf (Valid PDF)
- [ ] GET /api/print/receipts/{id}/pdf (Not found)

### Protection (Full CRUD)
- [ ] GET /api/protections (List)
- [ ] GET /api/protections/{id} (Detail)
- [ ] POST /api/protections (Create)
- [ ] POST /api/protections (Validation error)
- [ ] PUT /api/protections/{id} (Update)
- [ ] DELETE /api/protections/{id} (Valid)
- [ ] DELETE /api/protections/{id} (Not found)

**Total Test Cases:** 35+

---

## POSTMAN COLLECTION UPDATE

Tiga cara untuk add ke Postman:

### Option 1: Upload ke environment baru
Buat folder baru di Postman: "FitArt - Missing Endpoints"

### Option 2: Update existing collection
Tambahkan ke folder yang sudah ada

### Option 3: Import dari file
Export section ini sebagai JSON collection dan import ke Postman

---

## CATATAN PENTING

1. **Authorization:** Tous endpoints memerlukan Bearer token (kecuali /login)
2. **Role:** Beberapa endpoint protected by role:
   - `/users/**` → requires `super_admin`
   - `/invoice-types/**` → requires `super_admin` atau `finance_admin`
   - `/master-item-products/**` → requires `super_admin` atau `finance_admin`
   - `/work-orders/**` → requires `super_admin` atau `sales_operator`
   - `/stop-licenses/**` → requires `super_admin` atau `sales_operator`
   - `/print/**` → requires `super_admin` atau `finance_admin` atau `manager` atau `ar_collector`
   - `/protections/**` → requires `super_admin` ONLY

3. **Period Lock:** Write operations pada finance/posting endpoints protected oleh `period.unlocked` middleware

4. **Error Standardization:** All errors follow standard format:
   ```json
   {
     "message": "...",
     "errors": { "field": ["error message"] }
   }
   ```

