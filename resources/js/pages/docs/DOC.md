## Pengenalan
JavaraPay adalah jembatan pembayaran digital ( Payment Integrator ) yang memudahkan merchant untuk menerima pembayaran dari pelanggan. 

## Persiapan
Anda di haruskan untuk mendaftar ke JavaraPay dan mendaftarkan project anda terlebih dahulu.

### Project
Project adalah merchant anda yang terdaftar di JavaraPay, dengan mendaftar project anda nantinya akan mendapatkan API Key dan Secret Key yang digunakan untuk autentikasi saat menggunakan API JavaraPay.


### Integrasi

#### Integrasi via payment links.
Buat tagihan atau pembayaran kepada client anda cukup dengan "Create payment links" di halaman dashboard javaraPay. 
- Click "Create Payment Links"
- Masukan jumlah nominal
- Masukan "Notes" jika di perlukan. lalu link pembayaran siap di bagikan kepada client anda.

[Note] Jika anda mengatur Webhook URL pada project anda, JavaraPay akan mengirimkan notifikasi ke Webhook URL anda saat pembayaran berhasil.


### Integrasi via API.

#### API Endpoint

https://pay.javara.digital/api/

#### Mendapatkan semua channel pembayaran ( payment methods )
API : https://pay.javara.digital/api/channel
method : GET 
headers: X-JAVARAPAY-API, X-JAVARAPAY-MERCHANT-CODE


#### Membuat transaksi
API : https://pay.javara.digital/api/transaction/create
Method: POST
Headers: X-JAVARAPAY-API, X-JAVARAPAY-MERCHANT-CODE
Body: 
{
    "method_code": "qris",
    "merchant_ref": "INV-001",
    "amount": 10000,
    "customer_name": "Budi",
    "customer_email": "[EMAIL_ADDRESS]",
    "customer_phone": "08123456789",
    "notes": "Pembayaran invoice INV-001",
}
