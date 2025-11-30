# Instruksi: TTD Asesor Tidak Muncul di FR 06

## Masalah
TTD asesor tidak muncul di dokumen FR 06 yang di-generate.

## Penyebab
Asesor belum mengisi TTD digital di form review FR 06.

## Solusi

### Langkah 1: Asesor Login
1. Login sebagai asesor
2. Masuk ke menu **Pemeriksaan**

### Langkah 2: Pilih Asesi
1. Pilih jadwal ujian
2. Pilih asesi yang akan diperiksa
3. Akan muncul daftar formulir (Bank Soal)

### Langkah 3: Review FR 06
1. Cari formulir **"System Analyst FR 06"** atau FR 06 lainnya
2. Klik tombol **"Review"** pada formulir tersebut
3. Akan muncul form review dengan:
   - Jawaban Asesi (read-only)
   - Field untuk Asesor (editable)

### Langkah 4: Isi TTD Digital Asesor
1. Scroll ke bawah sampai menemukan field **"Tanda Tangan Digital Asesor"** atau **"TTD Asesor"**
2. **Gambar tanda tangan** di signature pad (kotak putih dengan border)
3. Jika salah, klik tombol **"Hapus"** untuk menghapus dan gambar ulang

### Langkah 5: Simpan Review
1. Scroll ke bawah
2. *(Optional)* Centang **"Tandai sebagai sudah selesai diperiksa"** jika sudah selesai
3. Klik tombol **"Simpan Review"**

### Langkah 6: Generate Dokumen
1. Kembali ke daftar formulir
2. Klik tombol **"Generate"** pada FR 06
3. **TTD asesor sekarang akan muncul sebagai gambar** di dokumen yang di-generate

## Catatan Penting
- TTD asesor **harus diisi dan disimpan terlebih dahulu** sebelum generate dokumen
- Jika TTD tidak tersimpan, cek apakah:
  - Sudah menggambar di signature pad (bukan hanya klik)
  - Sudah klik tombol "Simpan Review"
  - Field signature pad untuk asesor ada di Bank Soal configuration

## Troubleshooting
Jika TTD masih tidak muncul setelah mengikuti langkah di atas:
1. Cek konfigurasi Bank Soal FR 06 di menu Admin
2. Pastikan ada field dengan:
   - Type: `signature_pad`
   - Name: `ttd_digital_asesor` atau `ttd_asesor`
   - Role: `asesor`
3. Cek console browser apakah ada error JavaScript
4. Cek log Laravel apakah ada error saat save review

