# URL & Redirect Checklist - NEW FLOW

## ✅ Yang Sudah Diperbaiki

### 1. **DaftarUjikomController** ✅
**File**: `app/Http/Controllers/Asesi/DaftarUjikomController.php`

**Method `index()`**:
- ❌ Removed: Check pembayaran pending dari `SecondRegistrationService`
- ✅ Updated: Hanya cek apakah pernah daftar (untuk info)
- ✅ Updated: Removed `$isSecondRegistration` variable

**Method `store()`**:
- ❌ Removed: Buat pembayaran via `SecondRegistrationService`
- ✅ Updated: Langsung buat **Pendaftaran** (status 1)
- ✅ Updated: Redirect ke `asesi.sertifikasi.index` untuk isi APL
- ✅ Added: Check duplikasi pendaftaran
- ✅ Added: Hapus pendaftaran lama jika ditolak (status 2 atau 7)

### 2. **CheckSecondRegistration Middleware** ✅
**File**: `app/Http/Middleware/CheckSecondRegistration.php`

- ❌ Removed: Check based on pembayaran status
- ✅ Updated: Check based on **pendaftaran status** (1-10)
- ✅ Added: Redirect sesuai status pendaftaran
- ✅ Added: Informative messages untuk setiap status
- ✅ Added: Special handling untuk status 8 (Menunggu Pembayaran)

### 3. **View: Daftar Ujikom** ✅
**File**: `resources/views/components/pages/asesi/daftar-ujikom/index.blade.php`

**Registration Info Card**:
- ❌ Removed: Info pembayaran terakhir
- ✅ Updated: Info sederhana tentang pendaftaran sebelumnya

**Form Submit Button**:
- ✅ Updated: Text "Daftar Sekarang" (bukan "Simpan & Lanjut Pembayaran")
- ✅ Updated: Icon dan styling konsisten

**Scripts**:
- ❌ Removed: Show payment confirmation modal
- ✅ Added: Info box tentang langkah selanjutnya

**Info Box Baru**:
```
Langkah Selanjutnya:
1. Lengkapi formulir APL
2. Verifikasi Kaprodi → Admin → Asesor
3. Kelayakan disetujui → Pembayaran
4. Upload bukti → Konfirmasi → Ujikom
```

### 4. **Payment Confirmation Modal** ✅
**File**: `resources/views/components/modals/payment-confirmation-modal.blade.php`

**Ketentuan Section**:
- ❌ Removed: "Biaya pendaftaran kedua di awal"
- ✅ Updated: Flow pendaftaran baru dengan timeline
- ✅ Added: Alert box "Pembayaran SETELAH kelayakan disetujui"

## 🔄 Flow Redirect yang Benar

### **A. Asesi Mendaftar**
```
Route: POST /asesi/daftar-ujikom/store
↓
Success: Buat Pendaftaran (status 1)
↓
Redirect: /asesi/sertifikasi ✅
Message: "Berhasil daftar! Silakan lengkapi formulir APL"
```

### **B. Asesi Coba Daftar Lagi (Ada Pendaftaran Aktif)**
```
Middleware: CheckSecondRegistration
↓
Check: Ada pendaftaran status 1-10?
↓
YES → Redirect sesuai status:
  - Status 1,3,4,5,6: → /asesi/sertifikasi (info: sedang diverifikasi)
  - Status 8: → /asesi/informasi-pembayaran (pesan: silakan bayar)
  - Status 9,10: → /asesi/sertifikasi (info: menunggu ujian)
↓
NO → Proceed ke form daftar
```

### **C. Admin Approve Kelayakan**
```
Route: POST /admin/kelayakan/{id}/approve
↓
Action:
1. Update status pendaftaran: 6 → 8
2. Buat Pembayaran (status 1) ⭐
3. Kirim email ke asesi
↓
Email Content: "Kelayakan disetujui! Silakan lakukan pembayaran"
```

### **D. Asesi Upload Bukti Pembayaran**
```
Route: POST /asesi/informasi-pembayaran/{id}
↓
Action: Upload bukti transfer
↓
Update: Pembayaran status 1 → 2
↓
Redirect: /asesi/informasi-pembayaran
Message: "Bukti pembayaran berhasil diupload, tunggu verifikasi"
```

### **E. Admin Verifikasi Pembayaran**
```
Route: POST /admin/pembayaran-asesi/{id}/approve
↓
Action:
1. Update Pembayaran: status 2 → 4
2. Update Pendaftaran: status 8 → 9
3. Kirim email ke asesi
↓
Email: "Pembayaran dikonfirmasi! Menunggu jadwal ujian"
```

## 🎯 Button & Menu Consistency

### **Dashboard Asesi**
- ✅ Button "Daftar Ujikom" → `/asesi/daftar-ujikom`
- ✅ Menu "Sertifikasi" → `/asesi/sertifikasi` (Isi APL)
- ✅ Menu "Informasi Pembayaran" → `/asesi/informasi-pembayaran` (Hanya muncul jika status 8)

### **Form Daftar Ujikom**
- ✅ Submit Button: "Daftar Sekarang"
- ✅ Cancel Button: Redirect ke `/dashboard/asesi`
- ✅ Success: Redirect ke `/asesi/sertifikasi`

### **Halaman Sertifikasi (APL)**
- ✅ Button "Isi APL 1" → `/asesi/template/apl1/{id}`
- ✅ Button "Isi APL 2" → `/asesi/sertifikasi/{id}/apl2`
- ✅ Status badge sesuai status pendaftaran

### **Informasi Pembayaran**
- ✅ Hanya bisa diakses jika ada pembayaran
- ✅ Form upload bukti pembayaran
- ✅ Status pembayaran dengan badge

## 📝 Status Messages

### **Success Messages**
```php
// Setelah daftar
"Berhasil daftar ujikom! Silakan lengkapi formulir APL."

// Setelah approve kelayakan
"Kelayakan telah diapprove! Pembayaran telah dibuat untuk asesi."

// Setelah upload bukti
"Bukti pembayaran berhasil diupload. Menunggu verifikasi admin."

// Setelah verifikasi pembayaran
"Pembayaran dikonfirmasi! Anda sudah terdaftar untuk ujian."
```

### **Warning Messages**
```php
// Ada pendaftaran aktif
"Pendaftaran Anda sedang menunggu verifikasi Kaprodi."
"Pendaftaran Anda sudah disetujui. Silakan selesaikan pembayaran."

// Ada pembayaran pending (backward compatibility)
"Anda memiliki pembayaran yang belum diselesaikan."
```

### **Error Messages**
```php
// Coba daftar ulang
"Anda sudah mendaftar untuk jadwal ini."

// Profile belum lengkap
"Asesi harus melengkapi profil"
```

## ✅ Checklist Final

- [x] DaftarUjikomController tidak buat pembayaran di awal
- [x] Redirect setelah daftar ke sertifikasi (bukan pembayaran)
- [x] Middleware cek status pendaftaran (bukan pembayaran)
- [x] View daftar-ujikom update info & button
- [x] Payment modal update dengan flow baru
- [x] Info box tambahan di form daftar
- [x] KelayankanController buat pembayaran setelah approve
- [x] Email template sesuai dengan flow baru
- [x] Status messages konsisten
- [x] Documentation lengkap

## 🚫 Yang TIDAK Boleh Ada Lagi

❌ Pembayaran dibuat saat daftar  
❌ Redirect ke `/informasi-pembayaran` setelah daftar  
❌ Check `lastPayment->status` di middleware  
❌ Modal konfirmasi pembayaran saat daftar  
❌ Info "Upload bukti pembayaran" di form daftar  
❌ SecondRegistrationService untuk buat pembayaran  

## ✅ Yang Harus Ada

✅ Pendaftaran dibuat langsung (status 1)  
✅ Redirect ke `/sertifikasi` untuk isi APL  
✅ Pembayaran dibuat SETELAH kelayakan disetujui  
✅ Check status pendaftaran di middleware  
✅ Info tentang flow baru di form daftar  
✅ Email notifikasi di setiap step penting  

---

**Last Updated**: 21 Desember 2025  
**Status**: ✅ All URLs & Redirects Updated to NEW FLOW

