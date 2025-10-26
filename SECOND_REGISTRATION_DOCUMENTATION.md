# Dokumentasi Sistem Verifikasi dan Popup Pembayaran untuk Pendaftaran Kedua

## 📋 Overview

Sistem ini dirancang untuk menangani verifikasi dan popup pembayaran untuk pendaftaran kedua pada asesi. Fitur ini memastikan bahwa asesi yang sudah pernah mendaftar sebelumnya mendapat informasi yang jelas tentang status pembayaran dan dapat melakukan pendaftaran ulang dengan prosedur yang tepat.

## 🏗️ Arsitektur Sistem

### 1. **Middleware: CheckSecondRegistration**
- **File**: `app/Http/Middleware/CheckSecondRegistration.php`
- **Fungsi**: Mengecek apakah user sudah pernah mendaftar sebelumnya
- **Logika**:
  - Jika ada pembayaran pending (status 1,2) → redirect ke halaman pembayaran
  - Jika pembayaran ditolak (status 3) → tampilkan popup konfirmasi
  - Jika pembayaran dikonfirmasi (status 4) → izinkan pendaftaran baru

### 2. **Service: SecondRegistrationService**
- **File**: `app/Services/SecondRegistrationService.php`
- **Fungsi**: Menangani logika bisnis untuk pendaftaran kedua
- **Method Utama**:
  - `hasPreviousRegistration()`: Cek riwayat pendaftaran
  - `canRegisterAgain()`: Validasi apakah bisa daftar lagi
  - `createSecondRegistrationPayment()`: Buat pembayaran untuk pendaftaran kedua
  - `verifySecondRegistrationPayment()`: Verifikasi pembayaran

### 3. **Controller: RegistrationInfoController**
- **File**: `app/Http/Controllers/Asesi/RegistrationInfoController.php`
- **Fungsi**: Menyediakan API untuk mendapatkan informasi pendaftaran
- **Endpoint**: `GET /asesi/registration-info`

### 4. **Controller: VerifikasiPembayaranController (Admin)**
- **File**: `app/Http/Controllers/Admin/VerifikasiPembayaranController.php`
- **Fungsi**: Admin dapat memverifikasi pembayaran asesi
- **Method**:
  - `approve()`: Setujui pembayaran
  - `reject()`: Tolak pembayaran dengan keterangan

## 🎨 UI Components

### 1. **Payment Confirmation Modal**
- **File**: `resources/views/components/modals/payment-confirmation-modal.blade.php`
- **Fitur**:
  - Informasi riwayat pendaftaran
  - Detail pembayaran terakhir
  - Ketentuan pendaftaran kedua
  - Checkbox konfirmasi
  - Tombol lanjutkan pendaftaran

### 2. **Payment Status Modal**
- **File**: Sama dengan di atas
- **Fitur**:
  - Menampilkan status pembayaran
  - Pesan sukses/error
  - Tombol konfirmasi

## 📊 Status Pembayaran

| Status | Kode | Deskripsi | Aksi |
|--------|------|-----------|------|
| Belum Bayar | 1 | Asesi belum upload bukti pembayaran | Upload bukti |
| Menunggu Verifikasi | 2 | Bukti pembayaran sudah diupload, menunggu admin | Tunggu verifikasi |
| Tidak Lolos Verifikasi | 3 | Pembayaran ditolak admin | Daftar ulang |
| Dikonfirmasi | 4 | Pembayaran disetujui admin | Lanjut ke pendaftaran |

## 🔄 Flow Pendaftaran Kedua

### 1. **Asesi Mengakses Halaman Daftar Ujikom**
```
User → DaftarUjikomController@index → CheckSecondRegistration Middleware
```

### 2. **Middleware Mengecek Riwayat**
```php
if (hasPreviousRegistration()) {
    if (lastPaymentStatus == 1 || 2) {
        redirect('informasi-pembayaran');
    } else if (lastPaymentStatus == 3) {
        showPaymentPopup();
    }
}
```

### 3. **Popup Konfirmasi (Jika Diperlukan)**
- Tampilkan modal dengan informasi riwayat
- User konfirmasi dengan checkbox
- Redirect ke halaman pendaftaran dengan parameter `second_registration=true`

### 4. **Proses Pendaftaran**
```php
// Di DaftarUjikomController@store
$pembayaran = $secondRegistrationService->createSecondRegistrationPayment($jadwalId);
```

### 5. **Verifikasi Admin**
- Admin dapat melihat daftar pembayaran di `/admin/verifikasi-pembayaran`
- Admin dapat approve/reject dengan keterangan

## 🛠️ Konfigurasi

### 1. **Middleware Registration**
```php
// bootstrap/app.php
$middleware->alias([
    'check.second.registration' => CheckSecondRegistration::class,
]);
```

### 2. **Route Configuration**
```php
// routes/web.php
Route::resource('daftar-ujikom', DaftarUjikomController::class)
    ->names('asesi.daftar-ujikom')
    ->middleware('check.second.registration');

Route::get('registration-info', [RegistrationInfoController::class, 'index'])
    ->name('asesi.registration-info');
```

## 📱 Frontend Integration

### 1. **JavaScript Functions**
```javascript
// Tampilkan modal konfirmasi
showPaymentConfirmationModal();

// Tampilkan modal status
showPaymentStatusModal(message, type);

// Load data via AJAX
$.ajax({
    url: '/asesi/registration-info',
    success: function(data) {
        // Update UI dengan data
    }
});
```

### 2. **Modal Triggers**
- Session flash: `show_payment_popup`
- URL parameter: `second_registration=true`
- JavaScript events

## 🔐 Security Features

### 1. **Authorization**
- Middleware `user.type` memastikan hanya asesi yang bisa akses
- Validasi ownership data di controller

### 2. **Data Validation**
- Request validation di semua endpoint
- File upload validation untuk bukti pembayaran
- Status validation untuk verifikasi admin

### 3. **Error Handling**
- Try-catch blocks di semua service methods
- User-friendly error messages
- Logging untuk debugging

## 📈 Monitoring & Analytics

### 1. **Dashboard Integration**
- Card "Pembayaran Pending" di dashboard asesi
- Link langsung ke halaman informasi pembayaran
- Real-time status updates

### 2. **Admin Dashboard**
- Daftar semua pembayaran yang perlu diverifikasi
- Filter berdasarkan status
- Bulk actions untuk verifikasi

## 🚀 Deployment Checklist

### 1. **Database**
- Pastikan tabel `pembayaran` sudah ada
- Pastikan relasi dengan tabel `jadwal`, `users` sudah benar

### 2. **Files**
- Upload semua file controller, service, middleware
- Upload view files dan modal components
- Update routes dan bootstrap configuration

### 3. **Permissions**
- Pastikan storage directory writable untuk upload bukti pembayaran
- Pastikan user roles sudah dikonfigurasi dengan benar

### 4. **Testing**
- Test pendaftaran pertama (normal flow)
- Test pendaftaran kedua dengan berbagai status pembayaran
- Test verifikasi admin (approve/reject)
- Test error handling dan edge cases

## 🔧 Troubleshooting

### 1. **Modal Tidak Muncul**
- Cek session flash messages
- Cek JavaScript console untuk errors
- Pastikan jQuery dan Bootstrap sudah loaded

### 2. **Middleware Tidak Berfungsi**
- Cek registration di `bootstrap/app.php`
- Cek route middleware assignment
- Cek namespace dan import statements

### 3. **Service Error**
- Cek database connection
- Cek model relationships
- Cek validation rules

### 4. **File Upload Issues**
- Cek storage permissions
- Cek file size limits
- Cek MIME type validation

## 📞 Support

Untuk pertanyaan atau masalah teknis, silakan hubungi tim development atau buat issue di repository project.
