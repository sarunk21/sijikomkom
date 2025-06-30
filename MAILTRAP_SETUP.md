# Setup Mailtrap untuk Pengiriman Email

## Langkah-langkah Setup Mailtrap

### 1. Daftar di Mailtrap
- Kunjungi https://mailtrap.io
- Daftar akun gratis
- Login ke dashboard

### 2. Buat Inbox Baru
- Klik "Add Inbox"
- Beri nama "Sistem Ujian Kompetensi"
- Pilih "Development" sebagai environment

### 3. Dapatkan Kredensial SMTP
- Klik pada inbox yang baru dibuat
- Pilih tab "SMTP Settings"
- Pilih "Laravel" dari dropdown
- Copy kredensial yang diberikan

### 4. Update File .env
Tambahkan konfigurasi berikut ke file `.env`:

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

### 5. Test Pengiriman Email
Setelah setup selesai, email akan otomatis terkirim ke Mailtrap saat:
- Admin membuat jadwal baru
- User dengan `user_type = 'kepala_tuk'` akan menerima notifikasi

### 6. Melihat Email di Mailtrap
- Login ke dashboard Mailtrap
- Klik pada inbox "Sistem Ujian Kompetensi"
- Email yang dikirim akan muncul di sana
- Klik pada email untuk melihat isinya

## Catatan Penting
- Mailtrap hanya untuk development/testing
- Untuk production, gunakan service email seperti Gmail SMTP, SendGrid, atau Mailgun
- Pastikan ada user dengan `user_type = 'kepala_tuk'` di database untuk menerima email 
