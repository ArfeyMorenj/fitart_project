# ✅ UPDATED FitArt API TESTING GUIDE
## Based on Comprehensive Analysis - Fokus pada PUT & DELETE yang Belum Ditest

**Version:** 2.0 (Updated April 13, 2026)  
**Focus:** Complete ALL PUT and DELETE operations + Missing Features

---

## 🎯 PRIORITY 1: DELETE OPERATIONS (❌ 0% Tested)

Semua endpoint DELETE masih belum ditesting. Ini adalah CRITICAL untuk verify bahwa delete function bekerja.

### DELETE Pattern untuk Master Data

Semua master data mengikuti pattern yang sama:

```bash
DELETE /api/{resource}/{id}
```

---

### 1.1 DELETE /api/users/{id}
**Tested Status:** ❌ NOT TESTED  
**Complexity:** Simple

**Prerequisites:**
- User ID yang valid (dari GET /api/users)
- User yang ingin didelete bukanlah current user


**cURL:**
```bash
curl -X DELETE http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json"
```

**Postman:**
```
Method: DELETE
URL: {{base_url}}/api/users/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "User deleted successfully",
  "deleted": true
}
```

**Response - Error 404 (User not found):**
```json
{
  "message": "No query results for model [App\\Models\\User] 999"
}
```

**Response - Error 403 (Not authorized):**
```json
{
  "message": "Forbidden",
  "status": 403
}
```

**Testing Checklist:**
```
□ DELETE valid user ID → Should return 200 "deleted": true
□ DELETE invalid user ID → Should return 404
□ Verify user is deleted: GET /api/users/{id} → Should return 404
□ DELETE super_admin user → Should fail if protection exists
□ DELETE self user → Should fail with error
```

---

### 1.2 DELETE /api/companies/{id}
**Tested Status:** ❌ NOT TESTED

**cURL:**
```bash
curl -X DELETE http://localhost:8000/api/companies/1 \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success:**
```json
{
  "success": true,
  "message": "Company deleted successfully",
  "deleted": true
}
```

**Testing Checklist:**
```
□ DELETE valid company ID
□ DELETE invalid company ID → 404
□ Verify deletion by GET
□ DELETE company with related data → Should validate/restrict
```

---

### 1.3 DELETE /api/clients/{id}
**Tested Status:** ❌ NOT TESTED

**cURL:**
```bash
curl -X DELETE http://localhost:8000/api/clients/1 \
  -H "Authorization: Bearer TOKEN"
```

**Response - Success:**
```json
{
  "success": true,
  "message": "Client deleted successfully",
  "deleted": true
}
```

---

### 1.4 DELETE /api/banks/{id}
**Tested Status:** ❌ NOT TESTED  
**Note:** See screenshot for structure

**cURL:**
```bash
curl -X DELETE http://localhost:8000/api/banks/1 \
  -H "Authorization: Bearer TOKEN"
```

---

### 1.5 DELETE /api/team-members/{id}
**Tested Status:** ❌ NOT TESTED

---

### 1.6 DELETE /api/products/{id}
**Tested Status:** ❌ NOT TESTED

---

### 1.7 DELETE /api/items/{id}
**Tested Status:** ❌ NOT TESTED

---

### 1.8 DELETE /api/invoices/{id}
**Tested Status:** ❌ NOT TESTED  
**Important:** Should only delete if not posted

**Expected Behavior:**
```json
// If invoice not posted → Success
{ "success": true, "deleted": true }

// If invoice already posted → Error
{ 
  "success": false,
  "message": "Cannot delete posted invoice",
  "status": 422
}
```

---

### 1.9 DELETE /api/invoice-items/{id}
**Tested Status:** ❌ NOT TESTED

---

### 1.10 DELETE /api/payments/{id}
**Tested Status:** ❌ NOT TESTED

---

### 1.11 DELETE /api/debit-credit-notes/{id}
**Tested Status:** ❌ NOT TESTED

---

### 1.12 DELETE /api/work-orders/{id}
**Tested Status:** ❌ NOT TESTED

---

### 1.13-1.18: DELETE Other Resources
```
❌ DELETE /api/installations/{id}
❌ DELETE /api/licenses/{id}
❌ DELETE /api/stop-licenses/{id}
❌ DELETE /api/protections/{id}
❌ DELETE /api/tax-series/{id}
❌ DELETE /api/posting/payments/{id}/costs/{cost}
```

---

## 🎯 PRIORITY 2: PUT/UPDATE OPERATIONS (❌ 0% Tested)

Semua endpoint PUT masih belum ditesting. Gunakan existing data dari GET untuk update.

### UPDATE Pattern

```bash
PUT /api/{resource}/{id}
Content-Type: application/json

{
  "field": "new_value",
  "field2": "new_value2"
}
```

---

### 2.1 PUT /api/users/{id}
**Tested Status:** ❌ NOT TESTED

**Get current data first:**
```bash
GET http://localhost:8000/api/users/1
```

**Then update:**
```bash
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "email": "newemail@example.com"
  }'
```

**Response - Success (200):**
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Name",
    "email": "newemail@example.com",
    "role": "super_admin"
  },
  "was_changed": true,
  "changes": {
    "name": "Updated Name",
    "email": "newemail@example.com"
  }
}
```

**Response - Error 422 (Validation):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

**Testing Checklist:**
```
□ GET current user data
□ Update single field (name)
□ Update multiple fields
□ Update email (check unique validation)
□ Verify changes saved: GET again
□ Try invalid data → Should return 422
□ Update non-existent user → 404
```

---

### 2.2 PUT /api/companies/{id}
**Tested Status:** ❌ NOT TESTED

**Sample body:**
```json
{
  "name": "Updated Company Name",
  "description": "Updated description"
}
```

---

### 2.3 PUT /api/clients/{id}
**Tested Status:** ❌ NOT TESTED

**Sample body:**
```json
{
  "name": "Updated Client Name",
  "code": "NEW-CODE"
}
```

---

### 2.4 PUT /api/banks/{id}
**Tested Status:** ❌ NOT TESTED

---

### 2.5 PUT /api/team-members/{id}
**Tested Status:** ❌ NOT TESTED

---

### 2.6 PUT /api/products/{id}
**Tested Status:** ❌ NOT TESTED

---

### 2.7 PUT /api/items/{id}
**Tested Status:** ❌ NOT TESTED

---

### 2.8 PUT /api/invoices/{id}
**Tested Status:** ❌ NOT TESTED  
**Important:** Can only update if not posted

**Sample body:**
```json
{
  "invoice_note": "Updated note",
  "due_date": "2026-04-20"
}
```

---

### 2.9-2.18: PUT Other Resources
```
❌ PUT /api/invoice-items/{id}
❌ PUT /api/payments/{id}
❌ PUT /api/debit-credit-notes/{id}
❌ PUT /api/work-orders/{id}
❌ PUT /api/installations/{id}
❌ PUT /api/licenses/{id}
❌ PUT /api/stop-licenses/{id}
❌ PUT /api/protections/{id}
```

---

## 🎯 PRIORITY 3: MISSING ACTION ENDPOINTS (❌ Not Tested)

### 3.1 POST /api/users/{id}/assign-role
**Tested Status:** ❌ NOT TESTED

```bash
curl -X POST http://localhost:8000/api/users/1/assign-role \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"role": "finance_admin"}'
```

---

### 3.2 POST /api/users/{id}/toggle-status
**Tested Status:** ❌ NOT TESTED

```bash
curl -X POST http://localhost:8000/api/users/1/toggle-status \
  -H "Authorization: Bearer TOKEN"
```

---

### 3.3 POST /api/work-orders/{id}/assign-team
**Tested Status:** ❌ NOT TESTED

```bash
curl -X POST http://localhost:8000/api/work-orders/1/assign-team \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"team_members": [1, 2, 3]}'
```

---

### 3.4 POST /api/invoice-types/{id}/toggle-status
**Tested Status:** ❌ NOT TESTED

---

### 3.5 POST /api/master-item-products/{id}/toggle-status
**Tested Status:** ❌ NOT TESTED

---

### 3.6 Print Endpoints
**Tested Status:** ❌ NOT TESTED (All 4)

```
❌ POST /api/print/invoices/batch
❌ GET  /api/print/invoices/batch?batch_id=...
❌ GET  /api/print/invoices/{id}/pdf
❌ GET  /api/print/receipts/{id}/pdf
```

---

### 3.7 Protection CRUD Endpoints
**Tested Status:** ❌ NOT TESTED (Some)

```
❌ POST   /api/protections
❌ PUT    /api/protections/{id}
❌ DELETE /api/protections/{id}
```

---

### 3.8 Cost Management
**Tested Status:** ❌ NOT TESTED (3 endpoints)

```
❌ POST   /api/posting/payments/{id}/costs
❌ PUT    /api/posting/payments/{id}/costs/{cost}
❌ DELETE /api/posting/payments/{id}/costs/{cost}
```

---

### 3.9 Search Endpoints
**Tested Status:** ⚠️ PARTIALLY

```
⚠️ GET /api/work-orders/search
⚠️ GET /api/stop-licenses/search
```

---

## 📋 COMPLETE TESTING CHECKLIST

### DELETE Operations (18 endpoints)
```
□ DELETE /api/users/{id}
□ DELETE /api/companies/{id}
□ DELETE /api/clients/{id}
□ DELETE /api/banks/{id}
□ DELETE /api/team-members/{id}
□ DELETE /api/products/{id}
□ DELETE /api/items/{id}
□ DELETE /api/invoices/{id}
□ DELETE /api/invoice-items/{id}
□ DELETE /api/payments/{id}
□ DELETE /api/debit-credit-notes/{id}
□ DELETE /api/work-orders/{id}
□ DELETE /api/installations/{id}
□ DELETE /api/licenses/{id}
□ DELETE /api/stop-licenses/{id}
□ DELETE /api/protections/{id}
□ DELETE /api/tax-series/{id}
□ DELETE /api/posting/payments/{id}/costs/{cost}
```

### PUT Operations (17 endpoints)
```
□ PUT /api/users/{id}
□ PUT /api/companies/{id}
□ PUT /api/clients/{id}
□ PUT /api/banks/{id}
□ PUT /api/team-members/{id}
□ PUT /api/products/{id}
□ PUT /api/items/{id}
□ PUT /api/invoices/{id}
□ PUT /api/invoice-items/{id}
□ PUT /api/payments/{id}
□ PUT /api/debit-credit-notes/{id}
□ PUT /api/work-orders/{id}
□ PUT /api/installations/{id}
□ PUT /api/licenses/{id}
□ PUT /api/stop-licenses/{id}
□ PUT /api/protections/{id}
□ PUT /api/posting/payments/{id}/costs/{cost}
```

### Custom Actions (12 endpoints)
```
□ POST /api/users/{id}/assign-role
□ POST /api/users/{id}/toggle-status
□ POST /api/invoice-types/{id}/toggle-status
□ POST /api/master-item-products/{id}/toggle-status
□ POST /api/work-orders/{id}/assign-team
□ POST /api/posting/payments/{id}/costs
□ PUT  /api/posting/payments/{id}/costs/{cost}
□ DELETE /api/posting/payments/{id}/costs/{cost}
□ POST /api/print/invoices/batch
□ GET  /api/print/invoices/batch?batch_id=...
□ GET  /api/print/invoices/{id}/pdf
□ GET  /api/print/receipts/{id}/pdf
```

### Search Endpoints (2)
```
□ GET /api/work-orders/search
□ GET /api/stop-licenses/search
```

---

## 🚀 HOW TO TEST & SAVE RESPONSES

### Step-by-Step in Postman:

1. **Find endpoint in collection**
   - Example: DELETE /api/banks/1

2. **Make sure you have valid ID**
   - Run GET first if needed
   - Use ID from response

3. **Send request**
   - Click Send button
   - Wait for response

4. **Save response example**
   - Right-click on response
   - Click "Save response as"
   - Enter name (e.g., "Success" or "Not Found")
   - Click Save

5. **Repeat for different scenarios**
   - Valid case → "Success"
   - Invalid case → "Validation Error" or "404"

6. **That's it!**
   - Now endpoint shows saved response
   - You've "tested" it

---

## 📊 CURRENT STATUS SUMMARY

```
TESTED (76): All GET & some POST
NOT TESTED (64):
  • ALL 18 DELETE operations
  • ALL 17 PUT operations  
  • 12 Custom action endpoints
  • 2 Search endpoints (partial)
  • 7 Other missing tests

TOTAL: 153 endpoints
TESTED: 76 (49%)
TO TEST: 77 (51%)
```

---

## ⏱️ ESTIMATED TIME

- DELETE (18): 2-3 hours
- PUT (17): 2-3 hours
- Custom Actions (12): 1.5-2 hours
- Search (2): 30 minutes

**Total: 6-8.5 hours** to reach 100% testing

---

## 📝 NOTES

- Save response = Official test record
- No save response = Not officially tested (even if you tried it)
- Postman structure already exists, just need responses
- Each endpoint should have 2-3 response examples (success + errors)

