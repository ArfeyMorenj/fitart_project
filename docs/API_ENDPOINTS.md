# API Endpoints - Fitart Project

Dokumentasi API untuk backend PT. Fit Art Technology  
Digunakan oleh tim Front-end & QA Testing

---

## 📌 RESPONSE FORMAT STANDARD

### Success Response Format
```json
{
  "success": true,
  "message": "Success message here",
  "data": {}
}

{
  "success": false,
  "message": "Error message here",
  "errors": {}
}

AUTHENTICATION (Sanctum Token)
Method	    Endpoint	    Description	                Auth

POST	    /api/login	    Login user (get token)      ❌
POST	    /api/logout	    Logout	                    ✔
GET	        /api/me	        Current user profile	    ✔

Login Request
{
  "email": "admin@fitart.co.id",
  "password": "admin123"
}

Response Token
{
  "success": true,
  "token": "xxxxx",
  "token_type": "Bearer"
}
```

Authorization Header untuk semua API lain

Authorization: Bearer {token}

- CLIENTS

Method	    Endpoint	        Description	            Auth

GET	        /api/clients	    List semua clients	    ✔
POST	    /api/clients	    Create client	        ✔
GET	        /api/clients/{id}	Detail client	        ✔
PUT	        /api/clients/{id}	Update client	        ✔
DELETE	    /api/clients/{id}	Delete client	        ✔

- PRODUCTS

Method	    Endpoint

GET	        /api/products
POST	    /api/products
GET	        /api/products/{id}
PUT	        /api/products/{id}
DELETE	    /api/products/{id}

- INVOICES

Method	    Endpoint	                    Description

GET	        /api/invoices	                List invoices
POST	    /api/invoices	                Create invoice
GET	        /api/invoices/{id}	            Detail invoice
PUT	        /api/invoices/{id}	            Update
DELETE	    /api/invoices/{id}	            Delete
POST	    /api/invoices/{id}/calculate	Hitung total invoice
POST	    /api/invoices/{id}/post	        Posting ke jurnal

- PAYMENTS

Method	        Endpoint

GET	            /api/payments
POST	        /api/payments
GET	            /api/payments/{id}
PUT	            /api/payments/{id}
DELETE	        /api/payments/{id}

- JOURNAL ENTRIES

Method	    Endpoint

GET	        /api/journals
GET	        /api/journals/{id}

- RECEIVABLES

Method	     Endpoint	                    Description

GET	         /api/receivables	            Semua piutang
GET	         /api/receivables/{period}	    Piutang per periode

- PROTECTION PERIODS

Method	      Endpoint

GET	          /api/protections
POST	      /api/protections
PUT	          /api/protections/{id}/unlock

- SETTINGS

Method	    Endpoint

GET	        /api/settings
PUT	        /api/settings/{key}

- Default Admin Accounts (Setelah Seed)

Username	       Password	        Role

admin	           admin123	        admin
manager	           manager123	    manager
user	           user123	        user