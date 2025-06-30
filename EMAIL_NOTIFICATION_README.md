# Fitur Email Notification untuk Jadwal Baru

## Deskripsi
Fitur ini mengirimkan email notifikasi otomatis ke semua user dengan `user_type = 'kepala_tuk'` saat admin membuat jadwal ujian kompetensi baru.

## File yang Dibuat/Dimodifikasi

### 1. Mail Class
- **File**: `app/Mail/JadwalBaruMail.php`
- **Fungsi**: Class untuk mengirim email notifikasi jadwal baru

### 2. Email Template
- **File**: `resources/views/emails/jadwal-baru.blade.php`
- **Fungsi**: Template HTML email yang akan dikirim

### 3. Email Service
- **File**: `app/Services/EmailService.php`
- **Fungsi**: Service class untuk menangani logika pengiriman email

### 4. Controller Update
- **File**: `app/Http/Controllers/Admin/JadwalController.php`
- **Modifikasi**: Menambahkan pengiriman email di method `store()`

### 5. Test Command
- **File**: `app/Console/Commands/TestEmailCommand.php`
- **Fungsi**: Command untuk testing pengiriman email

### 6. Seeder
- **File**: `database/seeders/KepalaTukSeeder.php`
- **Fungsi**: Membuat user kepala TUK untuk testing

## Cara Penggunaan

### 1. Setup Mailtrap
Ikuti langkah-langkah di file `MAILTRAP_SETUP.md`

### 2. Jalankan Seeder
```bash
php artisan db:seed --class=KepalaTukSeeder
```

### 3. Test Pengiriman Email
```bash
# Test dengan jadwal pertama
php artisan email:test

# Test dengan jadwal spesifik
php artisan email:test --jadwal_id=1
```

### 4. Penggunaan Normal
Saat admin membuat jadwal baru melalui interface web, email akan otomatis terkirim ke semua kepala TUK.

## Struktur Email
Email yang dikirim berisi:
- Header dengan nama sistem
- Detail jadwal ujian (skema, TUK, tanggal, kuota, status)
- Instruksi untuk login ke sistem
- Footer dengan informasi copyright

## Konfigurasi Environment
Pastikan file `.env` berisi konfigurasi Mailtrap:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sistemujikom.com
MAIL_FROM_NAME="Sistem Ujian Kompetensi"
```

## Troubleshooting

### Email tidak terkirim
1. Periksa konfigurasi Mailtrap di `.env`
2. Pastikan ada user dengan `user_type = 'kepala_tuk'`
3. Cek log Laravel untuk error detail

### Template email tidak muncul
1. Pastikan file `resources/views/emails/jadwal-baru.blade.php` ada
2. Clear cache view: `php artisan view:clear`

### Command test tidak berjalan
1. Pastikan command terdaftar: `php artisan list | grep email`
2. Jika tidak ada, jalankan: `php artisan config:clear`

## Catatan Penting
- Fitur ini hanya mengirim email ke user dengan `user_type = 'kepala_tuk'`
- Email dikirim secara synchronous (bisa ditambahkan queue untuk production)
- Mailtrap hanya untuk development/testing
- Untuk production, gunakan service email yang sesuai 
