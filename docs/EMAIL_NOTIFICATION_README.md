# Fitur Email Notification untuk Sistem Ujian Kompetensi

## Deskripsi
Fitur ini mengirimkan email notifikasi otomatis dalam berbagai skenario sistem ujian kompetensi.

## Jenis Email Notification

### 1. Jadwal Baru
- **Trigger**: Admin membuat jadwal ujian kompetensi baru
- **Penerima**: Semua user dengan `user_type = 'kepala_tuk'`
- **Isi**: Detail jadwal ujian yang memerlukan konfirmasi

### 2. Pendaftaran Ditolak
- **Trigger**: Kaprodi menolak pendaftaran asesi
- **Penerima**: Asesi yang pendaftarannya ditolak
- **Isi**: Detail pendaftaran dan alasan penolakan

## File yang Dibuat/Dimodifikasi

### 1. Mail Classes
- **File**: `app/Mail/JadwalBaruMail.php`
- **Fungsi**: Class untuk mengirim email notifikasi jadwal baru
- **File**: `app/Mail/PendaftaranDitolakMail.php`
- **Fungsi**: Class untuk mengirim email notifikasi pendaftaran ditolak

### 2. Email Templates
- **File**: `resources/views/emails/jadwal-baru.blade.php`
- **Fungsi**: Template HTML email jadwal baru
- **File**: `resources/views/emails/pendaftaran-ditolak.blade.php`
- **Fungsi**: Template HTML email pendaftaran ditolak

### 3. Email Service
- **File**: `app/Services/EmailService.php`
- **Fungsi**: Service class untuk menangani semua logika pengiriman email

### 4. Controller Updates
- **File**: `app/Http/Controllers/Admin/JadwalController.php`
- **Modifikasi**: Menambahkan pengiriman email di method `store()`
- **File**: `app/Http/Controllers/Kaprodi/VerifikasiPendaftaranController.php`
- **Modifikasi**: Menambahkan pengiriman email di method `update()` saat status ditolak

### 5. Test Commands
- **File**: `app/Console/Commands/TestEmailCommand.php`
- **Fungsi**: Command untuk testing pengiriman email jadwal baru
- **File**: `app/Console/Commands/TestPendaftaranDitolakCommand.php`
- **Fungsi**: Command untuk testing pengiriman email pendaftaran ditolak

## Cara Penggunaan

### 1. Setup Mailtrap
Ikuti langkah-langkah di file `MAILTRAP_SETUP.md`

### 2. Test Pengiriman Email Jadwal Baru
```bash
# Test dengan jadwal pertama
php artisan email:test

# Test dengan jadwal spesifik
php artisan email:test --jadwal_id=1
```

### 3. Test Pengiriman Email Pendaftaran Ditolak
```bash
# Test dengan pendaftaran pertama
php artisan email:test-pendaftaran-ditolak

# Test dengan pendaftaran spesifik
php artisan email:test-pendaftaran-ditolak --pendaftaran_id=1
```

### 4. Penggunaan Normal
- **Jadwal Baru**: Saat admin membuat jadwal baru melalui interface web, email otomatis terkirim ke kepala TUK
- **Pendaftaran Ditolak**: Saat kaprodi menolak pendaftaran (status = 2), email otomatis terkirim ke asesi

## Struktur Email

### Email Jadwal Baru
- Header dengan nama sistem
- Detail jadwal ujian (skema, TUK, tanggal, kuota, status)
- Instruksi untuk login ke sistem
- Footer dengan informasi copyright

### Email Pendaftaran Ditolak
- Header dengan warna merah untuk menandakan penolakan
- Detail pendaftaran asesi
- Alasan penolakan (jika ada)
- Langkah selanjutnya yang harus dilakukan
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

## Status Pendaftaran
- **Status 1**: Diterima
- **Status 2**: Ditolak (akan mengirim email notifikasi)
- **Status 3**: Menunggu verifikasi

## Troubleshooting

### Email tidak terkirim
1. Periksa konfigurasi Mailtrap di `.env`
2. Pastikan ada user dengan tipe yang sesuai
3. Cek log Laravel untuk error detail

### Template email tidak muncul
1. Pastikan file template email ada di `resources/views/emails/`
2. Clear cache view: `php artisan view:clear`

### Command test tidak berjalan
1. Pastikan command terdaftar: `php artisan list | grep email`
2. Jika tidak ada, jalankan: `php artisan config:clear`

## Catatan Penting
- Email dikirim secara synchronous (bisa ditambahkan queue untuk production)
- Mailtrap hanya untuk development/testing
- Untuk production, gunakan service email yang sesuai
- Email pendaftaran ditolak hanya dikirim saat status berubah dari non-ditolak menjadi ditolak
