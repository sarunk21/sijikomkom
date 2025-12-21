# NEW REGISTRATION FLOW (Unified Flow - UPDATED)

## 📋 Overview

Flow pendaftaran baru yang **unified** tanpa membedakan pendaftaran pertama atau kedua. Semua pendaftaran mengikuti flow yang sama dengan:
- **Distribusi asesor otomatis** terjadi lebih dulu
- **Verifikasi dokumen** oleh Kaprodi dan Admin dilakukan **setelah distribusi**
- **Pembayaran di akhir** setelah kelayakan disetujui

⚠️ **IMPORTANT**: Dokumentasi lengkap ada di `docs/NEW_FLOW_AFTER_DISTRIBUTION.md`

## 🎯 Flow Lengkap (Summary)

### 1. **Asesi Mendaftar + Upload Dokumen**
- **Route**: `POST /asesi/daftar-ujikom`
- **Controller**: `DaftarUjikomController@store`
- **Action**: 
  - Upload dokumen (KTP, Sertifikat, KTM/KHS, Administrasi)
  - **Langsung buat Pendaftaran** dengan status 1
  - **TIDAK buat Pembayaran di awal**
- **Status Pendaftaran**: 1 (Menunggu Distribusi Asesor)
- **Redirect**: Halaman Sertifikasi untuk isi APL
- **Note**: Asesi sudah bisa langsung isi APL 1 dan APL 2

### 2. **Distribusi Asesor (Otomatis/Manual)**
- **Scheduler**: `php artisan ujikom:distribute` (otomatis via cron)
- **Manual**: Testing Tool `/admin/testing` → "Trigger Distribusi Asesor"
- **Status**: 1 → 5 (Menunggu Verifikasi Dokumen)
- **Action**: Email notifikasi ke asesor untuk konfirmasi kehadiran

### 3. **Verifikasi Dokumen Administratif (Kaprodi & Admin - Gabungan)**
- **Route Kaprodi**: `/kaprodi/verifikasi-pendaftaran`
- **Route Admin**: `/admin/verifikasi-pendaftaran`
- **Status**: 5 → 6 (Approve) atau 5 → 2 (Reject)
- **Note**: Satu halaman untuk Kaprodi dan Admin

### 4. **Verifikasi Kelayakan oleh Asesor**
- **Route**: `/asesor/verifikasi-kelayakan/{pendaftaranId}`
- **Status**: 6 → 8 (Layak) atau 6 → 7 (Tidak Layak)
- **Action**: Asesor review APL 1, APL 2, dan dokumen administratif

### 5. **Konfirmasi Kehadiran Asesor**
- **Route**: `POST /asesor/confirm-presence/{id}`
- **Status**: Tetap 5
- **Action**: Asesor konfirmasi bisa hadir

### 6. **Asesi Isi APL 1 & APL 2**
- **Route**: 
  - APL 1: `GET /asesi/template/apl1/{pendaftaranId}`
  - APL 2: `GET /asesi/sertifikasi/{id}/apl2`
- **Action**: Asesi mengisi formulir APL (bisa dilakukan setelah daftar)
- **Status**: Tetap (bisa di status 1-5)

### 7. **Verifikasi Kelayakan oleh Asesor**
- **Route**: 
  - List: `GET /asesor/verifikasi-kelayakan`
  - Form: `GET /asesor/verifikasi-kelayakan/{pendaftaranId}`
  - Submit: `POST /asesor/verifikasi-kelayakan/{pendaftaranId}`
- **Action**: 
  - Asesor melihat:
    1. ✅ **Dokumen administrasi** (sudah diverifikasi Kaprodi)
    2. ✅ **APL 1** (data lengkap asesi)
    3. ✅ **APL 2** (self assessment kompetensi)
  - Asesor memberikan penilaian: **LAYAK** atau **TIDAK LAYAK**
  - Beri catatan (wajib jika tidak layak)
- **Status**: 5 → 6 (Menunggu Approval Kelayakan Admin) atau 5 → 7 (Tidak Layak)

### 8. **Approval Kelayakan oleh Admin** ⭐ **PEMBAYARAN DIBUAT DI SINI**
- **Route**: 
  - Single: `POST /admin/kelayakan/{id}/approve`
  - Batch: `POST /admin/kelayakan/batch-approve`
- **Action**:
  - Update status pendaftaran ke 8 (Menunggu Pembayaran)
  - **BUAT PEMBAYARAN BARU** (status 1 = Belum Bayar)
  - Kirim email notifikasi ke asesi
- **Status**: 6 → 8 (Menunggu Pembayaran)
- **Pembayaran**: Status 1 (Belum Bayar)

### 9. **Asesi Upload Bukti Pembayaran**
- **Route**: `POST /asesi/informasi-pembayaran/{id}`
- **Action**: Upload bukti transfer
- **Status Pembayaran**: 1 → 2 (Menunggu Verifikasi)

### 10. **Verifikasi Pembayaran oleh Admin**
- **Route**: `POST /admin/pembayaran-asesi/{id}/approve`
- **Action**: Admin approve pembayaran
- **Status Pembayaran**: 2 → 4 (Dikonfirmasi)
- **Status Pendaftaran**: 8 → 9 (Menunggu Ujian)

### 11. **Pelaksanaan Ujikom**
- **Status**: 9 → 10 (Ujian Berlangsung) → 11 (Selesai)

## 🔄 Perbandingan Old vs New Flow

### OLD FLOW ❌
```
1. Daftar
2. PEMBAYARAN DULU ⚠️
3. Verifikasi Pembayaran
4. Verifikasi Kaprodi
5. Verifikasi Admin
6. Distribusi
7. Ujikom
```

### NEW FLOW ✅
```
1. Daftar + Upload Dokumen
2. ISI APL 1 & APL 2 (Asesi) ⭐
3. Verifikasi Kaprodi (Dokumen Administrasi)
4. Verifikasi Admin
5. Distribusi Asesor
6. Verif Kehadiran Asesor
7. Verifikasi Kelayakan Asesor (Cek: Dokumen Kaprodi + APL 1 + APL 2) ⭐
8. Approval Kelayakan Admin
9. PEMBAYARAN (baru di sini!) 💰
10. Ujikom
```

## 📊 Status Mapping

| Status | Keterangan |
|--------|------------|
| 1 | Menunggu Verifikasi Kaprodi |
| 2 | Ditolak Kaprodi |
| 3 | Menunggu Verifikasi Admin |
| 4 | Menunggu Distribusi Asesor |
| 5 | Menunggu Verifikasi Kelayakan Asesor |
| 6 | Menunggu Approval Kelayakan Admin |
| 7 | Tidak Lulus Kelayakan |
| 8 | **Menunggu Pembayaran** ⭐ |
| 9 | Menunggu Ujian |
| 10 | Ujian Berlangsung |
| 11 | Selesai |
| 12 | Asesor Tidak Dapat Hadir |

## 🎯 Key Changes

### 1. **DaftarUjikomController**
- ❌ Tidak lagi buat Pembayaran di awal
- ✅ Langsung buat Pendaftaran (status 1)
- ✅ Redirect ke halaman Sertifikasi untuk isi APL

### 2. **KelayankanController**
- ✅ Buat Pembayaran setelah approve kelayakan
- ✅ Support batch approval
- ✅ Email otomatis ke asesi

### 3. **CheckSecondRegistration Middleware**
- ✅ Cek pendaftaran aktif (bukan pembayaran pending)
- ✅ Redirect sesuai status pendaftaran
- ✅ Informative error messages

### 4. **SecondRegistrationService**
- ⚠️ Service lama, tidak digunakan di new flow
- 💡 Kept for backward compatibility

## 📝 Testing Flow

Gunakan Testing Tools (`/admin/testing`) dengan urutan:

1. ✅ **Loloskan Verifikasi** - Status 1,3 → 4
2. ✅ **Distribusi Asesor** - Status 4 → 5 + Email
3. ✅ **Auto Approve Kelayakan** ⭐ - Status 5 → 6 → 8 + **BUAT PEMBAYARAN**
4. ✅ **Auto Verify Pembayaran** - Pembayaran 1 → 4, Pendaftaran 8 → 9
5. ✅ **Mulai Jadwal** - Jadwal 1 → 3
6. ✅ **Mulai Ujikom** - Ujikom → Status 2
7. ✅ **Selesai Ujikom** - Ujikom → Status 3
8. ✅ **Selesai Jadwal** - Jadwal → Status 4
9. ✅ **Upload Sertifikat** - Generate sertifikat

## 🚀 Benefits

1. ✅ **Konsisten**: Semua pendaftaran pakai flow yang sama
2. ✅ **Fair**: Pembayaran setelah lolos kelayakan
3. ✅ **Efficient**: Batch approval untuk admin
4. ✅ **Transparent**: Asesi tahu progress pendaftaran
5. ✅ **Automated**: Email notifikasi di setiap step

## 📌 Important Notes

- Pembayaran **HANYA** dibuat setelah kelayakan disetujui
- Middleware mencegah double registration
- Asesi bisa daftar lagi jika pendaftaran ditolak (status 2 atau 7)
- Email otomatis di setiap perubahan status penting

---

**Last Updated**: 21 Desember 2025
**Version**: 2.0 (Unified Flow)

