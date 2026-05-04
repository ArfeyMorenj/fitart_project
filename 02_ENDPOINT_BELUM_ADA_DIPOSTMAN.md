# 🟧 26 ENDPOINT BELUM ADA DI POSTMAN (Harus Ditambahkan)

**Status:** Endpoint ini ada di backend tapi TIDAK ada di Postman collection  
**Action:** Tambahkan ke Postman + Test  
**Priority:** Sedang (setelah 47 endpoint tested)

---

## 📋 DAFTAR 26 ENDPOINT YANG HILANG

### 🔴 CRITICAL - GET Endpoints yang Missing (6 endpoint)

Ini adalah endpoint penting yang tidak ada documentation:

```
1. GET /api/invoice-license               (List semua invoice license)
2. GET /api/invoice-license/{invoice}     (Detail invoice license)
3. GET /api/invoice-license/search        (Search invoice license)
4. GET /api/debit-credit-notes/search     (Search DCN)
5. GET /api/posting/payments              (List semua posting payments)
6. GET /api/posting/payments/{postingPayment}  (Detail posting payment)
```

---

### 🟨 MEDIUM - PATCH Operations (20 endpoint)

Laravel auto-generate PATCH untuk semua resource update. Tim backend support PATCH tapi Postman tidak document:

```
1.  PATCH /api/banks/{bank}
2.  PATCH /api/clients/{client}
3.  PATCH /api/companies/{company}
4.  PATCH /api/invoice-types/{invoice_type}
5.  PATCH /api/items/{item}
6.  PATCH /api/master-item-products/{id}
7.  PATCH /api/product-groups/{productGroup}
8.  PATCH /api/products/{product}
9.  PATCH /api/team-members/{team_member}
10. PATCH /api/users/{user}
11. PATCH /api/installations/{installation}
12. PATCH /api/licenses/{license}
13. PATCH /api/stop-licenses/{stop_license}
14. PATCH /api/work-orders/{work_order}
15. PATCH /api/debit-credit-notes/{id}
16. PATCH /api/invoice-items/{invoice_item}
17. PATCH /api/invoices/{invoice}
18. PATCH /api/payments/{payment}
19. PATCH /api/posting/payments/{id}/costs/{cost}
20. PATCH /api/protections/{protection}
```

**Catatan PATCH:** Sama seperti PUT, tapi hanya update field yang dikiriм (partial update)

---

## 📌 CARA MENAMBAHKAN KE POSTMAN

### Step 1: Untuk GET Endpoints

```
1. Klik "+" → New Request
2. Method: GET
3. URL: {{base_url}}/api/invoice-license
4. Headers: Authorization: Bearer {{token}}
5. Klik SEND
6. Catat response
7. Klik "Save Response"
8. Nama: "GET /api/invoice-license"
9. Save ke folder yang sesuai
```

### Step 2: Untuk PATCH Endpoints

```
1. Klik "+" → New Request
2. Method: PATCH
3. URL: {{base_url}}/api/banks/{bank}  (ganti {bank} dengan ID)
4. Headers: Authorization; Bearer {{token}}
5. Body: (minimal 1 field yang mau diupdate)
   {
     "name": "Updated Bank Name"
   }
6. Klik SEND
7. Catat response
8. Klik "Save Response"
9. Nama: "PATCH /api/banks/{bank}"
```

---

## ✅ CHECKLIST PENAMBAHAN

### Urutan Priority:

```
PRIORITY 1 - GET Critical (6 endpoint):
□ GET /api/invoice-license
□ GET /api/invoice-license/{invoice}
□ GET /api/invoice-license/search
□ GET /api/debit-credit-notes/search
□ GET /api/posting/payments
□ GET /api/posting/payments/{postingPayment}
Total: ~30 menit

PRIORITY 2 - PATCH Operations (20 endpoint):
□ Semua 20 PATCH (bisa batch, semua sama)
Total: ~60 menit

TOTAL: ~90 menit (1.5 jam)
```

---

## 💡 TIPS

**GET Endpoints:**
- Paling cepat ditamba
- Cukup set URL + header + SEND
- Tidak perlu body

**PATCH Endpoints:**
- Sama seperti PUT tapi bedanya hanya update field yang diperlukan
- Urutan sama dengan PUT, tinggal ganti method jadi PATCH
- Copy dari PUT request, cukup ubah method-nya

---

**Urutan kerja yang efficient:**
1. Test semua 47 endpoint (DELETE/PUT/POST) → selesai hari 1
2. Tambah 26 endpoint baru → done hari 1 juga
3. Final export collection

**Total waktu estimate: 2 jam + 1.5 jam = 3.5 jam**

---

*Setelah semua ini selesai, Postman collection Anda akan 100% complete & documented ✅*
