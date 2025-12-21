# Flow Baru: Verifikasi Setelah Distribusi

## 📋 Overview

Flow pendaftaran yang telah diupdate di mana **distribusi asesor terjadi terlebih dahulu**, kemudian **verifikasi dokumen administratif** oleh Kaprodi dan Admin dilakukan **setelah distribusi**.

## 🎯 Flow Lengkap (Updated)

### 1. **Asesi Mendaftar + Upload Dokumen**
- **Route**: `POST /asesi/daftar-ujikom`
- **Controller**: `DaftarUjikomController@store`
- **Action**: 
  - Upload dokumen (KTP, Sertifikat, KTM/KHS, Administrasi)
  - **Langsung buat Pendaftaran** dengan status 1
  - **TIDAK buat Pembayaran di awal**
- **Status Pendaftaran**: 1 (Menunggu Distribusi Asesor)
- **Redirect**: Halaman Sertifikasi untuk isi APL
- **Note**: Asesi sudah bisa langsung isi APL 1 dan APL 2 setelah daftar

---

### 2. **Distribusi Asesor (Otomatis via Scheduler atau Manual via Testing Tool)**
- **Scheduler Command**: `php artisan ujikom:distribute` (berjalan otomatis via cron)
- **Manual Trigger**: Testing Tool di `/admin/testing` → "Trigger Distribusi Asesor"
- **Controller**: 
  - `DistributeUjikomCommand` (scheduler)
  - `TestingController@triggerDistribusi` (manual)
- **Action**:
  - Ambil pendaftaran dengan status 1 (Menunggu Distribusi Asesor)
  - Distribusikan ke asesor berdasarkan skema
  - Buat record `PendaftaranUjikom`
  - **Update status: 1 → 5**
  - Kirim email konfirmasi kehadiran ke asesor
- **Status Pendaftaran**: 1 → 5 (Menunggu Verifikasi Dokumen)

---

### 3. **Verifikasi Dokumen Administratif (Kaprodi & Admin - Gabungan)**
- **Route Kaprodi**: `/kaprodi/verifikasi-pendaftaran`
- **Route Admin**: `/admin/verifikasi-pendaftaran`
- **Controller**: `VerifikasiPendaftaranController` (shared untuk Kaprodi dan Admin)
- **Action**:
  - Kaprodi atau Admin melakukan verifikasi dokumen administratif
  - Bisa approve atau reject
  - Jika approve: status 5 → 6
  - Jika reject: status 5 → 2, kirim email penolakan
- **Status Pendaftaran**: 
  - 5 → 6 (Approve - Menunggu Verifikasi Kelayakan)
  - 5 → 2 (Reject - Tidak Lolos Verifikasi Dokumen)
- **Note**: Halaman sama untuk Kaprodi dan Admin, siapa saja bisa verifikasi

---

### 4. **Verifikasi Kelayakan oleh Asesor**
- **Route**: 
  - List: `GET /asesor/verifikasi-kelayakan`
  - Form: `GET /asesor/verifikasi-kelayakan/{pendaftaranId}`
  - Submit: `POST /asesor/verifikasi-kelayakan/{pendaftaranId}`
- **Action**: 
  - Asesor melihat:
    - Dokumen administratif yang sudah diverifikasi Kaprodi/Admin
    - APL 1 dan APL 2 yang sudah diisi asesi
  - Asesor melakukan penilaian kelayakan
  - Jika layak: status 6 → 8 (Menunggu Pembayaran)
  - Jika tidak layak: status 6 → 7 (Tidak Lolos Kelayakan)
- **Status Pendaftaran**: 
  - 6 → 8 (Layak - Menunggu Pembayaran)
  - 6 → 7 (Tidak Layak)

---

### 5. **Pembayaran**
- **Route**: 
  - Info: `GET /asesi/informasi-pembayaran`
  - Upload: `POST /asesi/informasi-pembayaran/upload`
- **Action**:
  - Setelah kelayakan disetujui (status 8), sistem membuat record `Pembayaran`
  - Asesi upload bukti pembayaran
  - Admin verifikasi pembayaran
  - Jika disetujui: status 8 → 9 (Menunggu Ujian)
- **Status Pendaftaran**: 8 → 9 (Menunggu Ujian)

---

### 6. **Pelaksanaan Ujian**
- **Status**: 9 → 10 (Ujian Berlangsung) → 11 (Selesai)
- **Action**:
  - Asesor mengisi formulir penilaian
  - Sistem generate report
  - Status berubah menjadi Selesai

---

## 📊 Status Mapping (Updated)

| Status | Nama Status                          | Keterangan                                    |
|--------|--------------------------------------|-----------------------------------------------|
| 1      | Menunggu Distribusi Asesor           | Setelah daftar, bisa isi APL                  |
| 2      | Tidak Lolos Verifikasi Dokumen       | Ditolak oleh Kaprodi/Admin                    |
| 5      | Menunggu Verifikasi Dokumen          | Setelah distribusi, menunggu Kaprodi/Admin    |
| 6      | Menunggu Verifikasi Kelayakan        | Asesor cek APL + dokumen                      |
| 7      | Tidak Lolos Kelayakan                | Ditolak asesor                                |
| 8      | Menunggu Pembayaran                  | Setelah kelayakan disetujui                   |
| 9      | Menunggu Ujian                       | Setelah pembayaran diverifikasi               |
| 10     | Ujian Berlangsung                    | Sedang ujian                                  |
| 11     | Selesai                              | Ujian selesai                                 |
| 12     | Asesor Tidak Dapat Hadir             | Asesor menolak/tidak bisa hadir               |

**Status yang dihapus**: 3 (Menunggu Verifikasi Admin) dan 4 (Menunggu Distribusi Asesor - diganti dengan status 1)

---

## 🔄 Perbedaan dengan Flow Sebelumnya

### Flow Lama (Sebelum):
1. Asesi Daftar (status 1)
2. ✅ Verifikasi Kaprodi (status 1 → 3)
3. ✅ Verifikasi Admin (status 3 → 4)
4. Distribusi Asesor (status 4 → 5)
5. Verifikasi Kelayakan Asesor (status 5 → 6 → 8)
6. Pembayaran → Ujian

### Flow Baru (Sekarang):
1. Asesi Daftar (status 1) - **Langsung bisa isi APL**
2. **Distribusi Asesor Otomatis** (status 1 → 5)
3. ✅ **Verifikasi Dokumen Administratif (Kaprodi & Admin Gabung)** (status 5 → 6)
4. Verifikasi Kelayakan Asesor (status 6 → 8)
5. Pembayaran → Ujian

**Keuntungan Flow Baru**:
- ✅ Distribusi asesor lebih cepat (otomatis, tidak menunggu verifikasi manual)
- ✅ Asesi bisa langsung isi APL setelah daftar
- ✅ Verifikasi dokumen oleh Kaprodi dan Admin disederhanakan (1 halaman)
- ✅ Asesor bisa langsung lihat APL dan dokumen yang sudah lengkap saat verifikasi kelayakan

---

## 📝 Implementasi Teknis

### Controller yang Diupdate:
1. `app/Http/Controllers/Kaprodi/VerifikasiPendaftaranController.php`
   - Sekarang shared untuk Kaprodi dan Admin
   - Hanya show pendaftaran dengan status 5
   - Update status 5 → 6 (approve) atau 5 → 2 (reject)

2. `app/Console/Commands/DistributeUjikomCommand.php`
   - Ambil pendaftaran dengan status 1 (bukan 4)
   - Update status 1 → 5 setelah distribusi

3. `app/Http/Controllers/Admin/TestingController.php`
   - Trigger distribusi manual untuk pendaftaran status 1
   - Update status 1 → 5

### Middleware yang Diupdate:
- `app/Http/Middleware/CheckSecondRegistration.php`
  - Update message untuk status 1, 5, 6 sesuai flow baru
  - Remove status 3 dan 4

### View yang Diupdate:
- `resources/views/components/pages/kaprodi/verifikasi-pendaftaran/list.blade.php`
- `resources/views/components/pages/admin/verifikasi-pendaftaran/list.blade.php` (copy dari Kaprodi)
- Kedua view menggunakan controller yang sama

### Routes yang Ditambahkan:
```php
// Admin route untuk verifikasi pendaftaran (shared dengan Kaprodi)
Route::resource('verifikasi-pendaftaran', \App\Http\Controllers\Kaprodi\VerifikasiPendaftaranController::class)->names('admin.verifikasi-pendaftaran');
```

---

## 🔍 Testing

### Testing via Testing Tool:
1. Login sebagai Admin
2. Buka `/admin/testing`
3. Klik "Trigger Distribusi Asesor"
4. Sistem akan:
   - Ambil semua pendaftaran dengan status 1
   - Distribusikan ke asesor
   - Update status 1 → 5
   - Kirim email ke asesor

### Testing via Scheduler:
```bash
php artisan ujikom:distribute
```

Scheduler akan berjalan otomatis sesuai jadwal di `app/Providers/ScheduleServiceProvider.php`.

---

## 📧 Email Notifications

1. **Setelah Distribusi**: Email ke asesor untuk konfirmasi kehadiran
2. **Setelah Verifikasi Dokumen Ditolak**: Email ke asesi dengan alasan penolakan
3. **Setelah Kelayakan Disetujui**: Reminder untuk pembayaran (jika ada)

---

## 🎓 Summary

Flow baru ini menyederhanakan proses dengan:
- Distribusi asesor terjadi lebih awal (otomatis)
- Verifikasi dokumen dilakukan setelah distribusi oleh Kaprodi dan Admin
- Satu halaman verifikasi untuk Kaprodi dan Admin (tidak terpisah)
- Asesi bisa mengisi APL sejak awal tanpa menunggu verifikasi

**Tanggal Update**: 21 Desember 2025

