# 🚀 Quick Setup Guide - Aplikasi Kos

## Langkah-langkah Instalasi Cepat

### 1. Persiapan Database

```bash
# Buat database baru di MySQL
CREATE DATABASE kos_app;
```

### 2. Install Dependencies

```bash
cd kos-app
composer install
npm install && npm run build
```

### 3. Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi .env

Edit file `.env` dan sesuaikan:

```env
APP_NAME="Aplikasi Kos"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kos_app
DB_USERNAME=root
DB_PASSWORD=your_password

FILESYSTEM_DISK=public
```

### 5. Migrasi Database

```bash
# Jalankan migrasi
php artisan migrate

# Link storage untuk file uploads
php artisan storage:link
```

### 6. Seed Data Demo (Opsional)

```bash
php artisan db:seed --class=KosDataSeeder
```

Data demo yang dibuat:

-   1 Admin user (admin@kos.com / password)
-   6 Kamar (berbagai tipe dan status)
-   3 Penyewa aktif
-   3 Pembayaran (2 confirmed, 1 pending)
-   4 Biaya operasional
-   1 Laporan keuangan bulan lalu

### 7. Buat Admin User (Jika tidak seed)

```bash
php artisan make:filament-user
```

Ikuti prompt untuk membuat user admin.

### 8. Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di: **http://localhost:8000/admin**

## ✅ Verifikasi Instalasi

Setelah login, pastikan menu berikut tersedia:

-   ✅ Penyewa
-   ✅ Kamar Kos
-   ✅ Pembayaran
-   ✅ Biaya Operasional
-   ✅ Laporan Keuangan

## 🎯 Next Steps

### Tambah Penyewa Pertama

1. Klik "Penyewa" → "Tambah Penyewa"
2. Isi data pribadi lengkap
3. Upload foto KTP di tab "Foto KTP & Dokumen"
4. Simpan

### Tambah Kamar

1. Klik "Kamar Kos" → "Tambah Kamar"
2. Masukkan nomor kamar dan tipe
3. Set harga sewa per bulan
4. Simpan

### Catat Pembayaran

1. Klik "Pembayaran" → "Tambah Pembayaran"
2. Pilih penyewa dan kamar
3. Harga otomatis terisi
4. Upload bukti bayar
5. Konfirmasi pembayaran

### Input Biaya Operasional

1. Klik "Biaya Operasional" → "Tambah Biaya"
2. Pilih kategori dan masukkan jumlah
3. Upload kwitansi
4. Approve biaya

### Generate Laporan

1. Klik "Laporan Keuangan" → "Tambah Laporan"
2. Pilih periode
3. Data otomatis dihitung
4. Publikasi laporan

## 🔧 Troubleshooting

### Storage Link Error

```bash
# Jika ada error storage link
rm public/storage
php artisan storage:link
```

### Permission Error (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Migration Error

```bash
# Reset database (HATI-HATI: akan hapus semua data)
php artisan migrate:fresh --seed
```

### Cache Issues

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## 📚 Dokumentasi Lengkap

Baca dokumentasi lengkap di: `APLIKASI_KOS_README.md`

## 🆘 Support

Jika ada masalah:

1. Cek log di `storage/logs/laravel.log`
2. Pastikan semua requirements terpenuhi (PHP 8.1+, MySQL)
3. Cek file permissions
4. Restart server development

## 🎉 Selamat!

Aplikasi kos Anda siap digunakan!
