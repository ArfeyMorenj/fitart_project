# 📋 PRODUCTION DEPLOYMENT - TESTING CHECKLIST (Printable)

**Project**: FitArt JPAS  
**Date**: ________________  
**Tested By**: ________________  
**Approved By**: ________________  

---

## ⏱️ PHASE 1: AUTHENTICATION & CORE SETUP (30 min)

### Step 1: Database Migration
```bash
php artisan migrate --force
```

**Result**: ☐ PASS | ☐ FAIL  
**Notes**: ___________________________________

### Step 2: Database Seeding
```bash
php artisan db:seed --force
```

**Result**: ☐ PASS | ☐ FAIL  
**Notes**: ___________________________________

### Step 3: Create Super Admin User

**Command**: Copy-paste dari TINKER_COMMANDS_PRODUCTION.txt  
**Result**: ☐ PASS | ☐ FAIL  
**Notes**: ___________________________________

### Step 4: Verify User Creation

```bash
php artisan tinker
User::count();
```

**Expected**: 6 users  
**Actual**: _______ users  
**Result**: ☐ PASS | ☐ FAIL  

---

## 👤 PHASE 2: LOGIN TESTING (15 min)

### Test 2.1: Login as Super Admin

**URL**: `POST http://localhost:8000/api/login`

**Request Body**:
```json
{
  "email": "superadmin@fitart.co.id",
  "password": "Admin@123"
}
```

**Expected Response**: Status 200 + Token  
**Actual Response**: Status _____ | Token: ☐ YES | ☐ NO  
**Result**: ☐ PASS | ☐ FAIL  

### Test 2.2: Login as Finance Admin

**Request Body**:
```json
{
  "email": "finance.admin@fitart.co.id",
  "password": "Admin@123"
}
```

**Expected Response**: Status 200 + Token  
**Actual Response**: Status _____ | Token: ☐ YES | ☐ NO  
**Result**: ☐ PASS | ☐ FAIL  

### Test 2.3: Login as AR Manager

**Request Body**:
```json
{
  "email": "ar.manager@fitart.co.id",
  "password": "Admin@123"
}
```

**Expected Response**: Status 200 + Token  
**Actual Response**: Status _____ | Token: ☐ YES | ☐ NO  
**Result**: ☐ PASS | ☐ FAIL  

### Test 2.4: Login as Sales Lead

**Request Body**:
```json
{
  "email": "sales.lead@fitart.co.id",
  "password": "Admin@123"
}
```

**Expected Response**: Status 200 + Token  
**Actual Response**: Status _____ | Token: ☐ YES | ☐ NO  
**Result**: ☐ PASS | ☐ FAIL  

### Test 2.5: Invalid Login (Negative Test)

**Request Body**:
```json
{
  "email": "superadmin@fitart.co.id",
  "password": "WrongPassword"
}
```

**Expected**: Status 401 (Unauthorized)  
**Actual**: Status _____  
**Result**: ☐ PASS | ☐ FAIL  

---

## 📊 PHASE 3: MASTER DATA VERIFICATION (30 min)

### Test 3.1: Bank List
```bash
GET /api/banks
Header: Authorization: Bearer {token}
```

**Expected**: 10+ banks  
**Actual Count**: _____ banks  
**Result**: ☐ PASS | ☐ FAIL  
**Sample Data**:
- BCA (code: 014) ☐
- Mandiri (code: 008) ☐
- BRI (code: 002) ☐

### Test 3.2: Chart of Accounts
```bash
GET /api/chart-of-accounts
```

**Expected**: 20+ accounts including 5033 & 1535  
**Actual Count**: _____ accounts  
**Result**: ☐ PASS | ☐ FAIL  
**Critical Accounts**:
- 5033 (Revenue) ☐
- 1535 (Piutang) ☐

### Test 3.3: Product Groups
```bash
GET /api/product-groups
```

**Expected**: 2+ groups  
**Actual Count**: _____ groups  
**Result**: ☐ PASS | ☐ FAIL  

### Test 3.4: Products
```bash
GET /api/products
```

**Expected**: 5+ products  
**Actual Count**: _____ products  
**Result**: ☐ PASS | ☐ FAIL  

### Test 3.5: Team Members
```bash
GET /api/team-members
```

**Expected**: 3+ members  
**Actual Count**: _____ members  
**Result**: ☐ PASS | ☐ FAIL  

### Test 3.6: Invoice Types
```bash
GET /api/invoice-types
```

**Expected**: 3+ types  
**Actual Count**: _____ types  
**Result**: ☐ PASS | ☐ FAIL  

---

## 💼 PHASE 4: BUSINESS LOGIC TESTING (60 min)

### Test 4.1: Create Invoice

**URL**: `POST /api/invoices`  
**Authorization**: Finance Admin Token

**Request Body**:
```json
{
  "invoice_number": "INV-TEST-001",
  "invoice_date": "2024-04-27",
  "due_date": "2024-05-27",
  "client_id": 1,
  "product_group_id": 1,
  "amount": 1000000,
  "tax": 100000,
  "total": 1100000
}
```

**Response Status**: _____  
**Invoice ID Created**: _____  
**Result**: ☐ PASS | ☐ FAIL  

### Test 4.2: Get Invoice Detail

**URL**: `GET /api/invoices/{invoice_id}`

**Response Status**: 200  
**Invoice Amount**: Rp 1.100.000  
**Result**: ☐ PASS | ☐ FAIL  

### Test 4.3: Create Payment

**URL**: `POST /api/payments`  
**Authorization**: Finance Admin Token

**Request Body**:
```json
{
  "invoice_id": 1,
  "payment_date": "2024-04-27",
  "amount": 1100000,
  "payment_method": "bank_transfer",
  "bank_id": 1
}
```

**Response Status**: _____  
**Payment ID**: _____  
**Invoice Status After**: paid / pending  
**Result**: ☐ PASS | ☐ FAIL  

### Test 4.4: Create Debit Note

**URL**: `POST /api/debit-notes`

**Request Body**:
```json
{
  "invoice_id": 1,
  "note_date": "2024-04-27",
  "reason": "Interest Charge",
  "amount": 50000
}
```

**Response Status**: _____  
**Result**: ☐ PASS | ☐ FAIL  

### Test 4.5: Create Credit Note

**URL**: `POST /api/credit-notes`

**Request Body**:
```json
{
  "invoice_id": 1,
  "note_date": "2024-04-27",
  "reason": "Price Adjustment",
  "amount": 50000
}
```

**Response Status**: _____  
**Result**: ☐ PASS | ☐ FAIL  

### Test 4.6: Post to General Ledger

**URL**: `POST /api/general-ledger/post`

**Request Body**:
```json
{
  "invoice_id": 1,
  "period": "2024-04"
}
```

**Response Status**: _____  
**GL Entry Created**: ☐ YES | ☐ NO  
**Result**: ☐ PASS | ☐ FAIL  

### Test 4.7: Generate Report

**URL**: `GET /api/reports/ar-aging`

**Response Status**: 200  
**Report Data**: ☐ Contains data | ☐ Empty  
**Result**: ☐ PASS | ☐ FAIL  

---

## 🔒 PHASE 5: SECURITY TESTING (15 min)

### Test 5.1: Unauthorized Access (No Token)
```bash
GET /api/invoices
# (tanpa header Authorization)
```

**Expected**: 401 Unauthorized  
**Actual Status**: _____  
**Result**: ☐ PASS | ☐ FAIL  

### Test 5.2: Invalid Token
```bash
GET /api/invoices
Header: Authorization: Bearer invalid-token-xyz
```

**Expected**: 401 Unauthorized  
**Actual Status**: _____  
**Result**: ☐ PASS | ☐ FAIL  

### Test 5.3: Role-Based Access (AR Manager cannot Delete User)

**URL**: `DELETE /api/users/1`  
**Authorization**: AR Manager Token

**Expected**: 403 Forbidden  
**Actual Status**: _____  
**Result**: ☐ PASS | ☐ FAIL  

### Test 5.4: Valid Token Accepted

**URL**: `GET /api/invoices`  
**Authorization**: Finance Admin Token

**Expected**: 200 OK + Data  
**Actual Status**: _____  
**Result**: ☐ PASS | ☐ FAIL  

---

## ⚡ PHASE 6: API ENDPOINT COVERAGE (120 min)

Gunakan Postman Collection: `FitArt_JPAS_API_Complete.postman_collection.json`

### GET Endpoints (6 total)

| # | Endpoint | Status | Notes |
|---|----------|--------|-------|
| 1 | GET /api/banks | ☐ 200 | |
| 2 | GET /api/chart-of-accounts | ☐ 200 | |
| 3 | GET /api/products | ☐ 200 | |
| 4 | GET /api/invoices | ☐ 200 | |
| 5 | GET /api/payments | ☐ 200 | |
| 6 | GET /api/reports | ☐ 200 | |

### POST Endpoints (15 total)

| # | Endpoint | Status | Notes |
|---|----------|--------|-------|
| 1 | POST /api/product-groups | ☐ 201 | |
| 2 | POST /api/products | ☐ 201 | |
| 3 | POST /api/team-members | ☐ 201 | |
| 4 | POST /api/invoices | ☐ 201 | |
| 5 | POST /api/invoice-items | ☐ 201 | |
| 6 | POST /api/payments | ☐ 201 | |
| 7 | POST /api/debit-notes | ☐ 201 | |
| 8 | POST /api/credit-notes | ☐ 201 | |
| 9 | POST /api/general-ledger/post | ☐ 201 | |
| 10 | POST /api/clients | ☐ 201 | |
| 11-15 | [Others...] | ☐ | |

### PUT Endpoints (15 total)

| # | Endpoint | Status | Notes |
|---|----------|--------|-------|
| 1 | PUT /api/products/1 | ☐ 200 | |
| 2 | PUT /api/invoices/1 | ☐ 200 | |
| 3-15 | [Others...] | ☐ | |

### DELETE Endpoints (22 total)

| # | Endpoint | Status | Notes |
|---|----------|--------|-------|
| 1 | DELETE /api/products/1 | ☐ 200/204 | |
| 2 | DELETE /api/invoices/1 | ☐ 200/204 | |
| 3-22 | [Others...] | ☐ | |

---

## 📈 OVERALL SUMMARY

### Test Results:

| Phase | Tests | Passed | Failed | % Success |
|-------|-------|--------|--------|-----------|
| 1. Auth & Setup | 4 | ___ | ___ | __% |
| 2. Login Testing | 5 | ___ | ___ | __% |
| 3. Master Data | 6 | ___ | ___ | __% |
| 4. Business Logic | 7 | ___ | ___ | __% |
| 5. Security | 4 | ___ | ___ | __% |
| 6. API Endpoints | 58 | ___ | ___ | __% |
| **TOTAL** | **84** | ___ | ___ | **__%** |

### Final Decision:

```
☐ APPROVED FOR PRODUCTION (All tests PASS, 100% success)
☐ CONDITIONAL APPROVAL (Minor issues noted below)
☐ NOT APPROVED (Critical issues found, see notes)
```

### Issues Found:

```
1. ________________________________
2. ________________________________
3. ________________________________
```

### Action Items Before Go-Live:

```
1. ☐ ________________________________
2. ☐ ________________________________
3. ☐ ________________________________
```

---

**Tested By**: ____________________  
**Date**: ____________________  
**Signature**: ____________________  

**Approved By**: ____________________  
**Date**: ____________________  
**Signature**: ____________________  

---

**Document Version**: 1.0  
**Last Updated**: April 27, 2026
