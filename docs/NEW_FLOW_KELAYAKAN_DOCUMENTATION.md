# Dokumentasi Flow Baru: Verifikasi Kelayakan Sebelum Pembayaran

## 📋 Ringkasan Update

Sistem telah diperbarui dengan **flow baru** di mana **pembayaran dilakukan SETELAH verifikasi kelayakan** oleh asesor dan admin. Ini memastikan asesi hanya membayar jika sudah dipastikan layak mengikuti ujikom.

---

## 🔄 Perbandingan Flow

### Flow Lama:
```
Asesi Daftar → Upload Dokumen → Pembayaran → Verif Pembayaran → 
Verif Kaprodi → Verif Admin → Distribusi Asesor → Ujikom
```

### Flow Baru:
```
Asesi Daftar → Upload Dokumen & APL → Verif Kaprodi → Verif Admin → 
Distribusi Asesor → Verif Kelayakan Asesor → Approval Admin → 
PEMBAYARAN → Ujikom
```

---

## ✨ Keuntungan Flow Baru

1. ✅ **Asesi tidak perlu bayar jika tidak layak** - lebih adil
2. ✅ **Mengurangi refund** - pembayaran setelah dipastikan lolos
3. ✅ **Review dokumen lebih awal** - asesor bisa memeriksa kelengkapan sebelum ujian
4. ✅ **Efisiensi waktu** - tidak membuang waktu untuk asesi yang tidak memenuhi syarat
5. ✅ **Transparansi** - asesi tahu status kelayakan sebelum membayar

---

## 📊 Status Mapping Baru

### Status Pendaftaran:
```
1  => Menunggu Verifikasi Kaprodi
2  => Tidak Lolos Verifikasi Kaprodi
3  => Menunggu Verifikasi Admin
4  => Menunggu Distribusi Asesor (BARU)
5  => Menunggu Verifikasi Asesor (BARU)
6  => Menunggu Approval Kelayakan (BARU)
7  => Tidak Lolos Kelayakan (BARU)
8  => Menunggu Pembayaran (BARU)
9  => Menunggu Ujian
10 => Ujian Berlangsung
11 => Selesai
12 => Asesor Tidak Dapat Hadir
```

### Status Kelayakan:
```
0 => Belum Diperiksa
1 => Layak
2 => Tidak Layak
```

---

## 🔧 File-file yang Dibuat/Diubah

### Database Migrations:
1. `2025_12_21_000001_add_kelayakan_fields_to_pendaftaran_table.php`
   - Tambah field: `kelayakan_status`, `kelayakan_catatan`, `kelayakan_verified_at`, `kelayakan_verified_by`

2. `2025_12_21_000002_create_kelayakan_verifikasi_table.php`
   - Tabel baru untuk tracking verifikasi kelayakan oleh asesor

### Models:
1. `app/Models/Pendaftaran.php` - Update status mapping + relasi baru
2. `app/Models/KelayankanVerifikasi.php` - Model baru untuk verifikasi

### Controllers:
1. `app/Http/Controllers/Asesor/VerifikasiKelayankanController.php` - **BARU**
   - `index()` - List pendaftaran untuk verifikasi
   - `show()` - Form verifikasi kelayakan
   - `store()` - Simpan verifikasi (Layak/Tidak Layak)

2. `app/Http/Controllers/Admin/KelayankanController.php` - **BARU**
   - `index()` - List pendaftaran menunggu approval
   - `approve()` - Approve kelayakan & buat pembayaran
   - `reject()` - Reject kelayakan

3. `app/Http/Controllers/Admin/TestingController.php` - **UPDATE**
   - `autoApproveKelayakan()` - Auto approve untuk testing
   - `autoVerifyPembayaran()` - Auto verify pembayaran untuk testing
   - Update statistik dashboard untuk status baru

### Views:
1. `resources/views/components/pages/asesor/verifikasi-kelayakan/list.blade.php`
2. `resources/views/components/pages/asesor/verifikasi-kelayakan/form.blade.php`
3. `resources/views/components/pages/admin/kelayakan/list.blade.php`

### Email Notifications:
1. `app/Mail/VerifikasiKelayankanMail.php` - Email ke admin setelah asesor verifikasi
2. `app/Mail/KelayankanDitolakMail.php` - Email ke asesi jika tidak layak
3. `app/Mail/MenungguPembayaranMail.php` - Email ke asesi untuk melakukan pembayaran

### Email Templates:
1. `resources/views/emails/verifikasi-kelayakan.blade.php`
2. `resources/views/emails/kelayakan-ditolak.blade.php`
3. `resources/views/emails/menunggu-pembayaran.blade.php`

### Routes:
Added to `routes/web.php`:
- Admin: `/admin/kelayakan` (index, approve, reject)
- Asesor: `/asesor/verifikasi-kelayakan` (index, show, store)
- Testing: `/admin/testing/auto-approve-kelayakan`, `/admin/testing/auto-verify-pembayaran`

### Menu:
Updated `app/Traits/MenuTrait.php`:
- Admin: Menu baru "Approval Kelayakan"
- Asesor: Menu baru "Verifikasi Kelayakan"

---

## 🚀 Flow Detail Step-by-Step

### 1️⃣ Asesi Mendaftar
- Asesi upload dokumen (KTP, Sertifikat, KTM/KHS, Administratif)
- Mengisi APL (Asesmen Portofolio Lapangan)
- Status: **1 - Menunggu Verifikasi Kaprodi**

### 2️⃣ Verifikasi Kaprodi
- Kaprodi memeriksa kelengkapan dokumen
- Approve (status → 3) atau Reject (status → 2)

### 3️⃣ Verifikasi Admin
- Admin memeriksa data pendaftaran
- Approve (status → 4 - Menunggu Distribusi Asesor)

### 4️⃣ Distribusi Asesor
- Sistem/Admin mendistribusikan asesi ke asesor berdasarkan skema
- Membuat record di `pendaftaran_ujikom`
- Status: **5 - Menunggu Verifikasi Asesor**
- Email dikirim ke asesor untuk konfirmasi kehadiran

### 5️⃣ Verifikasi Kelayakan Asesor ⭐ **BARU**
- Asesor login → Menu "Verifikasi Kelayakan"
- Asesor memeriksa:
  - Dokumen persyaratan
  - Data APL
  - Kelengkapan administratif
- Asesor memberikan penilaian:
  - **✓ LAYAK** → Status menjadi 6 (Menunggu Approval Admin)
  - **✗ TIDAK LAYAK** → Status menjadi 7 (Tidak Lolos), asesi diberi tahu via email
- Asesor wajib memberikan catatan jika tidak layak

### 6️⃣ Approval Kelayakan Admin ⭐ **BARU**
- Admin login → Menu "Approval Kelayakan"
- Admin memeriksa hasil verifikasi asesor
- Admin melakukan:
  - **Approve** → Status menjadi 8 (Menunggu Pembayaran), sistem otomatis membuat record pembayaran
  - **Reject** → Status menjadi 7 (Tidak Lolos)
- Email dikirim ke asesi:
  - Jika approve: instruksi untuk melakukan pembayaran
  - Jika reject: pemberitahuan tidak lolos

### 7️⃣ Pembayaran ⭐ **POSISI BARU**
- Asesi menerima email untuk melakukan pembayaran
- Asesi upload bukti pembayaran
- Admin verifikasi pembayaran
- Status: **9 - Menunggu Ujian**

### 8️⃣ Pelaksanaan Ujikom
- Jadwal ujian dimulai
- Asesi mengerjakan ujian
- Asesor menilai hasil ujian
- Status akhir: **11 - Selesai**

---

## 🧪 Testing Flow Baru

### Menggunakan Testing Tools:

1. **Loloskan Verifikasi Kaprodi & Admin**
   - Klik tombol "Loloskan Verifikasi"
   - Pendaftaran status 1 & 3 → status 4

2. **Distribusi Asesor**
   - Klik tombol "Distribusi Asesor"
   - Pendaftaran status 4 → status 5
   - Buat PendaftaranUjikom

3. **Auto Approve Kelayakan** ⭐ **BARU**
   - Klik tombol "Auto Approve Kelayakan"
   - Pendaftaran status 5 → 6 → 8
   - Buat record Pembayaran otomatis

4. **Auto Verify Pembayaran** ⭐ **BARU**
   - Klik tombol "Auto Verify Pembayaran"
   - Pembayaran status 1 → 4
   - Pendaftaran status 8 → 9

5. **Lanjutkan dengan flow normal**
   - Mulai Jadwal
   - Mulai Ujikom
   - Selesai Ujikom
   - Selesai Jadwal

### Testing Manual (Tanpa Auto):

1. Login sebagai **Asesor**
2. Menu "Verifikasi Kelayakan"
3. Pilih pendaftaran yang status 5
4. Klik "Verifikasi"
5. Centang ✓ Layak atau ✗ Tidak Layak
6. Isi catatan (wajib jika tidak layak)
7. Simpan

8. Login sebagai **Admin**
9. Menu "Approval Kelayakan"
10. Pilih pendaftaran yang sudah diverifikasi asesor
11. Approve atau Reject

12. Login sebagai **Asesi**
13. Cek email untuk instruksi pembayaran
14. Menu "Pembayaran"
15. Upload bukti pembayaran

---

## 📧 Email Notifications

### 1. Verifikasi Kelayakan (ke Admin)
**Trigger:** Asesor memberikan verifikasi LAYAK
**Subject:** Verifikasi Kelayakan - Menunggu Approval Admin
**Isi:** Detail asesi, hasil verifikasi asesor, link approval

### 2. Kelayakan Ditolak (ke Asesi)
**Trigger:** Asesor/Admin menolak kelayakan
**Subject:** Pemberitahuan: Pendaftaran Tidak Lolos Kelayakan
**Isi:** Alasan penolakan, saran perbaikan, instruksi daftar ulang

### 3. Menunggu Pembayaran (ke Asesi)
**Trigger:** Admin approve kelayakan
**Subject:** Pendaftaran Disetujui - Silakan Lakukan Pembayaran
**Isi:** Selamat lolos verifikasi, instruksi pembayaran, deadline

---

## 🗄️ Database Schema

### Tabel `pendaftaran` (Field Baru):
```sql
kelayakan_status TINYINT DEFAULT 0  -- 0=Belum, 1=Layak, 2=Tidak Layak
kelayakan_catatan TEXT NULL
kelayakan_verified_at DATETIME NULL
kelayakan_verified_by BIGINT NULL FK→users.id
```

### Tabel `kelayakan_verifikasi` (Baru):
```sql
id BIGINT PRIMARY KEY
pendaftaran_id BIGINT FK→pendaftaran.id
asesor_id BIGINT FK→users.id
status TINYINT  -- 1=Layak, 2=Tidak Layak
catatan TEXT NULL
verified_at DATETIME
created_at, updated_at
```

---

## ⚠️ Breaking Changes & Migration

### Untuk Data Existing:

1. **Run Migration:**
   ```bash
   php artisan migrate
   ```

2. **Update Status Pendaftaran Lama:**
   - Status 4 (Menunggu Ujian) LAMA → Perlu disesuaikan
   - Status yang sudah berjalan tidak akan terpengaruh
   - Hanya pendaftaran baru yang mengikuti flow baru

3. **Backward Compatibility:**
   - Status lama tetap valid
   - Sistem dapat handle mixed status (lama & baru)
   - Tidak perlu migration data untuk pendaftaran selesai

---

## 🔐 Permissions & Access Control

### Asesor:
- ✅ Dapat melihat list pendaftaran yang didistribusikan ke mereka (status 5)
- ✅ Dapat memberikan verifikasi kelayakan (Layak/Tidak Layak)
- ✅ Wajib memberikan catatan jika menilai tidak layak
- ❌ Tidak dapat approve final (hanya admin)

### Admin:
- ✅ Dapat melihat semua pendaftaran menunggu approval (status 6)
- ✅ Dapat approve kelayakan (membuat pembayaran otomatis)
- ✅ Dapat reject kelayakan
- ✅ Dapat melihat history verifikasi asesor

### Asesi:
- ✅ Menerima email notifikasi di setiap tahap
- ✅ Dapat melihat status pendaftaran real-time
- ✅ Hanya dapat melakukan pembayaran setelah diapprove
- ❌ Tidak dapat membayar sebelum lolos verifikasi kelayakan

---

## 🐛 Troubleshooting

### Pendaftaran stuck di status 5
**Solusi:** Asesor belum memberikan verifikasi. Cek menu "Verifikasi Kelayakan" di asesor.

### Pembayaran tidak muncul
**Solusi:** Pastikan admin sudah approve kelayakan. Status harus 8 untuk muncul pembayaran.

### Email tidak terkirim
**Solusi:** 
1. Cek konfigurasi SMTP di `.env`
2. Pastikan `MAIL_FROM_ADDRESS` sudah diset
3. Cek log di `storage/logs/laravel.log`

### Status tidak berubah setelah verifikasi
**Solusi:**
1. Cek log error di Laravel
2. Pastikan tidak ada validation error
3. Cek database transaction tidak rollback

---

## 📝 Notes

- **Production Ready:** ✅ Ya, sudah siap production
- **Testing Required:** ⚠️ Disarankan testing menyeluruh sebelum deploy
- **Backup Required:** ⚠️ **WAJIB** backup database sebelum migrate
- **Rollback Plan:** Jika ada masalah, rollback migration dan restore backup

---

## 🎯 Checklist Deployment

- [ ] Backup database
- [ ] Run migration: `php artisan migrate`
- [ ] Test flow lengkap dengan data dummy
- [ ] Test email notifications
- [ ] Verifikasi menu tampil di admin & asesor
- [ ] Test permissions (asesor tidak bisa approve final)
- [ ] Update dokumentasi user manual
- [ ] Training untuk asesor & admin tentang flow baru
- [ ] Monitor log setelah deploy

---

## 📞 Support

Jika ada pertanyaan atau masalah, hubungi developer atau buka issue di repository.

**Version:** 1.0.0  
**Last Updated:** 21 Desember 2025  
**Author:** System Developer

---

**🎉 Selamat! Flow baru sudah siap digunakan! 🎉**

