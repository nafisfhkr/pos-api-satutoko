# POS API (Laravel)

Backend API untuk POS sederhana dengan flow transaksi DRAFT -> PAID, stok konsisten, dan struktur kode yang rapi.

## Stack
- Laravel 12 + Sanctum
- MySQL/PostgreSQL

## Setup singkat
```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Auth
Semua endpoint outlet wajib `auth:sanctum`.

### Login dan generate token
POST `/api/v1/auth/login`
```json
{
  "email": "owner@example.com",
  "password": "password"
}
```
Response:
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "plain-text-token",
    "user": {
      "id": 1,
      "name": "Owner",
      "email": "owner@example.com"
    }
  }
}
```

Gunakan token pada header:
```
Authorization: Bearer plain-text-token
```

### Logout
POST `/api/v1/auth/logout`

## Base URL
`/api/v1`

## Outlet Scope
Semua resource transaksi dan stok di-scope ke outlet:
`/outlets/{outletId}/...`

## Endpoints (MVP)

### Stok
GET `/outlets/{outletId}/stocks`

POST `/outlets/{outletId}/stocks/adjust`
```json
{
  "product_id": 1,
  "qty": 5,
  "reason": "stock opname"
}
```

### Sales (DRAFT -> PAID)
POST `/outlets/{outletId}/sales`
```json
{
  "note": "penjualan pagi"
}
```

POST `/outlets/{outletId}/sales/{saleId}/items`
```json
{
  "product_id": 1,
  "qty": 2,
  "unit_price": 15000
}
```

GET `/outlets/{outletId}/sales/{saleId}`

### Payment
POST `/outlets/{outletId}/sales/{saleId}/pay/cash`
```json
{
  "cash_received": 50000
}
```
Response (contoh):
```json
{
  "success": true,
  "message": "Pembayaran cash berhasil",
  "data": {
    "payment": {
      "id": 1,
      "method": "cash",
      "amount": "30000.00",
      "cash_received": "50000.00",
      "change_amount": "20000.00"
    },
    "sale": {
      "id": 1,
      "status": "PAID",
      "total": "30000.00"
    }
  }
}
```

POST `/outlets/{outletId}/sales/{saleId}/pay/qris`
Headers:
```
Idempotency-Key: unique-key-123
```
Body:
```json
{
  "reference_no": "QRIS-REF-001"
}
```

### Shifts (stub)
POST `/outlets/{outletId}/shifts/open`

POST `/outlets/{outletId}/shifts/{shiftId}/close`

### Reports (stub)
GET `/outlets/{outletId}/reports/sales-summary`

## Catatan Teknis
- Semua pembayaran berjalan dalam DB transaction dan lock stok (`SELECT ... FOR UPDATE`).
- Stok berkurang otomatis saat pembayaran sukses.
- Setiap perubahan stok dicatat pada `inventory_movements`.
- Idempotency untuk QRIS via header `Idempotency-Key`.

## Struktur Folder Utama
```
app/Http/Controllers/Api/V1
app/Http/Controllers/Api/V1/Outlet
app/Http/Middleware/EnsureOutletAccess.php
app/Http/Requests
app/Services/Pos
app/Models
```
