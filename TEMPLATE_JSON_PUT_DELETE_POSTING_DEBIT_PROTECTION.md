# 📋 TEMPLATE JSON PUT & DELETE ENDPOINTS
## posting-payments, debit-credit-notes, protections

**Dibuat dari:** Scan database + analisis controller validation rules  
**Tanggal:** 2026-04-18  
**Status:** READY untuk testing - Semua data dari database yang ada

---

## ⚠️ PENTING - POSTING PAYMENTS STATUS

```
❌ PUT /api/posting-payments/{id} = TIDAK ADA dalam routes
❌ DELETE /api/posting-payments/{id} = TIDAK ADA dalam routes

✅ Hanya ada:
   - POST /api/posting/payments (postBatch - create new posting)
   - PUT /api/posting/payments/{id}/costs/{cost} (update costs)
   - DELETE /api/posting/payments/{id}/costs/{cost} (delete costs)
   
Catatan: Posting Payments tidak bisa di-UPDATE atau DELETE!
Hanya bisa dibuat (post) atau modify costs-nya saja.
```

---

## 1️⃣  PUT /api/debit-credit-notes/{id} - UPDATE Debit/Credit Note

**Controller:** DebitCreditNoteController.php (update method)  
**Authorization:** Requires: finance_admin, super_admin  
**Middleware:** period.unlocked (hanya saat periode tidak terkunci)

### Validation Rules:
```
type             : required | in:D,C
number           : required | unique:debit_credit_notes,number,{id}
date             : required | date
invoice_id       : nullable | exists:invoices,id
client_id        : nullable | exists:clients,id
description      : nullable | string
auto_journal     : nullable | boolean
is_posted        : nullable | boolean
dpp_amount       : nullable | numeric | min:0
ppn_amount       : nullable | numeric | min:0
total_amount     : nullable | numeric | min:0
items            : nullable | array
items.*.id       : nullable | integer | exists:debit_credit_note_items,id
items.*.sequence : nullable | integer | min:1
items.*.item_code: nullable | string | max:20
items.*.item_name: required_with:items | string | max:255
items.*.description: nullable | string
items.*.dpp_amount: required_with:items | numeric | min:0
items.*.ppn_amount: nullable | numeric | min:0
```

### JSON Template - ID 1:
```json
{
  "type": "D",
  "number": "DCN-202603-0001",
  "date": "2026-03-25",
  "invoice_id": 1,
  "client_id": 1,
  "description": "Adjustment for invoice",
  "auto_journal": true,
  "is_posted": false,
  "dpp_amount": 100000.00,
  "ppn_amount": 11000.00,
  "total_amount": 111000.00,
  "items": [
    {
      "sequence": 1,
      "item_code": "ADJ001",
      "item_name": "Adjustment Item 1",
      "description": "Penyesuaian nilai",
      "dpp_amount": 100000.00,
      "ppn_amount": 11000.00
    }
  ]
}
```

**Testing:** ID 1 → `PUT /api/debit-credit-notes/1`

### Business Rules:
- ⚠️ **Period Lock Check:** Hanya bisa update kalau periode tidak terkunci (middleware period.unlocked)
- ⚠️ **Posted Check:** Jika is_posted=true, hanya super_admin yang bisa update
- ✓ Items otomatis di-sinkronisasi (update, tambah, atau hapus items)
- ✓ dpp_amount dan total_amount otomatis di-hitung dari items jika tidak dikirim

---

## 2️⃣  DELETE /api/debit-credit-notes/{id} - DELETE Debit/Credit Note

**Controller:** DebitCreditNoteController.php (destroy method)  
**Authorization:** Requires: finance_admin, super_admin  
**Middleware:** period.unlocked (hanya saat periode tidak terkunci)

### Soft Delete atau Force Delete:
```
DELETE /api/debit-credit-notes/{id}              → Soft delete (logika bisnis)
DELETE /api/debit-credit-notes/{id}?force=true   → Force delete physical (super_admin only)
```

**Testing:** ID 1 → `DELETE /api/debit-credit-notes/1`

### Business Rules:
- ⚠️ **Period Lock:** Hanya bisa delete kalau periode tidak terkunci
- ⚠️ **Posted Check:** Jika is_posted=true, TIDAK bisa delete
- ✓ Default: Soft delete (delete flag set)
- ✓ Query param `force=true` untuk permanent delete

---

## 3️⃣  PUT /api/protections/{id} - UPDATE Protection Period

**Controller:** ProtectionController.php (update method)  
**Authorization:** Requires: super_admin ONLY  
**Role Check:** MANDATORY - super_admin only

### Validation Rules:
```
period       : required | unique:protection_periods,period,{id}
is_protected : nullable | boolean
protected_at : nullable | date
protected_by : nullable | exists:users,id
```

### JSON Template - ID 1:
```json
{
  "period": "2026-03",
  "is_protected": true,
  "protected_at": "2026-03-31",
  "protected_by": 1
}
```

**Testing:** ID 1 → `PUT /api/protections/1`

### Business Rules:
- 🔐 **Super Admin Only:** HANYA super_admin yang bisa PUT protection
- ⚠️ **Role Authorization:** Endpoint auto-check user role sebelum validate
- ✓ period format: YYYY-MM (untuk periode bulanan)
- ✓ protected_by harus user_id yang ada di database (User ID 1 = superadmin)
- ✓ Jika is_protected=true, protected_at dan protected_by akan di-set otomatis

---

## 4️⃣  DELETE /api/protections/{id} - DELETE Protection Period

**Controller:** ProtectionController.php (destroy method)  
**Authorization:** Requires: super_admin ONLY  
**Role Check:** MANDATORY

**Testing:** ID 1 → `DELETE /api/protections/1`

### Business Rules:
- 🔐 **Super Admin Only:** Hanya super_admin yang bisa DELETE
- ⚠️ **Be Careful:** Menghapus period protection akan MEMBUKA period untuk editing!
- ✓ Permanent delete (tidak soft delete)

---

## ⚠️ POSTING PAYMENTS - PUT & DELETE TIDAK ADA!

```
Routes yang ADA untuk posting-payments:
✓ POST    /api/posting/payments                              (Create batch posting)
✓ GET     /api/posting/payments                              (List postings)
✓ GET     /api/posting/payments/{id}                         (Show posting)
✓ GET     /api/posting/payments/{id}/costs                   (List costs)
✓ POST    /api/posting/payments/{id}/costs                   (Add cost)
✓ PUT     /api/posting/payments/{id}/costs/{cost}            (Update cost)
✓ DELETE  /api/posting/payments/{id}/costs/{cost}            (Delete cost)

Routes yang TIDAK ADA:
✗ PUT     /api/posting/payments/{id}                         (NO UPDATE POSTING!)
✗ DELETE  /api/posting/payments/{id}                         (NO DELETE POSTING!)
```

### Mengapa tidak ada PUT/DELETE untuk posting payment?
- Posting payments adalah **jurnal pembayaran final** yang sudah di-posting ke accounting
- Data ini bersifat **immutable (tidak bisa diubah)**
- Jika ingin modify, harus DELETE jurnal terlebih dahulu (manual proses)
- Hanya bisa modify **associated costs** (biaya-biaya tambahan)

---

## 📌 TESTING CHECKLIST

### Debit Credit Notes (ID 1):

**PUT /api/debit-credit-notes/1**
- [ ] Test dengan template di atas
- [ ] Pastikan user punya role: finance_admin atau super_admin
- [ ] Pastikan periode 2026-03 TIDAK terkunci
- [ ] Response: 200 OK dengan data yang ter-update
- [ ] Cek jika is_posted=false bisa update
- [ ] Test GAGAL jika is_posted=true (hanya super_admin)

**DELETE /api/debit-credit-notes/1**
- [ ] Test soft delete: `DELETE /api/debit-credit-notes/1`
- [ ] Response: 200 OK, deleted=true
- [ ] Test force delete: `DELETE /api/debit-credit-notes/1?force=true`
- [ ] Pastikan user punya role: finance_admin atau super_admin
- [ ] Jika is_posted=true, DELETE harus GAGAL

### Protection Periods (ID 1):

**PUT /api/protections/1**
- [ ] Test HANYA dengan super_admin token
- [ ] Test dengan template periode 2026-04 (atau nilai lain)
- [ ] Response: 200 OK dengan data ter-update
- [ ] Test GAGAL jika user role bukan super_admin (403)

**DELETE /api/protections/1**
- [ ] Test HANYA dengan super_admin token
- [ ] Response: 200 OK, deleted=true
- [ ] Test GAGAL jika user role bukan super_admin (403)
- [ ] Setelah delete, periode akan UNLOCK untuk editing

### Posting Payments:

- [ ] ✗ PUT /api/posting-payments/1 → Expect 405 Method Not Allowed
- [ ] ✗ DELETE /api/posting-payments/1 → Expect 405 Method Not Allowed
- [ ] ✓ PUT /api/posting/payments/1/costs/1 → Update cost (lihat template di bawah)
- [ ] ✓ DELETE /api/posting/payments/1/costs/1 → Delete cost

---

## 🔧 POSTING PAYMENT COSTS - PUT & DELETE (ADA!)

Jika ingin test PUT/DELETE untuk posting payments, gunakan costs endpoint:

### PUT /api/posting/payments/{posting_id}/costs/{cost_id}

**Validation:**
```
description : required | string | max:255
amount      : required | numeric | min:0
```

**Template - CORRECTED:**
```json
{
  "description": "Biaya admin",
  "amount": 500000.00
}
```

**Testing:** ID 1/1 → `PUT /api/posting/payments/1/costs/1`

### POST /api/posting/payments/{posting_id}/costs (Create new cost)

**Validation:**
```
description : required | string | max:255
amount      : required | numeric | min:0
```

**Template:**
```json
{
  "description": "Biaya operasional tambahan",
  "amount": 250000.00
}
```

**Testing:** ID 1 → `POST /api/posting/payments/1/costs`

### DELETE /api/posting/payments/{posting_id}/costs/{cost_id}

Tidak perlu body, hanya:
```
DELETE /api/posting/payments/1/costs/1
```

---

## 5️⃣  PUT /api/stop-licenses/{id} - UPDATE Stop License

**Controller:** StopLicenseController.php (update method)  
**Authorization:** Requires: super_admin, sales_operator  
**Middleware:** role:super_admin,sales_operator

### Validation Rules:
```
number           : required | string | max:50 | unique:stop_licenses,number,{id}
date             : required | date
stop_date        : required | date
work_order_id    : required | exists:work_orders,id
client_id        : nullable | exists:clients,id
product_id       : nullable | exists:products,id
client_spv_id    : nullable | exists:team_members,id
client_spv_code  : nullable | string
notes            : nullable | string
is_stopped       : nullable | boolean
```

### JSON Template - ID 1:
```json
{
  "number": "001/MIS-03/26",
  "date": "2026-03-20",
  "stop_date": "2026-03-01",
  "work_order_id": 1,
  "client_id": 1,
  "product_id": 1,
  "client_spv_id": 6,
  "client_spv_code": "03",
  "notes": "BERHENTI LISENSI",
  "is_stopped": true
}
```

**Testing:** ID 1 → `PUT /api/stop-licenses/1`

### Business Rules:
- ✓ client_id & product_id otomatis di-fill dari work_order_id jika tidak dikirim
- ✓ client_spv_code akan di-resolve ke client_spv_id via TeamMember lookup
- ✓ number harus unik per stop-license
- ✓ stop_date harus date format YYYY-MM-DD
- ✓ is_stopped true = lisensi sudah dihentikan

---

## 6️⃣  DELETE /api/stop-licenses/{id} - DELETE Stop License

**Controller:** StopLicenseController.php (destroy method)  
**Authorization:** Requires: super_admin, sales_operator  
**Middleware:** role:super_admin,sales_operator

**Testing:** ID 1 → `DELETE /api/stop-licenses/1`

### Business Rules:
- ✓ Permanent delete (force delete)
- ✓ Tidak ada soft delete untuk stop-licenses
- ✓ Hanya super_admin dan sales_operator yang bisa delete

---

## 📋 UPDATED SUMMARY - MANA YANG BISA TEST?

| Endpoint | Method | Status | ID | Body |
|----------|--------|--------|----|----|
| debit-credit-notes | PUT | ✅ ADA | 1 | Ada (items nested) |
| debit-credit-notes | DELETE | ✅ ADA | 1 | - |
| protections | PUT | ✅ ADA | 1 | Ada (period format YYYY-MM) |
| protections | DELETE | ✅ ADA | 1 | - |
| stop-licenses | PUT | ✅ ADA | 1 | Ada (work_order, client, product, etc) |
| stop-licenses | DELETE | ✅ ADA | 1 | - |
| posting-payments | PUT | ❌ TIDAK | - | - |
| posting-payments | DELETE | ❌ TIDAK | - | - |
| posting-payments costs | POST | ✅ ADA | 1 | Ada (description, amount) |
| posting-payments costs | PUT | ✅ ADA | 1/1 | Ada (description, amount) - CORRECTED |
| posting-payments costs | DELETE | ✅ ADA | 1/1 | - |

---

## 🚀 CORRECTED TESTING ORDER

1. **FIRST - Fix posting-payments costs template:**
   - ✓ Model: `description` + `amount` (BUKAN item_id, qty, price)
   - PUT /api/posting/payments/1/costs/1 dengan template baru
   - DELETE /api/posting/payments/1/costs/1

2. **SECOND - Period Unlock (CRITICAL):**
   - DELETE /api/protections/1 (unlock periode 2026-03)

3. **THIRD - Test Stop Licenses (NEW):**
   - PUT /api/stop-licenses/1 dengan template
   - DELETE /api/stop-licenses/1

4. **FOURTH - Test Debit Credit Notes:**
   - PUT /api/debit-credit-notes/1
   - DELETE /api/debit-credit-notes/1

---

**All templates corrected and ready! 🚀**
