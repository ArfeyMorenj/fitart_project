# Database Schema - Fitart Project

## Tables Overview
1. companies - Data perusahaan
2. users - User & authentication
3. clients - Data klien
4. invoices - Invoice license & non-license
5. ... (list semua tabel)

## Table Details

### clients
| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| code | varchar(20) | NO | Kode klien (unique) |
| name | varchar(255) | NO | Nama klien |
| ... | ... | ... | ... |

**Relationships:**
- Has Many: invoices, work_orders, payments
- Has Many Through: receivables

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (code)

---

### invoices
... (detail untuk setiap tabel)