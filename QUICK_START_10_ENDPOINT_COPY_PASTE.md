# ⚡ QUICK START: 10 ENDPOINT PERTAMA UNTUK DITAMBAHKAN KE POSTMAN

**Instruksi:** Copy-paste endpoint ini langsung ke Postman Collection  
**Waktu Estimasi:** ~2 jam untuk 10 endpoint  
**Format:** Siap copy ke Postman

---

## 🎯 ENDPOINT #1: DELETE /api/banks/{id}

```
HTTP Method: DELETE
URL: {{base_url}}/api/banks/1

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}",
  "Content-Type": "application/json"
}

Body: (empty)

Expected Response: 
{
  "message": "Bank deleted successfully"
}

Status: 200 OK atau 404 Not Found (jika ID tidak ada)
```

**Cara di Postman:**
1. Klik "New" → "Request"
2. Nama: "DELETE /api/banks/{id}"
3. Method: DELETE
4. URL: `{{base_url}}/api/banks/1`
5. Click "Send"
6. Click "Save Response"
7. Save ke collection folder "Delete Operations"

---

## 🎯 ENDPOINT #2: GET /api/banks

```
HTTP Method: GET
URL: {{base_url}}/api/banks

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}"
}

Query Parameters:
page=1&limit=10&search=&sort=

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "name": "BCA",
      "code": "014",
      "account_number": "1234567890",
      "is_active": 1
    }
  ],
  "total": 50,
  "per_page": 10,
  "current_page": 1
}
```

---

## 🎯 ENDPOINT #3: GET /api/banks/{id}

```
HTTP Method: GET
URL: {{base_url}}/api/banks/1

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}"
}

Response: 200 OK
{
  "data": {
    "id": 1,
    "name": "BCA",
    "code": "014",
    "account_number": "1234567890",
    "is_active": 1
  }
}
```

---

## 🎯 ENDPOINT #4: POST /api/banks

```
HTTP Method: POST
URL: {{base_url}}/api/banks

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}",
  "Content-Type": "application/json"
}

Request Body (JSON):
{
  "name": "BCA",
  "code": "014",
  "account_number": "0123456789"
}

Response: 201 Created
{
  "message": "Bank created successfully",
  "data": {
    "id": 51,
    "name": "BCA",
    "code": "014",
    "account_number": "0123456789",
    "is_active": 1
  }
}
```

---

## 🎯 ENDPOINT #5: PUT /api/banks/{id}

```
HTTP Method: PUT
URL: {{base_url}}/api/banks/1

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}",
  "Content-Type": "application/json"
}

Request Body (JSON):
{
  "name": "BCA UPDATE",
  "code": "014",
  "account_number": "9876543210"
}

Response: 200 OK
{
  "message": "Bank updated successfully",
  "data": {
    "id": 1,
    "name": "BCA UPDATE",
    "code": "014",
    "account_number": "9876543210",
    "is_active": 1
  }
}
```

---

## 🎯 ENDPOINT #6: DELETE /api/clients/{id}

```
HTTP Method: DELETE
URL: {{base_url}}/api/clients/1

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}",
  "Content-Type": "application/json"
}

Body: (empty)

Expected Response: 200 OK
```

---

## 🎯 ENDPOINT #7: GET /api/clients

```
HTTP Method: GET
URL: {{base_url}}/api/clients?page=1&limit=10

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}"
}

Response: 200 OK (list of clients)
```

---

## 🎯 ENDPOINT #8: GET /api/clients/{id}

```
HTTP Method: GET
URL: {{base_url}}/api/clients/1

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}"
}

Response: 200 OK (single client detail)
```

---

## 🎯 ENDPOINT #9: POST /api/clients

```
HTTP Method: POST
URL: {{base_url}}/api/clients

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}",
  "Content-Type": "application/json"
}

Request Body (JSON):
{
  "name": "PT. Maju Jaya Abadi",
  "email": "contact@majujaya.com",
  "phone": "021-123-4567",
  "address": "Jl. Sudirman No. 123 Jakarta"
}

Response: 201 Created
```

---

## 🎯 ENDPOINT #10: PUT /api/clients/{id}

```
HTTP Method: PUT
URL: {{base_url}}/api/clients/1

Headers:
{
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}",
  "Content-Type": "application/json"
}

Request Body (JSON):
{
  "name": "PT. Maju Jaya Abadi UPDATE",
  "email": "newemail@majujaya.com",
  "phone": "021-999-8888",
  "address": "Jl. Sudirman No. 456 Jakarta"
}

Response: 200 OK
```

---

## 📋 CHECKLIST: 10 ENDPOINT PERTAMA

```
[Masukkan ✅ atau ❌ setelah mengerjakan]

□ DELETE /api/banks/{id}         (Endpoint #1)
□ GET /api/banks                  (Endpoint #2)
□ GET /api/banks/{id}             (Endpoint #3)
□ POST /api/banks                 (Endpoint #4)
□ PUT /api/banks/{id}             (Endpoint #5)
□ DELETE /api/clients/{id}        (Endpoint #6)
□ GET /api/clients                (Endpoint #7)
□ GET /api/clients/{id}           (Endpoint #8)
□ POST /api/clients               (Endpoint #9)
□ PUT /api/clients/{id}           (Endpoint #10)

TOTAL COMPLETED: ___/10
```

---

## 🚀 STEPS UNTUK MENGERJAKAN:

### Step 1: Buka Postman Application

### Step 2: Buka Collection Yang Ada

```
File: FitArt_JPAS_API_Complete.postman_collection.json
```

### Step 3: Untuk Setiap Endpoint (#1-10):

```
a) Klik tombol "+"  untuk buat request baru
b) Copy HTTP Method dari section di atas (misal: DELETE)
c) Copy URL dari section di atas
d) Klik pada request baru
e) Pilih method yang sesuai
f) Paste URL
g) Jika ada Request Body - Klik "Body" tab → "raw" → Pilih "JSON" → Paste payload
h) Jika ada Headers yang custom - Klik "Headers" tab → Tambahkan header
i) Klik "Send"
j) Lihat Response di bagian bawah
k) Klik "Save Response"
l) Nama request sesuai endpoint (misal: "DELETE /api/banks/{id}")
m) Save ke collection dengan organizing dalam folder (misal: "Delete Operations")
```

### Step 4: Setelah 10 Endpoint Selesai:

```
a) Klik "File" → "Export"
b) Format: "Collection v2.1"
c) Filename: "FitArt_JPAS_API_Complete_UPDATED_171endpoints.json"
d) Save ke: c:\xampp\htdocs\fitart_project\
e) Report jumlah endpoint yang sudah ditambahkan
```

---

## 💡 TIPS:

1. **Gunakan Environment Variables:**
   - `{{base_url}}` = http://localhost:8000
   - `{{token}}` = Bearer token dari login

2. **Set Default Authorization:**
   - Klik Collection → Authorization
   - Tipe: Bearer Token
   - Token: `{{token}}`
   - Semua request akan inherit authorization ini

3. **Batch Create dengan Import:**
   - Export file untuk hanya 10 endpoint
   - Buat template di Excel/JSON
   - Import bulk ke Postman

4. **Test Response:**
   - Jangan khawatir jika ID tidak ada (404 OK)
   - Yang penting: Save response-nya
   - Postman automatically capture response

---

## ⏱️ WAKTU ESTIMASI:

```
Setup Environment & Collection: 5 menit
Per Endpoint (copy + paste + run + save): 8-10 menit

Jadi untuk 10 endpoint:
Setup: 5 menit
10 endpoint × 9 menit: 90 menit
Buffer: 5 menit
TOTAL: ~100 menit (1.5 jam)

Setelah 10 endpoint ini, pattern akan lebih cepat!
```

---

**Ready to start? Buka Postman sekarang dan mulai dengan endpoint #1! ✅**
