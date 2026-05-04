# AUTO-TEST GENERATION - READY-TO-USE TEST CASES

## Format for Postman Integration

Each test case below is ready to be added to the Postman collection. Copy the request body and response example directly into new test requests.

---

## CRITICAL TESTS - FINANCIAL MODULE

### Test 1: Create Invoice - Valid Scenario

**Endpoint:** `POST {{base_url}}/api/invoices`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "invoice_number": "INV-2026-0001",
  "invoice_type_id": 1,
  "client_id": 1,
  "company_id": 1,
  "invoice_date": "2026-04-14",
  "due_date": "2026-05-14",
  "currency_code": "USD",
  "items": [
    {
      "item_id": 1,
      "description": "Professional Services - Q1",
      "quantity": 1,
      "unit_price": 100000,
      "tax_rate": 0.1
    },
    {
      "item_id": 2,
      "description": "Support Services",
      "quantity": 2,
      "unit_price": 50000,
      "tax_rate": 0.1
    }
  ],
  "notes": "Quarterly services invoice",
  "payment_terms": "Net 30"
}
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "message": "Invoice created successfully",
  "data": {
    "id": 1,
    "invoice_number": "INV-2026-0001",
    "client_id": 1,
    "status": "draft",
    "total_amount": 330000,
    "subtotal": 300000,
    "tax_amount": 30000,
    "items_count": 2,
    "invoice_date": "2026-04-14",
    "due_date": "2026-05-14",
    "items": [
      {
        "id": 1,
        "item_id": 1,
        "description": "Professional Services - Q1",
        "quantity": 1,
        "unit_price": 100000,
        "tax_rate": 0.1,
        "line_total": 110000
      },
      {
        "id": 2,
        "item_id": 2,
        "description": "Support Services",
        "quantity": 2,
        "unit_price": 50000,
        "tax_rate": 0.1,
        "line_total": 220000
      }
    ],
    "created_at": "2026-04-14T10:30:00Z",
    "updated_at": "2026-04-14T10:30:00Z"
  }
}
```

---

### Test 2: Create Invoice - Validation Error

**Endpoint:** `POST {{base_url}}/api/invoices`

**Request Body (INVALID):**
```json
{
  "invoice_number": "",
  "client_id": 9999,
  "items": []
}
```

**Expected Response (422 Unprocessable Entity):**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "invoice_number": ["The invoice number field is required."],
    "client_id": ["The selected client id is invalid."],
    "items": ["You must provide at least one invoice item."],
    "invoice_type_id": ["The invoice type id field is required."]
  }
}
```

---

### Test 3: Revenue Posting - GL Entry Creation

**Endpoint:** `POST {{base_url}}/api/posting/omzet`

**Request Body:**
```json
{
  "invoice_id": 1,
  "period_id": 1,
  "gl_revenue_account": "4110",
  "gl_ar_account": "1120",
  "amount": 300000,
  "tax_gl_account": "2210"
}
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "message": "Revenue posted successfully",
  "data": {
    "posting_id": 1,
    "invoice_id": 1,
    "journal_entries": [
      {
        "id": 1,
        "entry_date": "2026-04-14",
        "period_id": 1,
        "account_number": "1120",
        "account_name": "Accounts Receivable",
        "debit": 330000,
        "credit": 0,
        "description": "AR from Invoice INV-2026-0001",
        "invoice_reference": "INV-2026-0001"
      },
      {
        "id": 2,
        "entry_date": "2026-04-14",
        "period_id": 1,
        "account_number": "4110",
        "account_name": "Service Revenue",
        "debit": 0,
        "credit": 300000,
        "description": "Revenue - Invoice INV-2026-0001",
        "invoice_reference": "INV-2026-0001"
      },
      {
        "id": 3,
        "entry_date": "2026-04-14",
        "period_id": 1,
        "account_number": "2210",
        "account_name": "Sales Tax Payable",
        "debit": 0,
        "credit": 30000,
        "description": "Sales Tax - Invoice INV-2026-0001",
        "invoice_reference": "INV-2026-0001"
      }
    ],
    "status": "posted",
    "created_at": "2026-04-14T10:35:00Z"
  }
}
```

**Validation Test - Invalid GL Account:**

**Request Body (INVALID):**
```json
{
  "invoice_id": 1,
  "period_id": 1,
  "gl_revenue_account": "9999",
  "gl_ar_account": "1120",
  "amount": 300000
}
```

**Expected Response (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "gl_revenue_account": ["GL account 9999 does not exist"]
  }
}
```

---

### Test 4: Record Payment

**Endpoint:** `POST {{base_url}}/api/payments`

**Request Body:**
```json
{
  "invoice_id": 1,
  "client_id": 1,
  "payment_date": "2026-04-15",
  "amount": 100000,
  "payment_method": "bank_transfer",
  "reference_number": "BT-2026-001",
  "bank_account_id": 1,
  "notes": "Partial payment received"
}
```

**Expected Response (201 Created):**
```json
{
  "success": true,
  "message": "Payment recorded successfully",
  "data": {
    "id": 1,
    "invoice_id": 1,
    "client_id": 1,
    "amount": 100000,
    "amount_applied": 100000,
    "remaining_balance": 230000,
    "payment_date": "2026-04-15",
    "payment_method": "bank_transfer",
    "reference_number": "BT-2026-001",
    "status": "applied",
    "created_at": "2026-04-15T11:00:00Z"
  }
}
```

---

### Test 5: Batch Payment Posting

**Endpoint:** `POST {{base_url}}/api/posting/payments`

**Request Body:**
```json
{
  "payments": [
    {
      "payment_id": 1,
      "gl_bank_account": "1010",
      "gl_ar_account": "1120",
      "amount": 100000
    }
  ],
  "posting_date": "2026-04-15",
  "period_id": 1
}
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "message": "Payments posted successfully",
  "data": {
    "posting_batch_id": 1,
    "posted_count": 1,
    "total_amount": 100000,
    "journal_entries": [
      {
        "id": 4,
        "account_number": "1010",
        "account_name": "Bank Account",
        "debit": 100000,
        "credit": 0,
        "description": "Payment received - Invoice INV-2026-0001"
      },
      {
        "id": 5,
        "account_number": "1120",
        "account_name": "Accounts Receivable",
        "debit": 0,
        "credit": 100000,
        "description": "AR Applied - Invoice INV-2026-0001"
      }
    ],
    "status": "posted",
    "created_at": "2026-04-15T11:05:00Z"
  }
}
```

---

## HIGH PRIORITY TESTS - REPORTING

### Test 6: Get Receivable Detail Report

**Endpoint:** `GET {{base_url}}/api/reports/receivable/detail?client_id=1&as_of_date=2026-04-15`

**Headers:**
```
Authorization: Bearer {{token}}
Accept: application/json
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "report_title": "Accounts Receivable Detail Report",
    "as_of_date": "2026-04-15",
    "client": {
      "id": 1,
      "name": "ACME Corporation",
      "code": "CLI-001",
      "credit_limit": 500000
    },
    "invoices": [
      {
        "invoice_number": "INV-2026-0001",
        "invoice_date": "2026-04-14",
        "due_date": "2026-05-14",
        "original_amount": 330000,
        "amount_paid": 100000,
        "balance_due": 230000,
        "days_outstanding": 1,
        "aging_bucket": "current",
        "payment_status": "partially_paid",
        "items": [
          {
            "item_id": 1,
            "description": "Professional Services - Q1",
            "quantity": 1,
            "unit_price": 100000,
            "line_total": 110000
          }
        ]
      }
    ],
    "summary": {
      "total_invoiced": 330000,
      "total_paid": 100000,
      "total_balance": 230000,
      "current_amount": 230000,
      "overdue_amount": 0,
      "invoice_count": 1,
      "avg_days_outstanding": 1
    }
  }
}
```

---

### Test 7: Get Tax Summary Report

**Endpoint:** `GET {{base_url}}/api/reports/tax/summary?start_date=2026-01-01&end_date=2026-03-31`

**Expected Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "period": "2026-01-01 to 2026-03-31",
    "report_type": "VAT Summary",
    "currency": "USD",
    "summary": {
      "total_taxable_sales": 1500000,
      "total_non_taxable_sales": 200000,
      "total_sales": 1700000,
      "tax_rate": 0.1,
      "total_tax_calculated": 150000,
      "tax_collected": 145000,
      "tax_adjustments": -5000,
      "tax_payable": 140000
    },
    "by_month": [
      {
        "month": "2026-01",
        "taxable_sales": 500000,
        "tax_amount": 50000
      },
      {
        "month": "2026-02",
        "taxable_sales": 500000,
        "tax_amount": 50000
      },
      {
        "month": "2026-03",
        "taxable_sales": 500000,
        "tax_amount": 50000
      }
    ]
  }
}
```

---

### Test 8: Generate Recurring Invoices

**Endpoint:** `POST {{base_url}}/api/recurring/invoices/generate`

**Request Body:**
```json
{
  "period_id": 1,
  "generate_date": "2026-04-30",
  "dry_run": false
}
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "message": "Recurring invoices generated successfully",
  "data": {
    "invoices_generated": 3,
    "invoices": [
      {
        "id": 2,
        "invoice_number": "INV-2026-0002",
        "client_id": 2,
        "amount": 50000,
        "invoice_date": "2026-04-30",
        "recurr_frequency": "monthly",
        "source_recurring_config_id": 1
      },
      {
        "id": 3,
        "invoice_number": "INV-2026-0003",
        "client_id": 3,
        "amount": 75000,
        "invoice_date": "2026-04-30",
        "recurr_frequency": "monthly",
        "source_recurring_config_id": 2
      },
      {
        "id": 4,
        "invoice_number": "INV-2026-0004",
        "client_id": 1,
        "amount": 100000,
        "invoice_date": "2026-04-30",
        "recurr_frequency": "monthly",
        "source_recurring_config_id": 3
      }
    ],
    "generated_at": "2026-04-30T23:59:59Z"
  }
}
```

**Validation Test - No Recurring Configs:**

**Request:**
```json
{
  "period_id": 99,
  "generate_date": "2026-05-01",
  "dry_run": false
}
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "message": "Recurring invoice generation completed",
  "data": {
    "invoices_generated": 0,
    "message": "No active recurring invoice configurations for this period"
  }
}
```

---

## EDGE CASE TESTS

### Test 9: Invoice with Decimal Quantities

**Endpoint:** `POST {{base_url}}/api/invoices`

**Request Body:**
```json
{
  "invoice_number": "INV-2026-0005",
  "invoice_type_id": 1,
  "client_id": 1,
  "invoice_date": "2026-04-14",
  "due_date": "2026-05-14",
  "items": [
    {
      "item_id": 1,
      "description": "Materials - per unit",
      "quantity": 2.5,
      "unit_price": 10000,
      "tax_rate": 0.1
    }
  ]
}
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 5,
    "invoice_number": "INV-2026-0005",
    "items": [
      {
        "quantity": 2.5,
        "unit_price": 10000,
        "line_total": 27500
      }
    ],
    "subtotal": 25000,
    "tax_amount": 2500,
    "total_amount": 27500
  }
}
```

---

### Test 10: Overpayment Handling

**Endpoint:** `POST {{base_url}}/api/payments`

**Request Body:**
```json
{
  "invoice_id": 1,
  "client_id": 1,
  "payment_date": "2026-04-16",
  "amount": 250000,
  "payment_method": "bank_transfer",
  "reference_number": "BT-2026-002"
}
```

**Expected Response (200/422 Depending on Policy):**

Option A - Auto-Handle Overpayment:
```json
{
  "success": true,
  "data": {
    "id": 2,
    "amount_applied_to_invoice": 230000,
    "overpayment_amount": 20000,
    "overpayment_status": "unapplied_credit",
    "invoice_balance": 0,
    "message": "Payment applied. Overpayment of 20000 held as client credit."
  }
}
```

Option B - Reject Overpayment:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "amount": ["Payment amount 250000 exceeds invoice balance 230000. Maximum allowed: 230000"]
  }
}
```

---

## IMPLEMENTATION GUIDE

### Steps to Add Tests to Postman:

1. **Import Tests into Postman Collection:**
   - Create folder "Forensic Analysis Tests" in collection
   - Copy each test case above as new request
   - Name requests according to test number

2. **Set Up Test Environment:**
   - Create Postman environment "Test Data"
   - Add variables: `test_client_id=1`, `test_invoice_date=2026-04-14`
   - Set `test_mode=true` for test execution

3. **Add Pre-request Scripts:**
   ```javascript
   // Generate unique invoice number
   const timestamp = new Date().getTime();
   pm.environment.set("unique_prefix", "TEST_" + timestamp);
   ```

4. **Add Tests Scripts:**
   ```javascript
   pm.test("Response status is 200", function() {
       pm.response.to.have.status(200);
   });
   pm.test("Response contains success flag", function() {
       var jsonData = pm.response.json();
       pm.expect(jsonData.success).to.equal(true);
   });
   ```

5. **Run Tests:**
   - Use Newman CLI: `newman run collection.json -e environment.json`
   - Or run in Postman UI: Collection Runner

---

## Expected Outcomes After Implementation

| Metric | Before | After Target | Effort |
|--------|--------|--------------|--------|
| Test Coverage | 49.7% | 85% | 60 hours |
| Financial Ops Coverage | 25% | 100% | 30 hours |
| Error Scenarios | 15% | 80% | 20 hours |
| Edge Cases | 10% | 70% | 15 hours |

**Total Implementation: ~125 hours (3 weeks)**

---

*End of Auto-Test Generation Guide*
