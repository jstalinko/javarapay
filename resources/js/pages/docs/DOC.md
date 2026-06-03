## Pengenalan
JavaraPay adalah jembatan pembayaran digital (Payment Integrator) yang memudahkan merchant untuk menerima pembayaran dari pelanggan.

## Persiapan
Anda diharuskan untuk mendaftar ke JavaraPay dan mendaftarkan project anda terlebih dahulu.

### Project
Project adalah merchant anda yang terdaftar di JavaraPay, dengan mendaftar project anda nantinya akan mendapatkan API Key dan Secret Key yang digunakan untuk autentikasi saat menggunakan API JavaraPay.

### Integrasi

#### Integrasi via payment links
Buat tagihan atau pembayaran kepada client anda cukup dengan "Create payment links" di halaman dashboard JavaraPay.
- Klik "Create Payment Links"
- Masukkan jumlah nominal
- Masukkan "Notes" jika diperlukan. Lalu link pembayaran siap dibagikan kepada client anda.

[Note] Jika anda mengatur Webhook URL pada project anda, JavaraPay akan mengirimkan notifikasi ke Webhook URL anda saat pembayaran berhasil.

### Integrasi via API

#### API Base Endpoint
```
https://pay.javara.digital/api/
```

#### Headers Autentikasi
Untuk setiap permintaan API, sertakan header berikut:
- `X-JAVARAPAY-API`: API Key project Anda
- `X-JAVARAPAY-MERCHANT-CODE`: Merchant Code project Anda

---

#### 1. Mendapatkan Semua Channel Pembayaran
Mendapatkan semua channel pembayaran yang tersedia untuk project Anda.

- **Endpoint**: `/channel`
- **Method**: `GET`
- **Headers**:
  - `X-JAVARAPAY-API`
  - `X-JAVARAPAY-MERCHANT-CODE`

##### Response (200 OK)
```json
{
  "success": true,
  "data": [
    {
      "method_code": "qris",
      "method_name": "QRIS",
      "group": "E-Wallet",
      "image": "https://pay.javara.digital/images/channels/qris.png",
      "fee_percent": 0.7,
      "fee_flat": 0,
      "min_amount": 1000,
      "max_amount": 50000000
    }
  ]
}
```

---

#### 2. Membuat Transaksi
Buat transaksi baru melalui API untuk memproses pembayaran pelanggan.

- **Endpoint**: `/transaction/create`
- **Method**: `POST`
- **Headers**:
  - `X-JAVARAPAY-API`
  - `X-JAVARAPAY-MERCHANT-CODE`
  - `Content-Type: application/json`

##### Request Body (JSON)
```json
{
  "method_code": "qris",
  "merchant_ref": "INV-001",
  "amount": 10000,
  "customer_name": "Budi",
  "customer_email": "budi@example.com",
  "customer_phone": "08123456789",
  "notes": "Pembayaran invoice INV-001"
}
```

##### Response (200 OK)
```json
{
  "success": true,
  "message": "Transaction created successfully.",
  "data": {
    "txid": "JP12345678",
    "merchant_ref": "INV-001",
    "amount": 10000,
    "total_fee": 1000,
    "total_amount": 11000,
    "payment_method_code": "qris",
    "payment_method_name": "QRIS",
    "status": "UNPAID",
    "pay_url": "https://pay.javara.digital/payment/1/JP12345678",
    "pay_code": null,
    "qr_url": null,
    "reference": "REF987654321",
    "expired_at": 1780490400
  }
}
```

---

#### 3. Detail Transaksi
Mendapatkan detail status transaksi tertentu menggunakan `txid`.

- **Endpoint**: `/transaction/detail/{txid}`
- **Method**: `GET`
- **Headers**:
  - `X-JAVARAPAY-API`
  - `X-JAVARAPAY-MERCHANT-CODE`

##### Response (200 OK)
```json
{
  "success": true,
  "data": {
    "txid": "JP12345678",
    "merchant_ref": "INV-001",
    "amount": 10000,
    "total_fee": 1000,
    "total_amount": 11000,
    "payment_method_code": "qris",
    "payment_method_name": "QRIS",
    "notes": "Pembayaran invoice INV-001",
    "status": "UNPAID",
    "paid_at": null,
    "settled_at": null,
    "created_at": "2026-06-03T12:58:12.000000Z"
  }
}
```