# 📋 DATA TESTING REFERENCE & FORM TEMPLATE

## A. TESTING DATA QUICK REFERENCE

### 1️⃣ Team Members Data

**Existing Data in Database**:
| ID | Code | Name | Position | Status | Active | Note |
|----|------|------|----------|--------|--------|------|
| 1 | 01 | IMAM S ARIFIN | MANAGER AREA | STATUS1 | ✅ | Author untuk product |
| 4 | 04 | ADIT | PROGRAMMER | STATUS1 | ✅ | - |
| 5 | 05 | FITRI AMALIA | ADM.KEUANGAN | STATUS1 | ✅ | - |
| ? | ? | ? | ? | ? | ? | 4th member (check DB) |

**Data Baru untuk Testing**:
```
Code: 06
Name: AGUS HERMAWAN
Position: MARKETING MANAGER
Status: STATUS1
Active: Yes
```

---

### 2️⃣ Product Groups Data

**Existing Data**:
| ID | Code | Name | Acc Omzet | CDF Piutang | Active | Products |
|----|------|------|-----------|------------|--------|----------|
| 1 | LSA | LISENSI APLIKASI SIGMA | 5033 | 1535 | ✅ | IKLAN KOLOM |
| 3 | LSATEST | LISENSI TEST | 5033 | 1535 | ✅ | - |
| 4 | GPA | REno | null | null | ✅ | - |

**Data Baru untuk Testing**:
```
Code: SERVICE
Name: SERVICE DAN MAINTENANCE
Acc Omzet: 5033
CDF Piutang: 1535
Active: Yes
```

---

### 3️⃣ Products Data

**Existing Data**:
| ID | Code | Name | Specification | Group | Author | Year | Active |
|----|------|------|---------------|-------|--------|------|--------|
| 2 | 04 | IKLAN KOLOM (SIGMA KOLOM) | ADS MANAGEMENT | LSA (1) | IMANG INDAH AYUNINGRUM | 2006 | ✅ |
| 4 | 026724 | Arfey Moreno Jazzua | null | null | - | - | ✅ |

**Data Baru untuk Testing**:
```
Code: 05
Name: INVOICE MANAGEMENT SYSTEM
Specification: BILLING SYSTEM
Description: Sistem manajemen invoice terintegrasi
Author Code: 01
Author Name: IMAM S ARIFIN
Compiler: LARAVEL 10
Year: 2026
Product Group: LSA (ID: 1)
Active: Yes
```

---

### 4️⃣ Chart of Accounts Data

**Common Codes yang digunakan**:
| Code | Expected Name | Type | Used For |
|------|---------------|------|----------|
| 5033 | REVENUE/OMZET | Revenue | Acc Omzet (Product Group) |
| 1535 | ACCOUNT RECEIVABLE/PIUTANG | Asset | CDF Piutang (Product Group) |
| ... | ... | ... | ... |

**Action**: Check di Database untuk list lengkap dengan `ChartOfAccount::all();`

---

### 5️⃣ Invoice Data

**Data Baru untuk Testing**:
```
Invoice Number: INV-2026-001
Date: 2026-04-25 (atau hari ini)
Customer Name: PT INDO JAYA MAKMUR
Amount: (auto-calculated) = Rp 9.000.000
Status: DRAFT → POSTED
Active: Yes
```

**With 2 Items**:
```
Item 1:
  - Product: IKLAN KOLOM (SIGMA KOLOM) (ID: 2)
  - Quantity: 5
  - Unit Price: 1.000.000
  - Amount: 5.000.000

Item 2:
  - Product: INVOICE MANAGEMENT SYSTEM (ID: 5)
  - Quantity: 2
  - Unit Price: 2.000.000
  - Amount: 4.000.000

Total: 9.000.000
```

---

### 6️⃣ Journal Entry Data

**Data Baru untuk Testing**:
```
Entry Number: JE-2026-001
Date: 2026-04-25 (atau hari ini)
Description: Posting invoice INV-2026-001
Status: DRAFT → POSTED

Details:
  Debit Entry:
    - Account: 1535 (CDF Piutang/AR)
    - Description: Piutang dari invoice
    - Amount: 9.000.000
  
  Credit Entry:
    - Account: 5033 (Revenue/Omzet)
    - Description: Pendapatan dari penjualan
    - Amount: 9.000.000
```

---

## B. FORM TEMPLATES (untuk copy-paste di browser)

### Form 1: Create Team Member
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Code: 06

Name: AGUS HERMAWAN

Position: MARKETING MANAGER

Status: STATUS1

Is Active: [✓] (Checked)

Button: [SAVE]
```

---

### Form 2: Create Product Group
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Code: SERVICE

Name: SERVICE DAN MAINTENANCE

Acc Omzet (Account Revenue): 5033
(Dropdown - select from Chart of Accounts)

CDF Piutang (Account Receivable): 1535
(Dropdown - select from Chart of Accounts)

Is Active: [✓] (Checked)

Button: [SAVE]
```

---

### Form 3: Create Product
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Code: 05

Name: INVOICE MANAGEMENT SYSTEM

Specification: BILLING SYSTEM

Description: Sistem manajemen invoice terintegrasi

Author Code: 01

Author Name: IMAM S ARIFIN

Compiler: LARAVEL 10

Year: 2026

Product Group: LSA
(Dropdown - select from Product Groups)

Is Active: [✓] (Checked)

Button: [SAVE]
```

---

### Form 4: Create Invoice
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Invoice Number: INV-2026-001

Date: 2026-04-25

Customer Name: PT INDO JAYA MAKMUR

Status: DRAFT
(Dropdown: DRAFT, POSTED, PAID, CANCELLED)

Is Active: [✓] (Checked)

Button: [NEXT] atau [SAVE & CONTINUE TO ITEMS]
```

---

### Form 4a: Add Invoice Item 1
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Product: IKLAN KOLOM (SIGMA KOLOM)
(Dropdown - select from Products)

Quantity: 5

Unit Price: 1000000

Amount: 5000000 (Auto-calculated atau manual input)

Button: [ADD ITEM]
```

---

### Form 4b: Add Invoice Item 2
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Product: INVOICE MANAGEMENT SYSTEM
(Dropdown - select from Products)

Quantity: 2

Unit Price: 2000000

Amount: 4000000 (Auto-calculated atau manual input)

Button: [ADD ITEM]
```

---

### Form 5: Create Journal Entry
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Entry Number: JE-2026-001

Date: 2026-04-25

Description: Posting invoice INV-2026-001

Status: DRAFT
(Dropdown: DRAFT, POSTED)

Button: [NEXT] atau [SAVE & CONTINUE TO DETAILS]
```

---

### Form 5a: Add Journal Debit Entry
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Account: 1535
(Dropdown - select from Chart of Accounts, nama: CDF Piutang/AR)

Description: Piutang dari invoice

Debit: 9000000

Credit: 0 (Otomatis atau lock)

Button: [ADD DETAIL]
```

---

### Form 5b: Add Journal Credit Entry
```
Form Fields:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Account: 5033
(Dropdown - select from Chart of Accounts, nama: Revenue/Omzet)

Description: Pendapatan dari penjualan

Debit: 0 (Otomatis atau lock)

Credit: 9000000

Button: [ADD DETAIL]
```

---

## C. TESTING CHECKLIST DENGAN DATA

### Phase 1: Team Member Testing
- [ ] **Create TM-01**: 
  - Input: Code=06, Name=AGUS HERMAWAN, Position=MARKETING MANAGER
  - Expected: Team member created, ID auto-assigned
  
- [ ] **Read TM-01**: 
  - Expected: See 4 team members in list (01, 04, 05, 06)
  
- [ ] **Update TM-01**: 
  - Input: Update Position to "SENIOR MARKETING"
  - Expected: Position changed
  
- [ ] **Delete TM-01**: 
  - Action: Click delete on AGUS HERMAWAN
  - Expected: Team member deleted or deactivated

### Phase 2: Product Group Testing
- [ ] **Create PG-01**: 
  - Input: Code=SERVICE, Name=SERVICE DAN MAINTENANCE, Acc=5033, CDF=1535
  - Expected: Product group created
  
- [ ] **Read PG-01**: 
  - Expected: See 4 product groups (LSA, LSATEST, GPA, SERVICE)
  
- [ ] **Update PG-01**: 
  - Input: Update Name to "SERVICE DAN MAINTENANCE EXTENDED"
  - Expected: Name changed

### Phase 3: Product Testing
- [ ] **Create P-01**: 
  - Input: Code=05, Name=INVOICE MANAGEMENT SYSTEM, Group=LSA(1), Author=01
  - Expected: Product created with group relation
  
- [ ] **Read P-01**: 
  - Expected: See product with correct product group (LSA)
  
- [ ] **Update P-01**: 
  - Input: Update Compiler to LARAVEL 11
  - Expected: Compiler changed

### Phase 4: Invoice Testing
- [ ] **Create INV-01**: 
  - Input: Number=INV-2026-001, Customer=PT INDO JAYA MAKMUR, Status=DRAFT
  - Expected: Invoice created with status DRAFT
  
- [ ] **Add Item 1**: 
  - Input: Product=IKLAN KOLOM(2), Qty=5, Price=1M
  - Expected: Item added, amount=5M
  
- [ ] **Add Item 2**: 
  - Input: Product=INVOICE MGMT(5), Qty=2, Price=2M
  - Expected: Item added, amount=4M, Total=9M
  
- [ ] **Read INV-01**: 
  - Expected: Invoice with 2 items, total 9.000.000
  
- [ ] **Post INV-01**: 
  - Action: Click Post button
  - Expected: Status changed from DRAFT to POSTED

### Phase 5: Journal Entry Testing
- [ ] **Create JE-01**: 
  - Input: Number=JE-2026-001, Description=Posting INV-2026-001, Status=DRAFT
  - Expected: Journal entry created
  
- [ ] **Add Debit**: 
  - Input: Account=1535, Amount=9M
  - Expected: Debit entry added
  
- [ ] **Add Credit**: 
  - Input: Account=5033, Amount=9M
  - Expected: Credit entry added, total debit=credit
  
- [ ] **Verify Balanced**: 
  - Expected: Total Debit = 9M, Total Credit = 9M ✓ BALANCED
  
- [ ] **Post JE-01**: 
  - Action: Click Post button
  - Expected: Status changed to POSTED

---

## D. DATABASE VERIFICATION COMMANDS

Run these commands in `php artisan tinker` to verify data:

```php
// Check Team Members
TeamMember::all();

// Check specific product group with products
ProductGroup::where('code', 'LSA')->with('products')->first();

// Check products with relations
Product::with('productGroup')->get();

// Check invoices with items
Invoice::find(1)->with('invoiceItems.product');

// Check if journal entries are balanced
JournalEntry::find(1)->with('details')->first();

// Quick count summary
"Users: " . User::count() . ", Teams: " . TeamMember::count() . 
", Groups: " . ProductGroup::count() . ", Products: " . Product::count() . 
", Invoices: " . Invoice::count() . ", Entries: " . JournalEntry::count();
```

---

## E. EXPECTED RESPONSES (API Testing jika needed)

### Success Response Format
```json
{
  "success": true,
  "message": "Data created successfully",
  "data": {
    "id": 6,
    "code": "06",
    "name": "AGUS HERMAWAN",
    ...
  }
}
```

### Error Response Format
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "code": ["Code is required"],
    "name": ["Name must be unique"]
  }
}
```

---

**Note**: 
- Replace `2026-04-25` dengan tanggal hari ini jika testing dilakukan di hari berbeda
- Replace IDs dan relationships sesuai dengan data actual di database Anda
- Gunakan `php artisan tinker` untuk verify semua data sebelum dan sesudah testing
