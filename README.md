# POS API (Laravel)

![Laravel 12](https://img.shields.io/badge/Laravel-12-red)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777bb4)
Backend API untuk sistem POS berbasis Laravel dengan fokus pada arsitektur rapi, transaksi aman, dan konsistensi stok.

## Features & Status
- ✅ Auth (login/logout)
- ✅ Stocks (list & adjust)
- ✅ Sales (DRAFT -> PAID)
- ✅ Payments (Cash & QRIS + idempotency)
- 🚧 Shifts (stub / planned)
- 🚧 Reports (stub / planned)

## Tech Stack & Requirements
- Laravel 12 + Sanctum
- PHP >= 8.2
- Database: MySQL / PostgreSQL (SQLite juga bisa untuk demo)

## Quick Start
```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Demo Flow (End-to-End)
1) Login untuk mendapatkan token
2) Adjust stok produk
3) Buat sale (DRAFT)
4) Tambahkan item ke sale
5) Lakukan pembayaran (cash / QRIS)

## Auth
Semua endpoint outlet wajib `auth:sanctum`.

### Login dan generate token
POST `/api/v1/auth/login`
```
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"owner@example.com","password":"password"}'
```
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
Default: `http://127.0.0.1:8000`  
API prefix: `{{base_url}}/api/v1`

## Outlet Scope
Semua resource transaksi dan stok di-scope ke outlet:
`{{base_url}}/api/v1/outlets/{outletId}/...`

## Endpoints (MVP) - Ringkas

### Stok
GET `{{base_url}}/api/v1/outlets/{outletId}/stocks`

POST `{{base_url}}/api/v1/outlets/{outletId}/stocks/adjust`

### Sales (DRAFT -> PAID)
POST `{{base_url}}/api/v1/outlets/{outletId}/sales`

POST `{{base_url}}/api/v1/outlets/{outletId}/sales/{saleId}/items`

GET `{{base_url}}/api/v1/outlets/{outletId}/sales/{saleId}`

### Payment
POST `{{base_url}}/api/v1/outlets/{outletId}/sales/{saleId}/pay/cash`
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

POST `{{base_url}}/api/v1/outlets/{outletId}/sales/{saleId}/pay/qris`
Headers:
```
Idempotency-Key: unique-key-123
```

### Shifts (stub)
POST `{{base_url}}/api/v1/outlets/{outletId}/shifts/open`

POST `{{base_url}}/api/v1/outlets/{outletId}/shifts/{shiftId}/close`

### Reports (stub)
GET `{{base_url}}/api/v1/outlets/{outletId}/reports/sales-summary`

## Testing with Postman
1) Import collection: `postman/pos-api.postman_collection.json`
2) Atur variable: `base_url`, `token`, `outlet_id`
3) Urutan test: Auth -> Stocks -> Sales -> Payments

## Project Structure (Overview)
- Controller tipis untuk validasi dan response.
- Business logic ada di `app/Services/Pos`.
- Middleware `EnsureOutletAccess` untuk outlet scoping.

## Notes for Reviewers
- Payment memakai DB transaction + row locking untuk mencegah oversell.
- Stok berkurang atomik dan tercatat di `inventory_movements`.
- Idempotency QRIS via header `Idempotency-Key`.
- Shifts & Reports masih stub sebagai placeholder scope berikutnya.

## Struktur Folder Utama
```
app/Http/Controllers/Api/V1
app/Http/Controllers/Api/V1/Outlet
app/Http/Middleware/EnsureOutletAccess.php
app/Http/Requests
app/Services/Pos
app/Models
```
