# Template Variable APL2 Documentation

## Overview
Template APL2 menggunakan variable yang akan diganti dengan data dari sistem saat generate dokumen Word.

**Format Variable**: Semua variable menggunakan format `${variable_name}` (dengan dollar sign)

## Available Variables

### 1. Data Asesi
- `${nama_asesi}` - Nama lengkap asesi
- `${email_asesi}` - Email asesi
- `${telephone_asesi}` - Nomor telepon asesi
- `${alamat_asesi}` - Alamat asesi
- `${nik_asesi}` - NIK asesi

### 2. Data Skema
- `${nama_skema}` - Nama skema sertifikasi
- `${kode_skema}` - Kode skema
- `${kategori_skema}` - Kategori skema
- `${bidang_skema}` - Bidang skema

### 3. Data Jadwal
- `${tanggal_ujian}` - Tanggal ujian
- `${waktu_mulai}` - Waktu mulai ujian
- `${waktu_selesai}` - Waktu selesai ujian
- `${lokasi_ujian}` - Lokasi ujian (TUK)

### 4. Data Sistem
- `${tanggal_generate}` - Tanggal generate dokumen
- `${waktu_generate}` - Waktu generate dokumen
- `${nomor_pendaftaran}` - Nomor pendaftaran

### 5. Data APL2
- `${soal_apl2}` - Daftar soal APL2 dengan deskripsi bukti
- `${jawaban_apl2}` - Jawaban asesi untuk setiap soal
- `${bukti_apl2}` - File bukti yang diupload
- `${bk_k_checkbox}` - Checkbox BK/K yang sudah tercentang sesuai jawaban asesi
- `${k_checkbox}` - Centang untuk kolom K (Kompeten) - untuk format tabel
- `${bk_checkbox}` - Centang untuk kolom BK (Belum Kompeten) - untuk format tabel
- `${radio_k_checkbox}` - Centang khusus untuk radio K (Kompeten) - untuk layout radio khusus
- `${radio_bk_checkbox}` - Centang khusus untuk radio BK (Belum Kompeten) - untuk layout radio khusus
- `${asesor_penilaian}` - Penilaian asesor (hanya untuk view asesor)

### 6. Tanda Tangan
- `${ttd_asesi}` - Tanda tangan digital asesi
- `${ttd_asesor}` - Tanda tangan digital asesor

## Contoh Penggunaan di Template Word

### Soal APL2
```
${soal_apl2}
```

Output:
```
1. Dapatkah saya mengaplikasikan keterampilan dasar komunikasi?
   Bukti yang diperlukan: Bukti kemampuan komunikasi di tempat kerja

2. Apakah saya mampu mengidentifikasi proses komunikasi dengan baik?
   Bukti yang diperlukan: Bukti identifikasi proses komunikasi
```

### Checkbox BK/K untuk Format Tabel
```
Kolom K: ${k_checkbox}
Kolom BK: ${bk_checkbox}
```

Output:
```
Kolom K: ☑
Kolom BK: ☐
```

**Catatan**: Variable ini digunakan untuk format tabel dimana kolom K dan BK terpisah. Centang (☑) akan muncul di kolom yang dipilih asesi, dan tidak centang (☐) di kolom lainnya.

### Checkbox Radio Khusus untuk Layout Radio
```
Kolom K: ${radio_k_checkbox}
Kolom BK: ${radio_bk_checkbox}
```

Output:
```
Kolom K: √
Kolom BK: (kosong)
```

**Catatan**: Variable ini digunakan untuk layout radio khusus dimana template Word memiliki struktur khusus untuk radio button BK/K. Centang (√) akan muncul di kolom yang dipilih asesi, dan kolom lainnya akan kosong. Setelah generate, centang tidak bisa diubah lagi karena sudah terpatri di dokumen.

### Template Word yang Diupload Admin
**PENTING**: Template Word yang diupload admin harus **persis sama** dengan layout yang akan dihasilkan untuk asesi dan asesor. Admin hanya perlu upload template Word dengan struktur tabel yang sudah ada, dan sistem tinggal mengganti variable saja.

```
Kompetensi: _________________
Dapatkah Saya ................?

┌─────────────────────────────────┬─────┬─────┬──────────┐
│ Elemen / Kriteria Unjuk Kerja   │  K  │ BK  │  Bukti   │
├─────────────────────────────────┼─────┼─────┼──────────┤
│ ${soal_apl2}                    │${radio_k_checkbox}│${radio_bk_checkbox}│${bukti_apl2}│
└─────────────────────────────────┴─────┴─────┴──────────┘
```

**Cara Kerja**:
1. **Admin upload template Word** dengan struktur tabel yang sudah ada
2. **Sistem mengganti variable** `${soal_apl2}`, `${radio_k_checkbox}`, `${radio_bk_checkbox}`, `${bukti_apl2}` dengan data asli
3. **Hasil generate** akan memiliki layout yang persis sama dengan template yang diupload admin

### Contoh Hasil Generate
```
Kompetensi: Mengaplikasikan Keterampilan Dasar Komunikasi
Dapatkah Saya ................?

┌─────────────────────────────────┬─────┬─────┬──────────┐
│ Elemen / Kriteria Unjuk Kerja   │  K  │ BK  │  Bukti   │
├─────────────────────────────────┼─────┼─────┼──────────┤
│ 1. Elemen: Mengidentifikasi     │ √   │     │          │
│    proses komunikasi             │     │     │          │
│    Kriteria Unjuk Kerja:        │     │     │          │
│    1.1 Mengidentifikasi proses  │     │     │          │
│        komunikasi               │     │     │          │
│                                 │     │     │          │
│ 2. Elemen: Menangani informasi  │     │ √   │          │
│    Kriteria Unjuk Kerja:        │     │     │          │
│    2.2 Menangani informasi      │     │     │          │
│                                 │     │     │          │
│ 3. Elemen: Membuat konsep       │ √   │     │          │
│    komunikasi tertulis          │     │     │          │
│    Kriteria Unjuk Kerja:        │     │     │          │
│    3.3 Membuat konsep komunikasi│     │     │          │
│        tertulis                 │     │     │          │
└─────────────────────────────────┴─────┴─────┴──────────┘
```

### Bukti yang Diperlukan
```
${bukti_apl2}
```

**Catatan**: Variable ini untuk menampilkan bukti yang diperlukan untuk setiap pertanyaan. Biasanya diisi dengan informasi tentang dokumen atau file yang harus disertakan.

## Cara Menggunakan di Template Word

1. **Buka template Word APL2**
2. **Ganti text dengan variable** menggunakan format `${variable_name}`
3. **Upload template** ke sistem melalui admin
4. **Generate dokumen** akan otomatis mengganti variable dengan data real

## Contoh Template Word Structure

```
APL 02 - PORTOFOLIO ASESI

Nama Asesi: ${nama_asesi}
Email: ${email_asesi}
Skema: ${nama_skema}
Tanggal Generate: ${tanggal_generate}

SOAL APL2:
${soal_apl2}

JAWABAN ASESI:
${bk_k_checkbox}

PENILAIAN ASESOR:
${asesor_penilaian}

Tanda Tangan Asesi: ${ttd_asesi}
Tanda Tangan Asesor: ${ttd_asesor}
```

## Notes
- Semua variable menggunakan format `${variable_name}` (dengan dollar sign)
- Semua variable akan diganti dengan data real saat generate
- Jika data tidak ada, variable akan kosong
- Checkbox menggunakan simbol ☑ dan ☐ untuk tampilan yang jelas
- Tanda tangan akan diinsert sebagai gambar
