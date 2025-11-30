# Testing Tools & Asesor Multiple Skema

## 🎯 Ringkasan Update

Sistem telah diperbarui dengan 2 fitur utama:

### 1. ✅ Asesor Multiple Skema
- Satu asesor bisa punya banyak skema sertifikasi (many-to-many)
- Management di halaman Create/Edit User dengan multiple select
- Distribusi dinamis berdasarkan skema yang dimiliki asesor
- Asesor dengan status `asesor_nonaktif` tidak akan didistribusikan ujikom

### 2. ✅ Testing Tools  
- Halaman khusus untuk testing dengan menu "Testing Tools"
- 7 tombol untuk mensimulasikan full flow sistem
- Quick statistics untuk monitoring
- Bypass scheduler untuk development/testing

---

## 📋 Cara Pakai Testing Tools

### Akses Testing Tools
```
Login sebagai Admin → Menu "Testing Tools"
```

### Alur Testing (Klik Berurutan)

1. **Loloskan Verifikasi** 
   - Update pendaftaran status 1 & 3 → 4 (Menunggu Ujian)

2. **Distribusi Asesor**
   - Distribusi ke asesor berdasarkan skema
   - Kirim email konfirmasi ke asesor
   - Buat PendaftaranUjikom status 6

3. **Mulai Jadwal**
   - Jadwal aktif → Status 3 (Ujian Berlangsung)
   - PendaftaranUjikom status 6 → 1

4. **Mulai Ujikom**
   - PendaftaranUjikom status 1 → 2 (Ujikom Berlangsung)

5. **Selesai Ujikom**
   - PendaftaranUjikom status 2 → 3 (Ujikom Selesai)

6. **Selesai Jadwal**
   - Jadwal status 3 → 4 (Selesai)
   - Pendaftaran → Status 6 (Selesai)

7. **Trigger Pembayaran Asesor**
   - Buat PembayaranAsesor untuk jadwal yang selesai
   - Status: Menunggu Pembayaran

---

## 🔧 Asesor Multiple Skema

### Cara Assign Asesor ke Skema

1. Login sebagai Admin
2. Menu **Skema** → Edit Skema
3. Di bagian **Asesor** akan muncul semua asesor yang tersedia
4. **Pilih asesor dengan checkbox** (lebih mudah dari dropdown)
   - Ada tombol "Pilih Semua Asesor" untuk select all
   - Atau pilih manual dengan checkbox
5. Simpan

### List Skema - Menampilkan Asesor
- Kolom "Asesor" menampilkan badge untuk setiap asesor yang ter-assign
- Jika belum ada asesor, akan tampil "Belum ada asesor"

### Distribusi Berdasarkan Skema
- Command `ujikom:distribute` sekarang pakai relasi many-to-many
- Query: `whereHas('skemas')` untuk ambil asesor berdasarkan skema
- Hanya asesor dengan `user_type = 'asesor'` yang didistribusikan
- Asesor `asesor_nonaktif` akan di-skip

---

## 📁 File Yang Diubah

### Models
- `app/Models/AsesorSkema.php` - Pivot model untuk relasi
- `app/Models/User.php` - Tambah relasi `skemas()`
- `app/Models/Skema.php` - Tambah relasi `asesors()`

### Controllers
- `app/Http/Controllers/Admin/UserController.php` - Handle multiple skema
- `app/Http/Controllers/Admin/TestingController.php` - 7 testing methods

### Views
- `resources/views/components/pages/admin/user/create.blade.php` - Multiple select skema
- `resources/views/components/pages/admin/user/edit.blade.php` - Multiple select skema  
- `resources/views/components/pages/admin/user/list.blade.php` - Kolom skema dengan badge
- `resources/views/components/pages/admin/testing/index.blade.php` - Testing tools UI

### Others
- `app/Traits/MenuTrait.php` - Menu "Testing Tools"
- `app/Console/Commands/DistributeUjikomCommand.php` - Distribusi dinamis
- `routes/web.php` - Testing routes

---

## 🚀 Routes Testing

```php
// Halaman Testing
GET  /admin/testing

// Testing Actions (POST)
POST /admin/testing/update-status-pendaftaran
POST /admin/testing/trigger-distribusi
POST /admin/testing/start-jadwal
POST /admin/testing/simulasi-ujikom
POST /admin/testing/selesaikan-ujikom
POST /admin/testing/selesaikan-jadwal
POST /admin/testing/trigger-pembayaran-asesor
```

---

## ⚠️ Untuk Production

### Hapus Menu Testing

Edit file `app/Traits/MenuTrait.php`, method `getMenuListAdmin()`:

```php
// HAPUS INI:
[
    'title' => 'Testing Tools',
    'url' => 'admin.testing',
    'key' => 'testing'
],
```

**Note:** Routes dan controller bisa dibiarkan. Cuma menu yang dihapus aja, jadi user gak bisa akses.

---

## 🐛 Troubleshooting

### Distribusi tidak berjalan
- Pastikan ada pendaftaran status 4
- Pastikan ada asesor aktif dengan skema yang sesuai
- Cek log di `storage/logs/laravel.log`

### Asesor tidak muncul
- Pastikan role = `asesor` (bukan `asesor_nonaktif`)
- Pastikan punya skema di tabel `asesor_skema`

### Email tidak terkirim
- Cek konfigurasi `.env`
- Lihat log error

---

## 📊 Database Schema

### Tabel `asesor_skema` (Pivot)
```
asesor_id (FK → users.id)
skema_id (FK → skema.id)
```

### Status Reference

**Pendaftaran:**
- 1: Menunggu Verifikasi Kaprodi
- 3: Menunggu Verifikasi Admin
- 4: Menunggu Ujian
- 6: Selesai

**Jadwal:**
- 1: Aktif
- 3: Ujian Berlangsung
- 4: Selesai

**PendaftaranUjikom:**
- 1: Belum Ujikom
- 2: Ujikom Berlangsung
- 3: Ujikom Selesai
- 6: Menunggu Konfirmasi Asesor

**PembayaranAsesor:**
- 1: Menunggu Pembayaran
- 2: Selesai

---

## ✨ Keuntungan

✅ Asesor fleksibel bisa menguji banyak skema  
✅ Testing jadi cepat tanpa tunggu scheduler  
✅ Monitoring mudah dengan statistik  
✅ Control penuh dengan status asesor_nonaktif  
✅ Mudah dihapus untuk production (hapus menu aja)

---

**Version:** 2.0  
**Last Updated:** October 2025

