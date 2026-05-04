# ⚡ QUICK START TESTING CHECKLIST

## 🎯 Pre-Testing Setup

- [ ] Open browser and navigate to application URL
- [ ] Login with **Super Admin** account:
  - Username: `superadmin`
  - Email: `superadmin@fitart.co.id`
- [ ] Have these files open for reference:
  - `GUIDE_TESTING_FRONTEND_LENGKAP.md` ← Main guide
  - `DATA_TESTING_REFERENCE.md` ← Data templates
  - `TINKER_COMMANDS_REFERENCE.txt` ← DB verification
- [ ] Open terminal with `php artisan tinker` ready
- [ ] Have Database viewer ready (PHPMyAdmin or similar)

---

## 📝 TESTING WORKFLOW

### ✅ Test 1: Team Member Management (15 minutes)

**Location**: Menu → Team Members / Manajemen Tim

| Step | Action | Data | Expected Result | Status |
|------|--------|------|-----------------|--------|
| 1 | Create | Code: 06, Name: AGUS HERMAWAN, Position: MARKETING MANAGER | New TM created | [ ] |
| 2 | View List | Browse all team members | Show 4+ members | [ ] |
| 3 | Update | Edit ADIT position to "SENIOR PROGRAMMER" | Position updated | [ ] |
| 4 | Delete | Delete AGUS HERMAWAN | TM deleted/inactive | [ ] |
| 5 | Verify DB | Run `TeamMember::all()` in tinker | 3 active members | [ ] |

---

### ✅ Test 2: Product Group Management (15 minutes)

**Location**: Menu → Product Groups / Grup Produk

| Step | Action | Data | Expected Result | Status |
|------|--------|------|-----------------|--------|
| 1 | Create | Code: SERVICE, Name: SERVICE DAN MAINTENANCE, Acc: 5033, CDF: 1535 | New PG created | [ ] |
| 2 | View List | Browse all product groups | Show LSA, LSATEST, GPA, SERVICE | [ ] |
| 3 | View Detail | Click LSA group | Show LSA with 1+ products | [ ] |
| 4 | Update | Edit GPA add Acc: 5033, CDF: 1535 | Accounting codes added | [ ] |
| 5 | Verify DB | Run `ProductGroup::all()` in tinker | 4 groups shown | [ ] |

---

### ✅ Test 3: Product Management (15 minutes)

**Location**: Menu → Products / Produk

| Step | Action | Data | Expected Result | Status |
|------|--------|------|-----------------|--------|
| 1 | Create | Code: 05, Name: INVOICE MANAGEMENT SYSTEM, Group: LSA(1), Author: IMAM S ARIFIN | New product created | [ ] |
| 2 | View List | Browse all products | Show IKLAN KOLOM, Arfey Moreno, INVOICE MGMT | [ ] |
| 3 | View Detail | Click INVOICE MGMT product | Show group: LSA, author: 01 | [ ] |
| 4 | Update | Edit IKLAN KOLOM compiler to "DELPHI 10" | Compiler updated | [ ] |
| 5 | Verify DB | Run `Product::with('productGroup')->get()` | Relations shown | [ ] |

---

### ✅ Test 4: Invoice Management (20 minutes)

**Location**: Menu → Invoices / Faktur

| Step | Action | Data | Expected Result | Status |
|------|--------|------|-----------------|--------|
| 1 | Create | Number: INV-2026-001, Customer: PT INDO JAYA MAKMUR, Status: DRAFT | Invoice created | [ ] |
| 2 | Add Item 1 | Product: IKLAN KOLOM, Qty: 5, Price: 1.000.000 | Item added, amount: 5.000.000 | [ ] |
| 3 | Add Item 2 | Product: INVOICE MGMT, Qty: 2, Price: 2.000.000 | Item added, amount: 4.000.000 | [ ] |
| 4 | View Detail | Open INV-2026-001 | Total: 9.000.000 (5M + 4M) ✓ | [ ] |
| 5 | Update | Change customer to "PT MAJU JAYA BERSAMA" | Customer updated | [ ] |
| 6 | Post Invoice | Click Post button | Status: DRAFT → POSTED | [ ] |
| 7 | Verify DB | Run `Invoice::with('invoiceItems.product')->get()` | Invoice + 2 items shown | [ ] |

---

### ✅ Test 5: Journal Entry / Accounting (20 minutes)

**Location**: Menu → Journal Entries / Jurnal Akuntansi

| Step | Action | Data | Expected Result | Status |
|------|--------|------|-----------------|--------|
| 1 | Create | Number: JE-2026-001, Description: Posting invoice, Status: DRAFT | Entry created | [ ] |
| 2 | Add Debit | Account: 1535 (Piutang), Amount: 9.000.000 | Debit entry added | [ ] |
| 3 | Add Credit | Account: 5033 (Revenue), Amount: 9.000.000 | Credit entry added | [ ] |
| 4 | Check Balance | View total debit vs credit | Debit = Credit = 9M ✓ BALANCED | [ ] |
| 5 | View Detail | Open JE-2026-001 | All details + balance shown | [ ] |
| 6 | Post Entry | Click Post button | Status: DRAFT → POSTED | [ ] |
| 7 | Verify DB | Run `JournalEntry::with('details')->get()` | Entry + 2 details balanced | [ ] |

---

## 🔍 Quick Verification Checklist

After testing, verify in database:

```bash
# Open terminal
php artisan tinker

# Run these commands and check results:
```

| Command | Expected Output | Check |
|---------|-----------------|-------|
| `User::count()` | 6 (or more) | [ ] |
| `TeamMember::count()` | 4 | [ ] |
| `ProductGroup::count()` | 4 | [ ] |
| `Product::count()` | 3 | [ ] |
| `Invoice::count()` | 1+ | [ ] |
| `InvoiceItem::count()` | 2+ | [ ] |
| `JournalEntry::count()` | 1+ | [ ] |

**Check Relationships**:
```
ProductGroup::where('code', 'LSA')->with('products')->first();
→ Should show LSA with 1+ products

Product::where('code', '05')->with('productGroup')->first();
→ Should show INVOICE MGMT with Group: LSA

Invoice::with('invoiceItems.product')->first();
→ Should show invoice with 2 items and products
```

---

## ❌ Troubleshooting

| Issue | Solution |
|-------|----------|
| Form tidak submit | Check browser console for JS errors |
| Data tidak muncul | Refresh page, check DB dengan tinker |
| Total invoice salah | Verify item amounts, check DB calculation |
| Journal tidak balanced | Check debit vs credit amounts match |
| Can't login | Use credentials from scan database output |
| Dropdown kosong | Ensure related data exists in DB |

---

## 📊 Testing Summary Form

**Date**: ________________
**Tester Name**: ________________
**Browser**: ________________ (Chrome/Firefox/etc)

### Results

| Feature | Passed | Failed | Notes |
|---------|--------|--------|-------|
| Team Members | [ ] | [ ] | |
| Product Groups | [ ] | [ ] | |
| Products | [ ] | [ ] | |
| Invoices | [ ] | [ ] | |
| Invoice Items | [ ] | [ ] | |
| Journal Entries | [ ] | [ ] | |

### Issues Found

```
Issue #1: ___________________________________
  Steps to reproduce: _______________________
  Expected: ________________________________
  Actual: __________________________________
  
Issue #2: ___________________________________
  Steps to reproduce: _______________________
  Expected: ________________________________
  Actual: __________________________________
```

### Database Data Count After Testing

- Users: ____
- Team Members: ____
- Product Groups: ____
- Products: ____
- Invoices: ____
- Invoice Items: ____
- Journal Entries: ____

**Overall Status**: ✅ PASSED / ❌ FAILED / ⚠️ PARTIAL

**Signature**: _________________ **Date**: _________________

---

## 🎓 Common Testing Mistakes to Avoid

❌ **Don't**:
- Test without checking database (use tinker verify)
- Create duplicate codes (system should reject)
- Submit empty required fields (should show error)
- Forget to test relationships (product group, items, etc)
- Skip the balance check for journal entries
- Test only happy path (test error scenarios too)

✅ **Do**:
- Verify data in DB after each action
- Use exact data from reference file
- Check browser console for errors
- Test both Create, Read, Update, Delete
- Verify all form validations work
- Check relationships are saved correctly
- Test status changes (DRAFT → POSTED)

---

## 📞 When to Ask for Help

Ask developer if:
- Form fields don't match documentation
- Database operations don't work
- Unexpected error messages appear
- Data seems to save but doesn't appear
- Relationships not loading correctly

---

**Last Updated**: 25 April 2026
**Quick Reference Version**: 1.0
**Estimated Testing Time**: 1.5 - 2 hours (all features)
