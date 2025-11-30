# Petunjuk Membuat Template FR AK 05

## 1. Struktur Template Word (.docx)

Template FR AK 05 harus dibuat dalam format Microsoft Word (.docx) dengan struktur seperti contoh di bawah.

### Bagian Header (Informasi Umum)

```
FR.AK.05.    LAPORAN ASESMEN

┌──────────────────────────────┬───────┬──────────────────────────────┐
│ Skema Sertifikasi            │ Judul │ : ${skema.judul}             │
│ (KKNI/Okupasi/Klaster)       │ Nomor │ : ${skema.nomor}             │
├──────────────────────────────┴───────┼──────────────────────────────┤
│ TUK                                   │ : ${tuk}                     │
├───────────────────────────────────────┼──────────────────────────────┤
│ Nama Asesor                           │ : ${nama_asesor}             │
├───────────────────────────────────────┼──────────────────────────────┤
│ Tanggal                               │ : ${tanggal}                 │
└───────────────────────────────────────┴──────────────────────────────┘
```

### Bagian Tabel Asesi (PENTING!)

**Buat tabel dengan 1 row yang berisi placeholder berikut:**

```
┌─────┬─────────────────┬────────────────────┬─────────────────────┐
│ No. │ Nama Asesi      │  Rekomendasi       │   Keterangan        │
│     │                 ├──────┬─────────────┤                     │
│     │                 │  K   │     BK      │                     │
├─────┼─────────────────┼──────┼─────────────┼─────────────────────┤
│ ${no} │ ${nama_asesi} │ ${checkbox_k} │ ${checkbox_bk} │ ${keterangan} │
└─────┴─────────────────┴──────┴─────────────┴─────────────────────┘
```

**Catatan Penting:**
- Row yang berisi `${nama_asesi}` akan **otomatis di-clone** sebanyak jumlah asesi
- Jangan tambah row manual di bawahnya, sistem akan auto-generate
- Checkbox K/BK akan otomatis dicentang (☑/☐) sesuai status asesi

### Bagian Bawah (Opsional)

Anda bisa menambahkan field custom seperti:

```
Aspek Negatif dan Positif dalam Asesmen:
${aspek_positif_negatif}

Pencatatan Penolakan Hasil Asesmen:
${pencatatan_penolakan}

Rekomendasi:
${rekomendasi}
```

---

## 2. Variables yang Tersedia Otomatis

Variables ini akan **otomatis terisi** oleh sistem:

### Header Information:
- `${skema.judul}` → Nama skema sertifikasi (contoh: "System Analyst")
- `${skema.nomor}` → Kode skema (contoh: "36/SS/UN61/LSP-UPNVJ/2023")
- `${tuk}` → Nama TUK (Tempat Uji Kompetensi)
- `${nama_asesor}` → Nama asesor yang login
- `${tanggal}` → Tanggal generate dokumen (format: dd-mm-yyyy)

### Statistik Asesi:
- `${total_asesi}` → Total jumlah asesi
- `${asesi_kompeten}` → Jumlah asesi kompeten
- `${asesi_tidak_kompeten}` → Jumlah asesi tidak kompeten

### Tabel Dinamis (per row asesi):
- `${no}` → Nomor urut asesi (1, 2, 3, ...)
- `${nama_asesi}` → Nama lengkap asesi (**WAJIB** untuk clone row)
- `${checkbox_k}` → Checkbox Kompeten (☑ jika kompeten, ☐ jika tidak)
- `${checkbox_bk}` → Checkbox Belum Kompeten (☑ jika tidak kompeten, ☐ jika kompeten)
- `${keterangan}` → Keterangan yang diisi asesor per asesi (opsional)

### System Variables:
- `${jadwal.tanggal_ujian}` → Tanggal ujian
- `${system.tanggal_generate}` → Tanggal dokumen dibuat
- `${system.waktu_generate}` → Waktu dokumen dibuat

### Signature (Tanda Tangan):
- Tanda tangan asesor menggunakan **signature pad** digital di form (bukan text field)
- **Field name** ditentukan dari **Custom Variables** dengan type `signature_pad`
- Contoh: Jika di custom variables ada field dengan name `ttd_digital_asesor` dan type `signature_pad`,
  maka di template Word gunakan placeholder `${ttd_digital_asesor}` sebagai **image placeholder**
- Tanda tangan akan diinsert sebagai gambar PNG ke dalam dokumen Word
- **WAJIB** membuat custom field dengan type `signature_pad` untuk signature asesor

---

## 3. Cara Menggunakan Custom Variables

### A. Untuk Field Tanda Tangan (WAJIB):

1. Saat **Create/Edit Template** di admin, scroll ke bagian **"Custom Fields"**
2. Klik **"Tambah Custom Field"**
3. Isi untuk signature:
   - **Name**: `ttd_digital_asesor` (atau nama lain sesuai kebutuhan)
   - **Label**: Tanda Tangan Digital Asesor
   - **Type**: `signature_pad` (PENTING!)
   - **Required**: ☑ (wajib)
   - **Role**: `asesor`

4. Di template Word, insert **image placeholder** dengan nama: `${ttd_digital_asesor}`
   - **CATATAN**: Ini harus berupa IMAGE placeholder, bukan text placeholder
   - Di Word: Insert → Quick Parts → Field → IncludePicture
   - Gunakan syntax: `${ttd_digital_asesor}`

### B. Untuk Field Custom Lainnya (Opsional):

Contoh menambah field text/textarea (seperti "Aspek Positif/Negatif", "Rekomendasi", dll):

1. Klik **"Tambah Custom Field"**
2. Isi:
   - **Name**: `aspek_positif_negatif` (tanpa spasi, gunakan underscore)
   - **Label**: Aspek Positif dan Negatif dalam Asesmen
   - **Type**: `textarea`
   - **Required**: ☐ (tidak wajib)
   - **Role**: `asesor` (hanya asesor yang isi)

3. Di template Word, gunakan: `${aspek_positif_negatif}` (text placeholder biasa)

---

## 4. Contoh Template Lengkap

Buat file Word dengan struktur seperti ini:

```docx
FR.AK.05.    LAPORAN ASESMEN

Skema Sertifikasi
├─ Judul: ${skema.judul}
└─ Nomor: ${skema.nomor}

TUK: ${tuk}
Nama Asesor: ${nama_asesor}
Tanggal: ${tanggal}

Tabel Asesi:
┌─────┬──────────────────┬──────┬──────┬──────────────────┐
│ No. │ Nama Asesi       │  K   │  BK  │  Keterangan      │
├─────┼──────────────────┼──────┼──────┼──────────────────┤
│ ${no} │ ${nama_asesi} │ ${checkbox_k} │ ${checkbox_bk} │ ${keterangan} │
└─────┴──────────────────┴──────┴──────┴──────────────────┘

Aspek Negatif dan Positif dalam Asesmen:
${aspek_positif_negatif}

Pencatatan Penolakan Hasil Asesmen:
${pencatatan_penolakan}

Rekomendasi:
${rekomendasi}
```

---

## 5. Langkah Upload Template

1. Login sebagai **Admin**
2. Menu → **Template Master** → **Create**
3. Isi form:
   - **Nama Template**: FR AK 05 - [Nama Skema]
   - **Tipe Template**: Pilih **"FR AK 05 (Form Asesmen Asesor)"**
   - **Skema**: Pilih skema yang sesuai
   - **Deskripsi**: (opsional)
   - **File Template**: Upload file .docx yang sudah dibuat
4. **Variables Template**:
   - Tab **Database Fields**: Centang variables yang dibutuhkan (sudah otomatis)
   - Tab **Custom Fields**: Tambah field custom jika perlu (opsional)
5. Klik **"Simpan Template"**

---

## 6. Cara Asesor Menggunakan

1. Asesor **selesai menilai semua asesi** di jadwal tertentu
2. Tombol **"Generate FR AK 05"** akan muncul (hijau)
3. Klik tombol → Form akan muncul dengan:
   - Informasi jadwal dan statistik (otomatis terisi)
   - Daftar asesi + status (K/BK) **sudah otomatis**
   - Field keterangan per asesi (opsional)
   - Custom fields (jika ada)
   - **Signature pad untuk tanda tangan asesor (WAJIB)**
4. Isi field yang diperlukan
5. **Tanda tangani di area signature pad** (jangan lupa!)
6. Klik **"Generate & Download FR AK 05"**
7. File .docx akan terdownload dengan data yang sudah terisi

**Catatan Penting:**
- Tanda tangan di signature pad adalah **WAJIB**, form tidak bisa di-submit tanpa TTD
- Gunakan mouse/trackpad/stylus untuk menggambar tanda tangan di area yang disediakan
- Klik tombol "Hapus Tanda Tangan" jika ingin mengulang

---

## 7. Tips & Troubleshooting

### Tabel tidak ter-generate dengan benar?
- Pastikan ada placeholder `${nama_asesi}` di row tabel
- Row ini harus dalam 1 tabel Word yang proper (bukan text biasa)
- Jangan tambah row manual, biarkan sistem auto-clone

### Checkbox K/BK tidak muncul?
- Gunakan placeholder `${checkbox_k}` dan `${checkbox_bk}` di kolom yang benar
- Sistem akan replace dengan ☑ atau ☐ otomatis

### Custom field tidak muncul di form asesor?
- Pastikan **Role** field diset ke `asesor` atau `both`
- Jika role = `asesi`, field tidak akan muncul di form FR AK 05

### Template tidak muncul di asesor?
- Pastikan **Tipe Template** = `FR_AK_05`
- Pastikan **Skema** sesuai dengan jadwal ujian
- Pastikan template **is_active** = true

### Tanda tangan tidak muncul di dokumen Word / muncul base64 string panjang?
- **Penyebab**: Placeholder tanda tangan di Word adalah TEXT placeholder, bukan IMAGE placeholder
- **Solusi**: 
  1. Pastikan di template Word, placeholder TTD menggunakan **image placeholder**
  2. Di Word: Insert → Quick Parts → Field → IncludePicture
  3. Field Name harus sama dengan nama field di custom variables (contoh: `${ttd_digital_asesor}`)
  4. **Jangan gunakan** `${variable}` biasa untuk tanda tangan, harus berupa image field
  
- **Cara cek**: Jika muncul string base64 panjang (data:image/png;base64...), berarti placeholder masih text field, bukan image field

### Custom field signature tidak muncul di form?
- Pastikan di **Template Master** sudah membuat custom variable dengan:
  - Type: `signature_pad`
  - Role: `asesor` atau `both`
- Signature field akan otomatis tampil di form FR AK 05 jika ada custom variable dengan type `signature_pad`

---

## 8. Contoh File Template

Download sample template di:
- [Sample FR AK 05 Template.docx](storage/templates/sample_fr_ak_05_template.docx)

---

**Dibuat dengan ❤️ untuk sistem asesmen UPNVJ**
