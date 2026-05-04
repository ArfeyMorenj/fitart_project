# 🎯 TESTING ACTION PLAN - Complete Roadmap
## Step-by-Step Execution to Reach 100% Testing Coverage

**Current Status:** 76 tested / 153 total (49.7%)  
**Target:** 153 tested / 153 total (100%)  
**Gap:** 77 endpoints remaining  
**Estimated Effort:** 6-8.5 hours

---

## 📊 BREAKDOWN SUMMARY

| Category | Total | Currently Tested | Status | Priority | Est. Time |
|----------|-------|------------------|--------|----------|-----------|
| DELETE endpoints | 18 | 0 | ❌ 0% | 🔴 CRITICAL | 2-3 hrs |
| PUT endpoints | 17 | 0 | ❌ 0% | 🔴 CRITICAL | 2-3 hrs |
| Custom Actions | 12 | 3 | ⚠️ 25% | 🟠 HIGH | 1-1.5 hrs |
| Search Endpoints | 2 | 0 | ❌ 0% | 🟡 MEDIUM | 30 min |
| POST (missing) | 2 | 0 | ❌ 0% | 🟡 MEDIUM | 30 min |
| Other missing | 21 | 0 | ❌ 0% | 🟡 MEDIUM | 1 hr |
| **TOTAL** | **72** | **3** | **4%** | — | **5-7 hrs** |

---

## 🔴 PHASE 1: CRITICAL - DELETE OPERATIONS (2-3 hours)

**Why First:** Data integrity is crucial. Need to verify deletion works properly.

### DELETE Batch 1: User Management (1 endpoint)
```
Test Order: 1
Endpoint: DELETE /api/users/{id}
Steps:
  1. GET /api/users → Pick a test user (NOT current user, NOT super_admin)
  2. DELETE /api/users/{picked_id}
  3. Verify 200 response
  4. Save response as "Success - User Deleted"
  5. GET /api/users/{picked_id} → Should be 404
  6. Save that 404 response
Scenarios to test:
  ✅ Valid user (should delete)
  ✅ Non-existent user (404)
  ❌ Current user (should fail)
  ❌ Super admin (if protected)
Time: 10 min
```

### DELETE Batch 2: Master Data (13 endpoints)
```
Test Order: 2-14
Endpoints:
  DELETE /api/companies/{id}
  DELETE /api/clients/{id}
  DELETE /api/banks/{id}
  DELETE /api/team-members/{id}
  DELETE /api/products/{id}
  DELETE /api/items/{id}
  DELETE /api/installations/{id}
  DELETE /api/licenses/{id}
  DELETE /api/stop-licenses/{id}
  DELETE /api/protections/{id}
  DELETE /api/tax-series/{id}
  DELETE /api/debit-credit-notes/{id}
  DELETE /api/work-orders/{id}

Steps for EACH:
  1. GET /api/{resource} → Note an ID
  2. DELETE /api/{resource}/{id}
  3. Verify response (200 or 422 if protected)
  4. Save response
  5. Verify with GET (should be 404 or error)

Pattern: All same format
Time: ~1.5-2 hours (5-8 min each)
Batch Order: Do in this order:
  1. products (no dependencies)
  2. items
  3. companies
  4. clients
  5. banks
  6. team-members
  7. protections
  8. tax-series
  9. work-orders
  10. debit-credit-notes
  11. licenses
  12. stop-licenses
  13. installations
```

### DELETE Batch 3: Complex Operations (4 endpoints)
```
Test Order: 15-18
Endpoints:
  DELETE /api/invoices/{id}
  DELETE /api/invoice-items/{id}
  DELETE /api/payments/{id}
  DELETE /api/posting/payments/{id}/costs/{cost}

Why last in Phase 1: These may have dependencies

DELETE /api/invoices/{id}:
  1. GET /api/invoices → Find non-posted invoice
  2. DELETE /api/invoices/{id}
  3. Should succeed if NOT posted
  4. Try deleting posted invoice → Should fail 422
  5. Save both responses

DELETE /api/invoice-items/{id}:
  1. GET /api/invoices/1/items
  2. DELETE /api/invoice-items/{id}
  3. Verify response
  
DELETE /api/payments/{id}:
  1. Standard delete flow
  
DELETE /api/posting/payments/{id}/costs/{cost}:
  1. Requires cost ID first
  2. Standard delete flow

Time: ~30-40 min
```

---

## 🔴 PHASE 2: CRITICAL - PUT/UPDATE OPERATIONS (2-3 hours)

**Why Second:** After verifying delete, test update functionality.

### PUT Batch 1: User Management (1 endpoint)
```
Test Order: 1
Endpoint: PUT /api/users/{id}
Steps:
  1. GET /api/users/1 → See current data
  2. PUT /api/users/1 with updated fields
  3. Save "Success - Updated" response
  4. Try invalid email (duplicate) → Save error
  5. Try non-existent user → Save 404

Sample body:
{
  "name": "Updated Name",
  "email": "newemail@domain.com"
}

Time: 10 min
```

### PUT Batch 2: Master Data (16 endpoints)
```
Test Order: 2-17
Endpoints:
  PUT /api/companies/{id}
  PUT /api/clients/{id}
  PUT /api/banks/{id}
  PUT /api/team-members/{id}
  PUT /api/products/{id}
  PUT /api/items/{id}
  PUT /api/installations/{id}
  PUT /api/licenses/{id}
  PUT /api/stop-licenses/{id}
  PUT /api/protections/{id}
  PUT /api/invoices/{id}
  PUT /api/invoice-items/{id}
  PUT /api/payments/{id}
  PUT /api/debit-credit-notes/{id}
  PUT /api/work-orders/{id}
  PUT /api/posting/payments/{id}/costs/{cost}

Generic steps for EACH:
  1. GET /api/{resource}/{id} → Copy data
  2. Change 1-2 fields
  3. PUT /api/{resource}/{id}
  4. Verify 200 response
  5. Save as "Success - Updated"
  6. Try invalid data → Save error 422
  7. Repeat for different field

Time: 2-2.5 hours (6-8 min each)
```

---

## 🟠 PHASE 3: HIGH - CUSTOM ACTIONS & SPECIAL ENDPOINTS (1.5-2 hours)

### Custom Actions Group 1: Toggle/Assignment (5 endpoints)
```
Test Order: 1-5
Endpoints:
  POST /api/users/{id}/assign-role
  POST /api/users/{id}/toggle-status
  POST /api/invoice-types/{id}/toggle-status
  POST /api/master-item-products/{id}/toggle-status
  POST /api/work-orders/{id}/assign-team

Steps for assign-role:
  1. GET /api/users/1
  2. POST /api/users/1/assign-role
     Body: {"role": "finance_admin"}
  3. Verify response, save
  4. Set back to original role

Steps for toggle-status:
  1. GET /api/{resource} to see current status
  2. POST /api/{resource}/{id}/toggle-status
  3. Verify status changed
  4. POST again to toggle back

Time: ~40 min
```

### Custom Actions Group 2: Cost Management (3 endpoints)
```
Test Order: 6-8
Endpoints:
  POST   /api/posting/payments/{id}/costs
  PUT    /api/posting/payments/{id}/costs/{cost}
  DELETE /api/posting/payments/{id}/costs/{cost}

Note: May require setup
Time: ~20 min
```

### Print Endpoints (4 endpoints)
```
Test Order: 9-12
Endpoints:
  POST /api/print/invoices/batch
  GET  /api/print/invoices/batch?batch_id=...
  GET  /api/print/invoices/{id}/pdf
  GET  /api/print/receipts/{id}/pdf

Note: PDF endpoints may just return file, still counts as tested
Time: ~20 min
```

---

## 🟡 PHASE 4: MEDIUM - SEARCH & REMAINING (1 hour)

### Search Endpoints (2 endpoints)
```
Test Order: 1-2
Endpoints:
  GET /api/work-orders/search
  GET /api/stop-licenses/search

Steps:
  1. GET /api/work-orders/search?q=test
  2. Verify results format
  3. Save response
  
Time: 15 min
```

### Remaining Missing Endpoints (check routes/api.php for any we missed)
```
Test any we identified but not yet tested
Time: 45 min
```

---

## 📋 QUICK REFERENCE - Test in This Exact Order

### DAY 1 (Priority 1 & 2)

**Hour 1-2: DELETE Operations**
```
1. DELETE /api/users/{id}
2. DELETE /api/companies/{id}
3. DELETE /api/clients/{id}
4. DELETE /api/banks/{id}
5. DELETE /api/team-members/{id}
6. DELETE /api/products/{id}
```

**Hour 2-3: DELETE Continued**
```
7. DELETE /api/items/{id}
8. DELETE /api/installations/{id}
9. DELETE /api/licenses/{id}
10. DELETE /api/stop-licenses/{id}
11. DELETE /api/protections/{id}
12. DELETE /api/tax-series/{id}
13. DELETE /api/debit-credit-notes/{id}
14. DELETE /api/work-orders/{id}
15. DELETE /api/invoices/{id}
16. DELETE /api/invoice-items/{id}
17. DELETE /api/payments/{id}
18. DELETE /api/posting/payments/{id}/costs/{cost}
```

**Hour 4-5: PUT Operations**
```
1. PUT /api/users/{id}
2. PUT /api/companies/{id}
3. PUT /api/clients/{id}
4. PUT /api/banks/{id}
5. PUT /api/team-members/{id}
6. PUT /api/products/{id}
7. PUT /api/items/{id}
8. PUT /api/installations/{id}
```

**Hour 6: PUT Continued + Custom Actions**
```
9. PUT /api/licenses/{id}
10. PUT /api/stop-licenses/{id}
11. PUT /api/protections/{id}
12. PUT /api/invoices/{id}
13. PUT /api/invoice-items/{id}
14. PUT /api/payments/{id}
15. PUT /api/debit-credit-notes/{id}
16. PUT /api/work-orders/{id}
17. PUT /api/posting/payments/{id}/costs/{cost}

Start: POST /api/users/{id}/assign-role
```

### DAY 2 (Priority 3 & 4)

**Hour 1: Custom Actions & Special**
```
1. POST /api/users/{id}/toggle-status
2. POST /api/invoice-types/{id}/toggle-status
3. POST /api/master-item-products/{id}/toggle-status
4. POST /api/work-orders/{id}/assign-team
5. POST /api/posting/payments/{id}/costs
6. PUT  /api/posting/payments/{id}/costs/{cost}
7. DELETE /api/posting/payments/{id}/costs/{cost}
```

**Hour 2: Print + Search**
```
8. POST /api/print/invoices/batch
9. GET  /api/print/invoices/batch?batch_id=...
10. GET /api/print/invoices/{id}/pdf
11. GET /api/print/receipts/{id}/pdf
12. GET /api/work-orders/search
13. GET /api/stop-licenses/search
```

---

## ✅ SUCCESS CRITERIA

- [ ] All 18 DELETE operations tested and responses saved
- [ ] All 17 PUT operations tested and responses saved
- [ ] All 12 custom action operations tested
- [ ] All 2 search operations tested
- [ ] All 4 print operations tested
- [ ] Postman collection updated with all responses
- [ ] Updated testing guide shows 100% coverage
- [ ] No red/warning indicators in Postman

---

## 🔧 TIPS FOR TESTING

### How to Work Efficiently:
1. **Keep GET responses ready** - Open GET for resource in another tab
2. **Reuse IDs** - Use same ID across tests unless noted
3. **Document errors** - If you get 422, 403, or error, save that response too
4. **Double-check before delete** - Make sure you're not deleting real production data
5. **Save every response** - Right click → Save response

### Common Issues:
- **404 on DELETE after successful delete** → This is EXPECTED, save it
- **422 validation error** → Normal for invalid data, save the error response
- **403 Forbidden** → Expected for protected resources, save it
- **500 Server Error** → This is a BUG, report it

### Using IDs Safely:
- Always use test data from GET response
- Or create new record just for testing
- Don't use ID 1 if it's critical data
- For destructive tests, use highest ID numbers (less likely to be important)

---

## 📊 TRACKING PROGRESS

After each phase, count saved responses:

```
DELETE completed: ___ / 18 responses saved
PUT completed: ___ / 17 responses saved
Custom actions: ___ / 12 responses saved
Search: ___ / 2 responses saved
Print: ___ / 4 responses saved

Total: ___ / 77 responses saved
New coverage: (76 + ___) / 153 = ____%
```

---

## 🎯 FINAL CHECKLIST

Before declaring 100% complete:

- [ ] UPDATED_TESTING_GUIDE_v2.md shows all endpoints tested
- [ ] Postman collection has responses for all endpoints
- [ ] No endpoint shows "untested" status
- [ ] All 77 new tests documented
- [ ] fitart_api_testing_guide_final.md updated with new findings
- [ ] README updated with "100% endpoint coverage achieved"

---

## 📞 SUPPORT

If you get stuck on:
- **Database errors** → Check if data exists first with GET
- **Permission errors (403)** → Verify you're logged in as correct role
- **Validation errors (422)** → Review body structure in existing POST tests
- **Timeout errors** → Give server more time, try again
- **JSON parse errors** → Check Content-Type header is application/json

---

**Ready to start? Begin with Phase 1, Test #1: DELETE /api/users/{id}**

