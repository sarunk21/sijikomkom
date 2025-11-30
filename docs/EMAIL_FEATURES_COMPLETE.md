# Fitur Email Notification Lengkap - Sistem Ujian Kompetensi

## Overview
Sistem ini memiliki 2 jenis email notification yang berjalan otomatis:

1. **Email Jadwal Baru** - Dikirim ke kepala TUK saat admin membuat jadwal
2. **Email Pendaftaran Ditolak** - Dikirim ke asesi saat kaprodi menolak pendaftaran

## File yang Dibuat

### Mail Classes
```
app/Mail/
├── JadwalBaruMail.php              # Email jadwal baru ke kepala TUK
└── PendaftaranDitolakMail.php      # Email pendaftaran ditolak ke asesi
```

### Email Templates
```
resources/views/emails/
├── jadwal-baru.blade.php           # Template email jadwal baru
└── pendaftaran-ditolak.blade.php   # Template email pendaftaran ditolak
```

### Services
```
app/Services/
└── EmailService.php                # Service untuk semua pengiriman email
```

### Controllers (Updated)
```
app/Http/Controllers/
├── Admin/JadwalController.php      # + Email saat create jadwal
└── Kaprodi/VerifikasiPendaftaranController.php  # + Email saat tolak pendaftaran
```

### Commands
```
app/Console/Commands/
├── TestEmailCommand.php            # Test email jadwal baru
└── TestPendaftaranDitolakCommand.php  # Test email pendaftaran ditolak
```

### Seeders
```
database/seeders/
├── KepalaTukSeeder.php            # User kepala TUK untuk testing
└── AsesiSeeder.php                # User asesi untuk testing
```

## Cara Setup dan Testing

### 1. Setup Mailtrap
```bash
# Ikuti panduan di MAILTRAP_SETUP.md
# Update file .env dengan kredensial Mailtrap
```

### 2. Jalankan Seeders
```bash
# Buat user kepala TUK
php artisan db:seed --class=KepalaTukSeeder

# Buat user asesi
php artisan db:seed --class=AsesiSeeder
```

### 3. Test Email Jadwal Baru
```bash
# Test dengan jadwal pertama
php artisan email:test

# Test dengan jadwal spesifik
php artisan email:test --jadwal_id=1
```

### 4. Test Email Pendaftaran Ditolak
```bash
# Test dengan pendaftaran pertama
php artisan email:test-pendaftaran-ditolak

# Test dengan pendaftaran spesifik
php artisan email:test-pendaftaran-ditolak --pendaftaran_id=1
```

## Detail Email Notification

### 1. Email Jadwal Baru
**Trigger**: Admin membuat jadwal baru
**Penerima**: Semua user dengan `user_type = 'kepala_tuk'`
**Isi Email**:
- Header biru dengan judul "Jadwal Ujian Kompetensi Baru"
- Detail jadwal (skema, TUK, tanggal, kuota, status)
- Instruksi untuk login dan konfirmasi
- Status: "Menunggu Konfirmasi Kepala TUK"

### 2. Email Pendaftaran Ditolak
**Trigger**: Kaprodi mengubah status pendaftaran menjadi "Ditolak" (status = 2)
**Penerima**: Asesi yang pendaftarannya ditolak
**Isi Email**:
- Header merah dengan judul "Pendaftaran Ujian Kompetensi Ditolak"
- Detail pendaftaran (nama, NIM, skema, TUK, tanggal)
- Alasan penolakan (jika ada)
- Langkah selanjutnya yang harus dilakukan
- Status: "DITOLAK"

## Konfigurasi .env
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
- **Status 2**: Ditolak (trigger email notification)
- **Status 3**: Menunggu verifikasi

## User Testing
Setelah menjalankan seeder, Anda akan memiliki:

### Kepala TUK
- Email: `kepalatuk@test.com`
- Password: `password`
- User Type: `kepala_tuk`

### Asesi
- Email: `asesi@test.com`
- Password: `password`
- User Type: `asesi`

## Workflow Email

### Jadwal Baru
1. Admin login ke sistem
2. Admin membuat jadwal baru
3. Sistem otomatis kirim email ke semua kepala TUK
4. Kepala TUK terima email di Mailtrap
5. Kepala TUK login untuk konfirmasi jadwal

### Pendaftaran Ditolak
1. Asesi daftar ujian kompetensi
2. Kaprodi review pendaftaran
3. Kaprodi tolak pendaftaran dengan alasan
4. Sistem otomatis kirim email ke asesi
5. Asesi terima email di Mailtrap
6. Asesi bisa perbaiki dan daftar ulang

## Troubleshooting

### Email tidak terkirim
1. Cek konfigurasi Mailtrap di `.env`
2. Pastikan ada user dengan tipe yang sesuai
3. Cek log Laravel: `tail -f storage/logs/laravel.log`

### Template tidak muncul
```bash
php artisan view:clear
php artisan config:clear
```

### Command tidak ditemukan
```bash
php artisan config:clear
php artisan list | grep email
```

## Production Notes
- Ganti Mailtrap dengan service email production (Gmail SMTP, SendGrid, Mailgun)
- Tambahkan queue untuk pengiriman email asynchronous
- Tambahkan retry mechanism untuk email yang gagal
- Monitor email delivery rate

## Security Considerations
- Email dikirim hanya ke user yang berhak
- Tidak ada informasi sensitif di email
- Email template tidak bisa diakses langsung dari web
- Logging untuk audit trail 
