# 📄 Template Master System

Sistem untuk mengelola template dokumen APL (Asesmen Penilaian Lapangan) dengan fitur auto-generate menggunakan PhpWord dan TTD digital.

## 🎯 Fitur Utama

### ✅ **Admin Features:**
- Upload template master (.docx) untuk setiap skema
- Define variables yang bisa diganti secara otomatis
- Upload TTD digital untuk ditambahkan ke template
- Manage multiple template types (APL 1, APL 2, APL 3, dll)
- Toggle status aktif/nonaktif template
- Download template master

### ✅ **Asesi Features:**
- Generate APL 1 otomatis dengan data pendaftaran
- Preview data sebelum generate
- Download file DOCX yang sudah terisi
- TTD digital otomatis di-insert ke dokumen

---

## 🗂️ Database Structure

### **Table: `template_master`**

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| nama_template | string | Nama template |
| tipe_template | string | APL1, APL2, APL3, dll |
| skema_id | bigint | Foreign key ke table skema |
| deskripsi | text | Deskripsi template (optional) |
| file_path | string | Path ke file template .docx |
| variables | json | Array variable yang bisa diganti |
| ttd_path | string | Path ke file TTD digital (optional) |
| is_active | boolean | Status aktif template |
| created_at | timestamp | - |
| updated_at | timestamp | - |

**Constraints:**
- Unique: `tipe_template` + `skema_id` (satu template per tipe per skema)
- Foreign key: `skema_id` → `skema.id` (cascade on delete)

---

## 📋 Variables Template

### **Default Variables yang Tersedia:**

#### **Data Asesi:**
- `{{nama_asesi}}` - Nama lengkap asesi
- `{{email_asesi}}` - Email asesi
- `{{telephone_asesi}}` - Nomor telepon asesi
- `{{alamat_asesi}}` - Alamat asesi
- `{{nik_asesi}}` - NIK asesi

#### **Data Skema:**
- `{{nama_skema}}` - Nama skema sertifikasi
- `{{kode_skema}}` - Kode skema
- `{{kategori_skema}}` - Kategori skema (Sertifikasi/Pelatihan)
- `{{bidang_skema}}` - Bidang skema

#### **Data Jadwal:**
- `{{tanggal_ujian}}` - Tanggal ujian
- `{{waktu_mulai}}` - Waktu mulai ujian
- `{{waktu_selesai}}` - Waktu selesai ujian
- `{{lokasi_ujian}}` - Nama TUK (Tempat Uji Kompetensi)

#### **Data Sistem:**
- `{{tanggal_generate}}` - Tanggal generate dokumen
- `{{waktu_generate}}` - Waktu generate dokumen
- `{{nomor_pendaftaran}}` - ID pendaftaran
- `{{ttd_digital}}` - Placeholder untuk TTD digital (akan diganti dengan image)

---

## 🚀 Cara Penggunaan

### **1. Admin - Upload Template Master**

1. Login sebagai Admin
2. Menu **"Template Master"** → **"Tambah Template"**
3. Isi form:
   - **Nama Template**: Nama identifikasi template
   - **Tipe Template**: Pilih APL 1 (nanti bisa ditambah APL 2, APL 3)
   - **Skema**: Pilih skema sertifikasi
   - **Deskripsi**: Keterangan template (opsional)
   - **File Template**: Upload file .docx
   - **TTD Digital**: Upload gambar TTD (opsional)
   - **Variables**: Define variable yang akan diganti (minimal 1)
4. Klik **"Simpan Template"**

### **2. Membuat Template DOCX**

1. Buat dokumen Word (.docx) sesuai format APL yang diinginkan
2. Gunakan **double curly braces** untuk variable:
   ```
   Nama Asesi: {{nama_asesi}}
   Skema: {{nama_skema}} ({{kode_skema}})
   Tanggal Ujian: {{tanggal_ujian}}
   ```
3. Untuk TTD digital, tambahkan placeholder:
   ```
   TTD Digital:
   {{ttd_digital}}
   ```
4. Save as .docx dan upload ke sistem

### **3. Asesi - Generate APL 1**

1. Login sebagai Asesi
2. Menu **"Sertifikasi"**
3. Cari pendaftaran dengan status **"Menunggu Ujian"** (status 4)
4. Klik tombol **"👁️ Preview"** untuk melihat data yang akan digunakan
5. Klik tombol **"📄 APL 1"** atau **"Generate & Download APL 1"** dari modal
6. File DOCX akan otomatis terdownload dengan data yang sudah terisi

---

## 🔧 Technical Details

### **Service: `TemplateGeneratorService`**

#### **Method: `generateApl1($pendaftaran, $customData = [])`**
- Generate APL 1 dari template master
- Auto-replace variables dengan data pendaftaran
- Insert TTD digital jika tersedia
- Return file path dan download URL

#### **Method: `checkTemplateExists($tipeTemplate, $skemaId)`**
- Cek apakah template tersedia untuk skema tertentu

#### **Method: `validateTemplate($templatePath)`**
- Validasi file template
- Extract variables dari template

#### **Method: `getAvailableVariables($tipeTemplate = 'APL1')`**
- Get list default variables yang tersedia

### **Controller Routes:**

#### **Admin:**
```php
// CRUD Template Master
Route::resource('template-master', AdminTemplateController::class)
    ->names('admin.template-master');

// Download template
Route::get('template-master/{id}/download', [AdminTemplateController::class, 'download'])
    ->name('admin.template-master.download');

// Toggle status
Route::post('template-master/{id}/toggle-status', [AdminTemplateController::class, 'toggleStatus'])
    ->name('admin.template-master.toggle-status');
```

#### **Asesi:**
```php
// Generate APL 1
Route::get('template/generate-apl1/{pendaftaranId}', [Asesi\TemplateController::class, 'generateApl1'])
    ->name('asesi.template.generate-apl1');

// Preview data
Route::get('template/preview-apl1-data/{pendaftaranId}', [Asesi\TemplateController::class, 'previewApl1Data'])
    ->name('asesi.template.preview-apl1-data');
```

---

## 📁 File Structure

```
app/
├── Models/
│   └── TemplateMaster.php           # Model template master
├── Http/Controllers/
│   ├── Admin/
│   │   └── AdminTemplateController.php  # CRUD template
│   └── Asesi/
│       └── TemplateController.php       # Generate APL 1
└── Services/
    └── TemplateGeneratorService.php     # Service untuk generate dokumen

database/
└── migrations/
    └── 2025_10_12_161705_create_template_master_table.php

resources/
└── views/
    └── components/pages/
        ├── admin/template-master/
        │   ├── list.blade.php
        │   ├── create.blade.php
        │   ├── edit.blade.php
        │   └── show.blade.php
        └── asesi/sertifikasi/
            └── list.blade.php  # Dengan tombol generate APL 1

storage/
└── app/public/
    ├── templates/           # Folder template master (.docx)
    ├── ttd/                # Folder TTD digital
    └── generated/apl1/     # Folder hasil generate APL 1
```

---

## 🔐 Security

### **Authorization:**
- Admin: Full access ke template master
- Asesi: Hanya bisa generate untuk pendaftaran sendiri
- Validasi status pendaftaran (harus status 4 - Menunggu Ujian)

### **File Upload:**
- Template: .docx only, max 10MB
- TTD: PNG, JPG, JPEG only, max 2MB
- File validation dengan Laravel validation rules

### **Data Privacy:**
- Asesi hanya bisa akses data pendaftaran sendiri
- Generated files disimpan dengan nama unique (timestamp)

---

## 🎨 UI/UX Features

### **Admin Interface:**
- ✅ DataTables untuk list template
- ✅ Checkbox UI untuk variables
- ✅ Dynamic add/remove variables
- ✅ Preview TTD digital
- ✅ Download template master
- ✅ Toggle status aktif/nonaktif
- ✅ Badge untuk tipe template

### **Asesi Interface:**
- ✅ Tombol generate hanya muncul untuk status "Menunggu Ujian"
- ✅ Modal preview data sebelum generate
- ✅ Color-coded preview table (Data Asesi, Skema, Jadwal, Sistem)
- ✅ Direct download setelah generate
- ✅ Loading spinner saat fetch data

---

## 📦 Dependencies

### **PHP Libraries:**
```json
{
    "phpoffice/phpword": "^1.4",
    "dompdf/dompdf": "^3.1"
}
```

### **Install:**
```bash
composer require phpoffice/phpword
composer require dompdf/dompdf
```

---

## 🧪 Testing Flow

### **1. Setup Template:**
```bash
Admin → Template Master → Create
- Upload template APL 1 untuk skema tertentu
- Define variables
- Upload TTD (opsional)
```

### **2. Test Generate:**
```bash
Testing Tools → Loloskan Verifikasi → Distribusi Asesor
Asesi → Login → Sertifikasi → Generate APL 1
```

### **3. Verify:**
- Cek file generated di `storage/app/public/generated/apl1/`
- Open DOCX dan verify variables sudah terisi
- Cek TTD digital sudah ter-insert (jika ada)

---

## 🔄 Future Enhancements

### **Planned Features:**
- [ ] APL 2 (Portofolio) template
- [ ] APL 3 (Simulasi) template
- [ ] Convert DOCX to PDF
- [ ] Email attachment hasil generate
- [ ] Template versioning
- [ ] Batch generate untuk multiple asesi
- [ ] Custom variable per template
- [ ] Template preview sebelum upload

---

## 📝 Notes

1. **TTD Digital**: Gunakan format PNG dengan background transparan untuk hasil terbaik
2. **Variables**: Pastikan nama variable di template DOCX match dengan yang didefinisikan di sistem
3. **File Size**: Template yang terlalu besar akan memperlambat proses generate
4. **Status**: Hanya pendaftaran dengan status 4 (Menunggu Ujian) yang bisa generate APL 1

---

## 🐛 Troubleshooting

### **Template tidak ditemukan:**
- Pastikan template sudah diupload untuk skema yang sesuai
- Cek status template (harus aktif)
- Verify tipe template (APL1)

### **Variables tidak ter-replace:**
- Pastikan format di DOCX menggunakan `{{variable_name}}`
- Cek nama variable match dengan yang didefinisikan
- Gunakan curly braces biasa, bukan special characters

### **TTD tidak muncul:**
- Upload file TTD dalam format PNG/JPG/JPEG
- Pastikan placeholder `{{ttd_digital}}` ada di template
- Cek file TTD ada di storage

---

**Dibuat dengan ❤️ menggunakan Laravel + PhpWord**
