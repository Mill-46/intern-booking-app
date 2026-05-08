# Technical Test - Fullstack Developer (Intern)
## Aplikasi Pemesanan Kendaraan Operasional Tambang

Aplikasi ini dibuat untuk memenuhi brief technical test pemesanan kendaraan dengan alur persetujuan berjenjang, monitoring operasional kendaraan, dan pelaporan periodik.

## 1) Kesesuaian Dengan Brief

### Requirement Utama
- Terdapat 2 jenis user utama: `admin` dan `pihak yang menyetujui`.
  - Implementasi approver dipisah menjadi `approver_l1` dan `approver_l2` untuk memenuhi approval berjenjang minimal 2 level.
- Admin dapat:
  - Menginput pemesanan kendaraan
  - Menentukan driver
  - Menentukan approver level 1 dan level 2
- Persetujuan dilakukan berjenjang minimal 2 level:
  - Level 1 (`approver_l1`) -> Level 2 (`approver_l2`)
- Pihak yang menyetujui dapat melakukan approve/reject melalui aplikasi.
- Terdapat dashboard monitoring pemakaian kendaraan.
- Terdapat laporan periodik pemesanan kendaraan yang dapat di-export ke Excel (`.xlsx`).

### Nilai Tambahan (Instruction)
- Physical Data Model tersedia di: `docs/physical-data-model.md`
- Activity Diagram fitur booking tersedia di: `docs/booking-activity-diagram.md`
- Activity log untuk proses utama aplikasi tersedia di menu `Log Aktivitas`
- UI responsive untuk desktop dan mobile

## 2) Akun Login

| Role | Username (Email) | Password |
|---|---|---|
| Admin | `admin@example.com` | `password123` |
| Approver Level 1 | `approver1@example.com` | `password123` |
| Approver Level 2 | `approver2@example.com` | `password123` |

## 3) Versi Teknologi

- PHP: `8.4.1`
- Framework: `Laravel 13.8.0`
- Database (default local): `SQLite 3`
- Frontend: `Blade + Tailwind CSS v4`
- Testing: `Pest v4`
- Export Excel: `maatwebsite/excel`

## 4) Setup & Menjalankan Aplikasi

1. Install dependency
```bash
composer install
npm install
```

2. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

3. Siapkan database (SQLite default)
```bash
touch database/database.sqlite
php artisan migrate --seed --no-interaction
```

4. Build asset frontend
```bash
npm run build
```

5. Jalankan aplikasi
```bash
php artisan serve
```

Akses aplikasi di `http://127.0.0.1:8000`

## 5) Panduan Penggunaan Singkat

1. Login sebagai `admin@example.com`
2. Buka menu `Pemesanan` -> buat booking baru
3. Pilih kendaraan, driver, origin site, destination site, approver L1, approver L2
4. Submit booking
5. Login sebagai `approver1@example.com` -> approve/reject
6. Login sebagai `approver2@example.com` -> approve/reject
7. Kembali ke admin untuk konfirmasi dan penyelesaian booking
8. Lihat `Dashboard` untuk monitoring operasional
9. Export laporan periodik dari halaman `Pemesanan` (Excel)
10. Cek jejak proses di `Log Aktivitas`

## 6) Fitur Utama yang Tersedia

- CRUD master data kendaraan
- CRUD master data driver
- Booking kendaraan oleh admin
- Approval berjenjang L1 -> L2
- Monitoring pemakaian kendaraan (vehicle usage)
- Monitoring konsumsi BBM
- Monitoring dan jadwal servis kendaraan
- Export laporan booking ke Excel dengan filter periode
- Activity logging

## 7) Menjalankan Test

```bash
php artisan test --compact
```

Atau test minimal utama:

```bash
php artisan test --compact tests/Feature/BookingApprovalFlowTest.php tests/Feature/DashboardRenderTest.php
```
