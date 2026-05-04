# 🔴 47 ENDPOINT BELUM TESTED (Tidak Ada Saved Response)

**Status:** Semua endpoint ini ADA di Postman, tapi TIDAK ADA saved response-nya  
**Action:** Testing dari endpoint ini mulai sekarang  
**Priority:** URGENT - Update dan Delete critical!

---

## 📋 DAFTAR 47 ENDPOINT YANG HARUS DITEST

### 🟥 CRITICAL - DELETE Operations (22 endpoint)
Semua DELETE belum tested - HARUS dikerjakan duluan!

```
1.  DELETE /api/tax-series/{taxSeries}
2.  DELETE /api/banks/{bank}
3.  DELETE /api/clients/{client}
4.  DELETE /api/companies/{company}
5.  DELETE /api/invoice-types/{invoice_type}
6.  DELETE /api/items/{item}
7.  DELETE /api/master-item-products/{master_item_product}
8.  DELETE /api/product-groups/{productGroup}
9.  DELETE /api/products/{product}
10. DELETE /api/team-members/{team_member}
11. DELETE /api/users/{user}
12. DELETE /api/installations/{installation}
13. DELETE /api/licenses/{license}
14. DELETE /api/stop-licenses/{stop_license}
15. DELETE /api/work-orders/{work_order}
16. DELETE /api/debit-credit-notes/{debit_credit_note}
17. DELETE /api/invoice-items/{invoice_item}
18. DELETE /api/invoices/{invoice}
19. DELETE /api/payments/{payment}
20. DELETE /api/posting/payments/{postingPayment}/costs/{cost}
21. DELETE /api/protections/{protection}
```

**Cara Test (Contoh DELETE /api/banks/{bank}):**
```
1. Klik request di Postman
2. Ganti :bank dengan ID yang valid (misal: 1)
3. Klik SEND
4. Catat response-nya
5. Klik "Save Response" → simpan
6. Repeat untuk semua DELETE lainnya
```

---

### 🟨 PUT/UPDATE Operations (15 endpoint)
Semua UPDATE belum tested!

```
1.  PUT /api/banks/{bank}
2.  PUT /api/clients/{client}
3.  PUT /api/companies/{company}
4.  PUT /api/invoice-types/{invoice_type}
5.  PUT /api/items/{item}
6.  PUT /api/master-item-products/{master_item_product}
7.  PUT /api/product-groups/{productGroup}
8.  PUT /api/products/{product}
9.  PUT /api/team-members/{team_member}
10. PUT /api/users/{user}
11. PUT /api/installations/{installation}
12. PUT /api/licenses/{license}
13. PUT /api/stop-licenses/{stop_license}
14. PUT /api/work-orders/{work_order}
15. PUT /api/debit-credit-notes/{debit_credit_note}
16. PUT /api/invoice-items/{invoice_item}
17. PUT /api/invoices/{invoice}
18. PUT /api/payments/{payment}
19. PUT /api/posting/payments/{postingPayment}/costs/{cost}
20. PUT /api/protections/{protection}
```

**Cara Test (Contoh PUT /api/banks/{bank}):**
```
1. Klik request di Postman
2. Masuk ke Body tab → masukkan data update
3. Ganti :bank dengan ID yang valid
4. Klik SEND
5. Catat response
6. Klik "Save Response" → simpan
7. Repeat untuk semua PUT lainnya
```

---

### 🟦 POST/Special Operations (10 endpoint)
Beberapa POST critical belum tested!

```
1. POST /api/logout                          (HIGH - Session security)
2. POST /api/users                           (HIGH - User creation)
3. POST /api/users/{user}/assign-role        (CRITICAL - Role assignment)
4. POST /api/users/{user}/toggle-status      (HIGH - User activation)
5. POST /api/print/invoices/{invoice}/pdf    (HIGH - Invoice PDF generation)
6. POST /api/print/receipts/{posting_payment}/pdf  (HIGH - Receipt PDF)
7. POST /api/debit-credit-notes              (MEDIUM)
8. POST /api/invoice-items                   (MEDIUM)
9. POST /api/protections/{protection}        (CRITICAL - Period protection)
10. PUT (PATCH) /api/protections/{protection} (CRITICAL)
```

**Cara Test (Contoh POST /api/logout):**
```
1. Klik request di Postman
2. Pastikan sudah login (ada token valid di {{token}})
3. Klik SEND
4. Catat response
5. Klik "Save Response" → simpan
```

---

## ✅ CHECKLIST TESTING

### Harium dikerjakan HARI INI:

```
DELETE Operations (22):
□ Coba 3 DELETE endpoint dulu (banks, clients, companies)
□ Jika tidak error → lanjut DELETE lainnya (tax-series, invoice-types, items, dll)
□ Total: 22 DELETE harus selesai

PUT/UPDATE Operations (15):
□ Coba 3 PUT endpoint (banks, clients, companies)
□ Jika berhasil → lanjut PUT lainnya
□ Total: 15 PUT harus selesai

POST/Special (10):
□ POST /api/logout
□ POST /api/users
□ POST /api/users/{user}/assign-role
□ POST /api/users/{user}/toggle-status
□ Sisanya (5 POST lain)

TOTAL: 47 Endpoint selesai ditest semua
```

---

## 📌 TIPS CEPAT

**Untuk Delete/Put yang butuh ID:**
- Ambil ID dari response GET sebelumnya
- Contoh: GET /api/banks → ambil id dari response → gunakan di DELETE/PUT

**Untuk endpoint PDF:**
- Pastikan header Accept correct
- Redirect ke binary file akan terlihat di response

**Jangan khawatir error 404:**
- Jika ID tidak ada → normal 404
- Yang penting: Save response-nya (success atau error sama-sama)

---

**Mulai dari DELETE dulu! Biasanya yang paling cepat ditest. ✅**
