# 📚 TESTING GUIDE - INDEX & QUICK LINKS

## 🎯 Start Here - Choose Your Guide

### 👥 For QA / Frontend Testers
**New to testing? Start here:**
1. **[QUICK_START_TESTING.md](QUICK_START_TESTING.md)** ⭐ START HERE
   - Quick checklist format
   - Step-by-step testing workflows
   - Easy to follow while testing
   - ~1.5-2 hours to complete all tests

2. **[GUIDE_TESTING_FRONTEND_LENGKAP.md](GUIDE_TESTING_FRONTEND_LENGKAP.md)** (Comprehensive)
   - Detailed guide with explanations
   - All features covered
   - Best practices included
   - Database relationships explained

3. **[docs/GUIDE_TESTING_FRONTEND_DATABASE_AKTUAL.md](docs/GUIDE_TESTING_FRONTEND_DATABASE_AKTUAL.md)** (Current database snapshot)
   - Sinkron dengan data database aktual
   - Kode, ID, dan relasi yang benar-benar ada
   - Cocok untuk QA yang ingin test tanpa asumsi

4. **[DATA_TESTING_REFERENCE.md](DATA_TESTING_REFERENCE.md)** (Reference)
   - Copy-paste test data
   - Form templates
   - Quick reference tables
   - Database verification commands

---

### 👨‍💻 For Developers
**Need to verify backend? Start here:**

1. **[ADVANCED_TESTING_GUIDE.md](ADVANCED_TESTING_GUIDE.md)** ⭐ FOR DEVELOPERS
   - API endpoint testing
   - Validation rules
   - Error scenarios
   - Security testing
   - Performance testing
   - Database integrity checks

2. **[TINKER_COMMANDS_REFERENCE.txt](TINKER_COMMANDS_REFERENCE.txt)**
   - PHP Artisan Tinker commands
   - Database query examples
   - Copy-paste commands
   - Data creation & verification

3. **[VERIFICATION_SCRIPTS_TINKER.php](VERIFICATION_SCRIPTS_TINKER.php)**
   - Complete verification script
   - Run in `php artisan tinker`
   - Checks all models and relationships

---

## 📑 FILE GUIDE

| File | Purpose | Best For | Read Time |
|------|---------|----------|-----------|
| **QUICK_START_TESTING.md** | Quick checklist format | QA/Tester | 30 min |
| **GUIDE_TESTING_FRONTEND_LENGKAP.md** | Complete detailed guide | Everyone | 1-2 hours |
| **DATA_TESTING_REFERENCE.md** | Test data & templates | QA/Tester | 20 min |
| **ADVANCED_TESTING_GUIDE.md** | API & edge cases | Developer | 1 hour |
| **TINKER_COMMANDS_REFERENCE.txt** | Database commands | Developer | 15 min |
| **VERIFICATION_SCRIPTS_TINKER.php** | Auto-verify database | Developer | 10 min |

---

## 🚀 Quick Navigation

### Testing Team Members
- **For QA**: [QUICK_START_TESTING.md - Test 1](QUICK_START_TESTING.md#test-1-team-member-management-15-minutes)
- **For Tester**: [GUIDE_TESTING_FRONTEND_LENGKAP.md - Section A](GUIDE_TESTING_FRONTEND_LENGKAP.md#a-testing-team-member-management)
- **For Developer**: [ADVANCED_TESTING_GUIDE.md - API](ADVANCED_TESTING_GUIDE.md#team-members-endpoints)

### Testing Product Groups
- **For QA**: [QUICK_START_TESTING.md - Test 2](QUICK_START_TESTING.md#test-2-product-group-management-15-minutes)
- **For Tester**: [GUIDE_TESTING_FRONTEND_LENGKAP.md - Section B](GUIDE_TESTING_FRONTEND_LENGKAP.md#b-testing-product-group-management)
- **For Developer**: [ADVANCED_TESTING_GUIDE.md - API](ADVANCED_TESTING_GUIDE.md#product-groups-endpoints)

### Testing Products
- **For QA**: [QUICK_START_TESTING.md - Test 3](QUICK_START_TESTING.md#test-3-product-management-15-minutes)
- **For Tester**: [GUIDE_TESTING_FRONTEND_LENGKAP.md - Section C](GUIDE_TESTING_FRONTEND_LENGKAP.md#c-testing-product-management)
- **For Developer**: [ADVANCED_TESTING_GUIDE.md - API](ADVANCED_TESTING_GUIDE.md#products-endpoints)

### Testing Invoices
- **For QA**: [QUICK_START_TESTING.md - Test 4](QUICK_START_TESTING.md#test-4-invoice-management-20-minutes)
- **For Tester**: [GUIDE_TESTING_FRONTEND_LENGKAP.md - Section D](GUIDE_TESTING_FRONTEND_LENGKAP.md#d-testing-invoice-management)
- **For Developer**: [ADVANCED_TESTING_GUIDE.md - API](ADVANCED_TESTING_GUIDE.md#invoices-endpoints)

### Testing Journal Entries
- **For QA**: [QUICK_START_TESTING.md - Test 5](QUICK_START_TESTING.md#test-5-journal-entry--accounting-20-minutes)
- **For Tester**: [GUIDE_TESTING_FRONTEND_LENGKAP.md - Section E](GUIDE_TESTING_FRONTEND_LENGKAP.md#e-testing-journal-entry--posting-accounting)
- **For Developer**: [ADVANCED_TESTING_GUIDE.md - API](ADVANCED_TESTING_GUIDE.md#journal-entries-endpoints)

### Validations & Error Handling
- [ADVANCED_TESTING_GUIDE.md - Validation Section](ADVANCED_TESTING_GUIDE.md#validation-testing)

### Database Verification
- [TINKER_COMMANDS_REFERENCE.txt](TINKER_COMMANDS_REFERENCE.txt)
- [VERIFICATION_SCRIPTS_TINKER.php](VERIFICATION_SCRIPTS_TINKER.php)

---

## 📊 CURRENT DATABASE DATA

### Summary
```
Users:              6 total
Team Members:       5 total (01, 03, 04, 05, 06)
Product Groups:     3 total (LSA, LSATEST, SERVICE)
Products:           2 total (026724, 05)
Chart of Accounts:  39 total
Invoices:           0 active
Invoice Items:      0 active
Journal Entries:    5 total
```

### Key Relationships
```
Product Group (1) ─┬─ Products (N)
                   ├─ Chart of Accounts (Acc Omzet)
                   └─ Chart of Accounts (CDF Piutang)

Product (1) ─┬─ Author (Team Member, via code)
             └─ Invoice Items (N)

Invoice (1) ──┬─ Invoice Items (N)
              └─ Journal Entries (via posting)

Journal Entry (1) ──┬─ Details (N)
                    └─ Chart of Accounts (N)
```

---

## 🎓 LEARNING PATH

### For First-Time Testers
1. Read [QUICK_START_TESTING.md](QUICK_START_TESTING.md) (30 min)
2. Reference [DATA_TESTING_REFERENCE.md](DATA_TESTING_REFERENCE.md) while testing (as needed)
3. Use [GUIDE_TESTING_FRONTEND_LENGKAP.md](GUIDE_TESTING_FRONTEND_LENGKAP.md) for detailed info (as needed)
4. Check database with [TINKER_COMMANDS_REFERENCE.txt](TINKER_COMMANDS_REFERENCE.txt) commands (as needed)

### For Experienced Testers
1. Skim [QUICK_START_TESTING.md](QUICK_START_TESTING.md) (5 min)
2. Jump to specific feature sections in [GUIDE_TESTING_FRONTEND_LENGKAP.md](GUIDE_TESTING_FRONTEND_LENGKAP.md)
3. Use [ADVANCED_TESTING_GUIDE.md](ADVANCED_TESTING_GUIDE.md) for edge cases

### For Developers
1. Review [ADVANCED_TESTING_GUIDE.md](ADVANCED_TESTING_GUIDE.md) (1 hour)
2. Run verification commands from [TINKER_COMMANDS_REFERENCE.txt](TINKER_COMMANDS_REFERENCE.txt)
3. Execute [VERIFICATION_SCRIPTS_TINKER.php](VERIFICATION_SCRIPTS_TINKER.php)
4. Check API endpoints using curl (commands in advanced guide)

---

## 🔧 QUICK COMMANDS

### Verify Data in Database
```bash
php artisan tinker

# In tinker, run:
TeamMember::all();
ProductGroup::with('products')->get();
Product::with('productGroup')->get();
Invoice::with('invoiceItems.product')->get();
JournalEntry::with('details')->get();
```

### Create Test Data
```bash
php artisan tinker < TINKER_COMMANDS_REFERENCE.txt
```

### Check Database Structure
```php
php artisan tinker
# Then run commands from VERIFICATION_SCRIPTS_TINKER.php
```

---

## 🆘 TROUBLESHOOTING

### Data Not Showing
1. Check [DATA_TESTING_REFERENCE.md - Section D](DATA_TESTING_REFERENCE.md#d-database-verification-commands)
2. Run verification commands
3. Check browser console for errors

### Test Failing
1. Check specific feature in [GUIDE_TESTING_FRONTEND_LENGKAP.md](GUIDE_TESTING_FRONTEND_LENGKAP.md)
2. Verify data with database commands
3. Check [ADVANCED_TESTING_GUIDE.md - Error Scenarios](ADVANCED_TESTING_GUIDE.md#error-scenarios)

### Don't Know Which File to Use
1. You're a **QA/Tester**? → Use [QUICK_START_TESTING.md](QUICK_START_TESTING.md)
2. You need **detailed guide**? → Use [GUIDE_TESTING_FRONTEND_LENGKAP.md](GUIDE_TESTING_FRONTEND_LENGKAP.md)
3. You need **test data**? → Use [DATA_TESTING_REFERENCE.md](DATA_TESTING_REFERENCE.md)
4. You're a **Developer**? → Use [ADVANCED_TESTING_GUIDE.md](ADVANCED_TESTING_GUIDE.md)
5. You need **DB commands**? → Use [TINKER_COMMANDS_REFERENCE.txt](TINKER_COMMANDS_REFERENCE.txt)

---

## 📋 TESTING PROGRESS TRACKER

### Overall Progress
- [ ] All guides read and understood
- [ ] Database verified (data exists)
- [ ] Team Member testing completed
- [ ] Product Group testing completed
- [ ] Product testing completed
- [ ] Invoice testing completed
- [ ] Journal Entry testing completed
- [ ] All validations tested
- [ ] Error scenarios tested
- [ ] Database consistency verified

### Testing Summary
| Feature | Status | Notes |
|---------|--------|-------|
| Team Members | [ ] Pass [ ] Fail | |
| Product Groups | [ ] Pass [ ] Fail | |
| Products | [ ] Pass [ ] Fail | |
| Invoices | [ ] Pass [ ] Fail | |
| Invoice Items | [ ] Pass [ ] Fail | |
| Journal Entries | [ ] Pass [ ] Fail | |

---

## 📞 WHEN TO USE EACH GUIDE

### Use QUICK_START_TESTING.md When:
- ✅ You want a quick checklist to follow
- ✅ You have 1-2 hours for testing
- ✅ You want clear step-by-step instructions
- ✅ You need a printable guide

### Use GUIDE_TESTING_FRONTEND_LENGKAP.md When:
- ✅ You want detailed explanations
- ✅ You need to understand what you're testing
- ✅ You're new to the application
- ✅ You need database structure information

### Use DATA_TESTING_REFERENCE.md When:
- ✅ You need copy-paste test data
- ✅ You want form templates
- ✅ You need quick data reference
- ✅ You're testing locally

### Use ADVANCED_TESTING_GUIDE.md When:
- ✅ You're testing APIs
- ✅ You need to verify validations
- ✅ You need edge case scenarios
- ✅ You're a developer

### Use TINKER_COMMANDS_REFERENCE.txt When:
- ✅ You need database commands
- ✅ You want to verify data
- ✅ You need to create test data
- ✅ You're debugging issues

---

## 🎯 TESTING GOALS

By the end of testing, you should have:
- ✅ Tested all 5 main features (Team, Group, Product, Invoice, Journal)
- ✅ Verified all CRUD operations (Create, Read, Update, Delete)
- ✅ Checked all relationships between tables
- ✅ Tested validation rules
- ✅ Tested error scenarios
- ✅ Confirmed database integrity
- ✅ Documented any issues found
- ✅ Verified all data calculations (invoice totals, journal balances)

---

## 📅 ESTIMATED TIME

| Activity | Time | Notes |
|----------|------|-------|
| Read QUICK_START_TESTING.md | 30 min | Review checklist |
| Setup & Login | 5 min | Get browser ready |
| Test Team Members | 15 min | CRUD operations |
| Test Product Groups | 15 min | With relationships |
| Test Products | 15 min | With FK relations |
| Test Invoices & Items | 20 min | Multi-item scenario |
| Test Journal Entries | 20 min | Balance verification |
| Verify Database | 10 min | Run tinker commands |
| Document Issues | 10 min | Record findings |
| **TOTAL** | **~2 hours** | Full test coverage |

---

## ✅ COMPLETION CHECKLIST

After completing all tests:

- [ ] All 5 features tested
- [ ] All CRUD operations verified
- [ ] No major bugs found (or documented)
- [ ] Database data is consistent
- [ ] Relationships working correctly
- [ ] Validations enforced
- [ ] Error handling working
- [ ] Status transitions work (DRAFT → POSTED)
- [ ] Calculations correct (invoice total, journal balance)
- [ ] Tested on Chrome, Firefox (if possible)
- [ ] Responsive design checked
- [ ] Issues documented in testing report

---

**Created**: 25 April 2026
**Version**: 1.0 - Complete
**Status**: Ready for Testing
**Total Guides**: 6 files
**Total Documentation**: ~50+ pages

---

## 📞 Support

If you have questions about:
- **Which guide to use** → See section "Quick Navigation"
- **How to test a feature** → Check the feature section in GUIDE_TESTING_FRONTEND_LENGKAP.md
- **Database verification** → Use TINKER_COMMANDS_REFERENCE.txt
- **API testing** → See ADVANCED_TESTING_GUIDE.md
- **Test data** → Use DATA_TESTING_REFERENCE.md

**Good luck with testing! 🚀**
