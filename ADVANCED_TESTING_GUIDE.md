# 🔧 ADVANCED TESTING GUIDE (Developer Edition)

## Table of Contents
1. [API Endpoint Testing](#api-endpoint-testing)
2. [Validation Testing](#validation-testing)
3. [Error Scenarios](#error-scenarios)
4. [Performance Testing](#performance-testing)
5. [Database Integrity](#database-integrity)
6. [Security Testing](#security-testing)

---

## API ENDPOINT TESTING

### Team Members Endpoints

#### POST /api/team-members (Create)
```bash
curl -X POST http://localhost/api/team-members \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "code": "06",
    "name": "AGUS HERMAWAN",
    "position": "MARKETING MANAGER",
    "status": "STATUS1",
    "is_active": true
  }'
```

**Expected Response** (201 Created):
```json
{
  "success": true,
  "message": "Team member created successfully",
  "data": {
    "id": 6,
    "code": "06",
    "name": "AGUS HERMAWAN",
    "position": "MARKETING MANAGER",
    "status": "STATUS1",
    "is_active": true,
    "created_at": "2026-04-25T07:30:00Z",
    "updated_at": "2026-04-25T07:30:00Z"
  }
}
```

---

#### GET /api/team-members (Read All)
```bash
curl -X GET http://localhost/api/team-members \
  -H "Authorization: Bearer TOKEN"
```

**Expected Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "01",
      "name": "IMAM S ARIFIN",
      "position": "MANAGER AREA",
      "status": "STATUS1",
      "is_active": true
    },
    ...
  ],
  "total": 4,
  "page": 1,
  "per_page": 15
}
```

---

#### GET /api/team-members/{id} (Read Single)
```bash
curl -X GET http://localhost/api/team-members/1 \
  -H "Authorization: Bearer TOKEN"
```

---

#### PUT /api/team-members/{id} (Update)
```bash
curl -X PUT http://localhost/api/team-members/4 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "position": "SENIOR PROGRAMMER"
  }'
```

**Expected Response** (200 OK):
```json
{
  "success": true,
  "message": "Team member updated successfully",
  "data": {
    "id": 4,
    "code": "04",
    "name": "ADIT",
    "position": "SENIOR PROGRAMMER",
    "status": "STATUS1",
    "is_active": true,
    "updated_at": "2026-04-25T07:35:00Z"
  }
}
```

---

#### DELETE /api/team-members/{id}
```bash
curl -X DELETE http://localhost/api/team-members/6 \
  -H "Authorization: Bearer TOKEN"
```

**Expected Response** (200 OK or 204 No Content):
```json
{
  "success": true,
  "message": "Team member deleted successfully"
}
```

---

### Product Groups Endpoints

#### POST /api/product-groups (Create)
```bash
curl -X POST http://localhost/api/product-groups \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "code": "SERVICE",
    "name": "SERVICE DAN MAINTENANCE",
    "acc_omzet": "5033",
    "cdf_piutang": "1535",
    "is_active": true
  }'
```

---

#### GET /api/product-groups (Read with Relations)
```bash
curl -X GET "http://localhost/api/product-groups?include=products" \
  -H "Authorization: Bearer TOKEN"
```

**Expected**: Each product group includes array of related products

---

### Products Endpoints

#### POST /api/products (Create with Relations)
```bash
curl -X POST http://localhost/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
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
  }'
```

**Validation**: `product_group_id` must exist in `product_groups` table

---

### Invoices Endpoints

#### POST /api/invoices (Create)
```bash
curl -X POST http://localhost/api/invoices \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "invoice_number": "INV-2026-001",
    "date": "2026-04-25",
    "customer_name": "PT INDO JAYA MAKMUR",
    "status": "DRAFT",
    "is_active": true
  }'
```

---

#### POST /api/invoices/{id}/items (Add Invoice Items)
```bash
curl -X POST http://localhost/api/invoices/1/items \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "product_id": 2,
    "quantity": 5,
    "unit_price": 1000000
  }'
```

**Auto-calculation**: `amount = quantity × unit_price`

---

#### PUT /api/invoices/{id}/post (Change Status to POSTED)
```bash
curl -X PUT http://localhost/api/invoices/1/post \
  -H "Authorization: Bearer TOKEN"
```

**Expected**: Status changes from DRAFT to POSTED

---

### Journal Entries Endpoints

#### POST /api/journal-entries (Create)
```bash
curl -X POST http://localhost/api/journal-entries \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "entry_number": "JE-2026-001",
    "date": "2026-04-25",
    "description": "Posting invoice INV-2026-001",
    "status": "DRAFT"
  }'
```

---

#### POST /api/journal-entries/{id}/details (Add Debit/Credit)
```bash
curl -X POST http://localhost/api/journal-entries/1/details \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "account_id": "1535",
    "description": "Piutang dari invoice",
    "debit": 9000000,
    "credit": 0
  }'
```

**Validation**: Account code must exist in `chart_of_accounts` table

---

#### GET /api/journal-entries/{id}/validate (Check Balance)
```bash
curl -X GET http://localhost/api/journal-entries/1/validate \
  -H "Authorization: Bearer TOKEN"
```

**Expected Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "entry_number": "JE-2026-001",
    "total_debit": 9000000,
    "total_credit": 9000000,
    "is_balanced": true
  }
}
```

---

## VALIDATION TESTING

### Team Member Validations

| Field | Validation | Test Case | Expected |
|-------|-----------|-----------|----------|
| code | Required, Unique, Max 10 | code="06" | ✅ Pass |
| code | Duplicate | code="01" (already exists) | ❌ Error: "Code already exists" |
| code | Empty | code="" | ❌ Error: "Code is required" |
| name | Required, Max 100 | name="AGUS HERMAWAN" | ✅ Pass |
| name | Empty | name="" | ❌ Error: "Name is required" |
| position | Required, Max 100 | position="MARKETING MANAGER" | ✅ Pass |
| status | Required | status="" | ❌ Error: "Status is required" |
| is_active | Boolean | is_active=true/false | ✅ Pass |

---

### Product Group Validations

| Field | Validation | Test Case | Expected |
|-------|-----------|-----------|----------|
| code | Required, Unique | code="SERVICE" | ✅ Pass |
| code | Duplicate | code="LSA" (already exists) | ❌ Error |
| name | Required | name="SERVICE DAN MAINTENANCE" | ✅ Pass |
| acc_omzet | Nullable, Must exist in COA | acc_omzet="5033" | ✅ Pass |
| acc_omzet | Invalid reference | acc_omzet="9999" | ❌ Error: "Account not found" |
| cdf_piutang | Nullable, Must exist in COA | cdf_piutang="1535" | ✅ Pass |

---

### Product Validations

| Field | Validation | Test Case | Expected |
|-------|-----------|-----------|----------|
| code | Required, Unique | code="05" | ✅ Pass |
| name | Required | name="INVOICE MANAGEMENT SYSTEM" | ✅ Pass |
| product_group_id | Nullable, Must exist | product_group_id=1 | ✅ Pass |
| product_group_id | Invalid FK | product_group_id=999 | ❌ Error: "Product group not found" |
| author_code | Max 10 | author_code="01" | ✅ Pass |

---

### Invoice Validations

| Field | Validation | Test Case | Expected |
|-------|-----------|-----------|----------|
| invoice_number | Required, Unique | invoice_number="INV-2026-001" | ✅ Pass |
| invoice_number | Duplicate | invoice_number="INV-2026-001" (2x) | ❌ Error: "Invoice number already exists" |
| date | Required, Valid date | date="2026-04-25" | ✅ Pass |
| customer_name | Required | customer_name="PT INDO JAYA MAKMUR" | ✅ Pass |
| status | Valid enum | status="DRAFT" / "POSTED" / "PAID" | ✅ Pass |
| status | Invalid enum | status="INVALID" | ❌ Error: "Invalid status" |
| amount | Auto-calculated | - | ✓ Sum of all items |

---

### Invoice Item Validations

| Field | Validation | Test Case | Expected |
|-------|-----------|-----------|----------|
| product_id | Must exist | product_id=2 | ✅ Pass |
| product_id | Invalid FK | product_id=999 | ❌ Error: "Product not found" |
| quantity | Required, Positive | quantity=5 | ✅ Pass |
| quantity | Zero/Negative | quantity=0 or -1 | ❌ Error: "Quantity must be positive" |
| unit_price | Required, Positive | unit_price=1000000 | ✅ Pass |
| amount | Auto-calculated | - | ✓ qty × price |
| invoice_id | Must exist | invoice_id=1 | ✅ Pass |

---

### Journal Entry Validations

| Field | Validation | Test Case | Expected |
|-------|-----------|-----------|----------|
| entry_number | Required, Unique | entry_number="JE-2026-001" | ✅ Pass |
| date | Required | date="2026-04-25" | ✅ Pass |
| description | Max 500 | Long description | ✅ Pass if < 500 |
| status | Valid enum | status="DRAFT" / "POSTED" | ✅ Pass |
| account_id | Must exist in COA | account_id="1535" | ✅ Pass |
| account_id | Invalid reference | account_id="9999" | ❌ Error: "Account not found" |
| debit/credit | Non-negative | debit=9000000, credit=0 | ✅ Pass |
| debit/credit | Negative | debit=-1000 | ❌ Error: "Amount must be non-negative" |
| balance | Debit = Credit | debit=9M, credit=9M | ✅ Pass |
| balance | Not balanced | debit=9M, credit=8M | ⚠️ Warning: "Not balanced" |

---

## ERROR SCENARIOS

### Test 1: Duplicate Code Creation
```
Scenario: Create team member with code that already exists
Steps:
  1. Create team member with code="01"
  2. Try to create another with code="01"
Expected: Error message "Code already exists" with HTTP 422
```

---

### Test 2: Invalid Foreign Key
```
Scenario: Create product with non-existent product group
Steps:
  1. Submit POST /api/products with product_group_id=999
Expected: Error "Product group not found" with HTTP 422
```

---

### Test 3: Unbalanced Journal Entry
```
Scenario: Try to post journal entry with debit ≠ credit
Steps:
  1. Create journal entry
  2. Add debit 9M and credit 8M
  3. Try to post
Expected: Error "Journal not balanced" or warning
```

---

### Test 4: Missing Required Fields
```
Scenario: Submit form without required field
Steps:
  1. Create team member but skip "name" field
Expected: Error "Name field is required" with HTTP 422
```

---

### Test 5: Invalid Data Type
```
Scenario: Submit quantity as string instead of number
Steps:
  1. Create invoice item with quantity="abc"
Expected: Error "Quantity must be numeric" with HTTP 422
```

---

## PERFORMANCE TESTING

### Load Testing

```bash
# Test with 100 records
# Use Apache Bench or similar tool

ab -n 100 -c 10 http://localhost/api/team-members
# Expected: Response time < 500ms per request
```

### Large Data Sets

```php
// Create 1000 invoice items and measure performance
for ($i = 0; $i < 1000; $i++) {
    InvoiceItem::create([
        'invoice_id' => 1,
        'product_id' => rand(1, 5),
        'quantity' => rand(1, 100),
        'unit_price' => rand(100000, 10000000),
    ]);
}

// Measure load time
time {
    Invoice::with('invoiceItems.product')->find(1);
}
// Expected: < 1 second
```

---

## DATABASE INTEGRITY

### Referential Integrity

Test foreign key constraints:

```php
// Test 1: Can't delete product group with products
ProductGroup::find(1)->delete();
// Expected: Either fail with FK constraint or soft delete

// Test 2: Can't create product with invalid group
Product::create(['product_group_id' => 999, ...]);
// Expected: Error at database level or application validation

// Test 3: Invoice items reference valid products
InvoiceItem::create(['product_id' => 999, ...]);
// Expected: Error, product not found

// Test 4: Journal entries reference valid accounts
JournalEntryDetail::create(['account_id' => '9999', ...]);
// Expected: Error, account not found
```

---

### Data Consistency

```php
// Test: Invoice total matches sum of items
$invoice = Invoice::with('invoiceItems')->find(1);
$itemsTotal = $invoice->invoiceItems->sum('amount');
// Expected: $invoice->amount == $itemsTotal

// Test: Journal entry debit = credit
$entry = JournalEntry::with('details')->find(1);
$debitTotal = $entry->details->where('debit', '>', 0)->sum('debit');
$creditTotal = $entry->details->where('credit', '>', 0)->sum('credit');
// Expected: $debitTotal == $creditTotal
```

---

## SECURITY TESTING

### Authentication & Authorization

```bash
# Test 1: Access without token
curl -X GET http://localhost/api/team-members
# Expected: 401 Unauthorized

# Test 2: Access with invalid token
curl -X GET http://localhost/api/team-members \
  -H "Authorization: Bearer INVALID_TOKEN"
# Expected: 401 Unauthorized

# Test 3: Role-based access control
# Login as "ar_collector" and try to delete invoice
# Expected: 403 Forbidden if not authorized
```

---

### Input Validation & Sanitization

```php
// Test: SQL Injection
POST /api/team-members
{
  "code": "' OR '1'='1",
  "name": "Hacker"
}
// Expected: Error or safe encoding

// Test: XSS Prevention
POST /api/team-members
{
  "name": "<script>alert('xss')</script>"
}
// Expected: Escaped or filtered output
```

---

### Rate Limiting

```bash
# Test: Too many requests
for i in {1..100}; do
  curl -X GET http://localhost/api/team-members
done
# Expected: 429 Too Many Requests after limit
```

---

## TEST EXECUTION CHECKLIST

- [ ] All CRUD operations tested
- [ ] Validations tested (required, unique, foreign keys)
- [ ] Error scenarios tested
- [ ] Relationships verified
- [ ] Balance checking for journal entries
- [ ] API responses match expected format
- [ ] HTTP status codes correct (200, 201, 422, 401, etc)
- [ ] Database data consistent
- [ ] No orphaned records
- [ ] Performance acceptable (< 1s for queries)
- [ ] Security validated
- [ ] Edge cases handled
- [ ] Error messages clear and helpful

---

## COMMON ISSUES & SOLUTIONS

| Issue | Cause | Solution |
|-------|-------|----------|
| Foreign key constraint error | Related record doesn't exist | Create parent record first |
| Journal not balanced | Debit ≠ Credit | Verify amounts match |
| Invoice total wrong | Item amounts not calculated | Check product prices |
| Duplicate code error | Code exists | Use unique code |
| Validation failed | Invalid input format | Check data type and format |
| Soft delete shows in list | Query doesn't exclude deleted | Add `->whereNull('deleted_at')` |

---

**Last Updated**: 25 April 2026
**Version**: 1.0 (Advanced)
**Target Audience**: QA Engineers, Developers
