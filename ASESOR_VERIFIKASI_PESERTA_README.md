# Fitur Verifikasi Peserta - Asesor

## Deskripsi
Fitur ini memungkinkan asesor untuk melihat jadwal ujian yang ditugaskan kepada mereka dan melihat detail asesi yang akan diuji.

## Fitur Utama

### 1. List Jadwal Ujian
- Menampilkan daftar jadwal ujian yang ditugaskan kepada asesor yang login
- Data diambil dari tabel `pendaftaran_ujikom` dengan `asesor_id` sesuai user yang login
- Menampilkan informasi: Skema, TUK, Tanggal Ujian, Status

### 2. List Asesi per Jadwal
- Menampilkan daftar asesi untuk jadwal tertentu
- Detail jadwal lengkap (skema, TUK, tanggal, kuota, jumlah asesi)
- Informasi asesi: nama, NIM, telepon, status, keterangan

## File yang Dibuat/Dimodifikasi

### Controller
- **File**: `app/Http/Controllers/Asesor/VerifikasiPesertaController.php`
- **Method**:
  - `index()` - List jadwal
  - `showAsesi($jadwalId)` - List asesi per jadwal

### Views
- **File**: `resources/views/components/pages/asesor/verifikasi-peserta/list.blade.php`
  - Diupdate untuk menampilkan list jadwal
- **File**: `resources/views/components/pages/asesor/verifikasi-peserta/asesi-list.blade.php`
  - View untuk menampilkan list asesi (read-only)

### Model
- **File**: `app/Models/PendaftaranUjikom.php`
  - Diupdate relasi untuk membedakan asesi dan asesor

### Routes
- **File**: `routes/web.php`
  - Route untuk show asesi

## Status Pendaftaran Ujikom
- **Status 1**: Belum Ujikom
- **Status 2**: Ujikom Berlangsung
- **Status 3**: Ujikom Selesai
- **Status 4**: Tidak Kompeten
- **Status 5**: Kompeten
- **Status 6**: Menunggu Konfirmasi Asesor
- **Status 7**: Asesor Tidak Dapat Hadir

## Cara Penggunaan

### 1. Login sebagai Asesor
- Email: `asesor@test.com`
- Password: `password`

### 2. Akses Menu Verifikasi Peserta
- Klik menu "Verifikasi Peserta" di sidebar asesor
- Akan muncul list jadwal ujian yang ditugaskan

### 3. Lihat List Asesi
- Klik tombol "Lihat Asesi" pada jadwal tertentu
- Akan muncul detail jadwal dan list asesi
- Halaman ini hanya untuk melihat informasi (read-only)

## Workflow

### 1. Asesor Login
- Sistem mengambil `asesor_id` dari user yang login
- Menampilkan jadwal yang ditugaskan kepada asesor tersebut

### 2. Pilih Jadwal
- Asesor memilih jadwal tertentu
- Sistem menampilkan list asesi untuk jadwal tersebut

### 3. Lihat Detail Asesi
- Asesor dapat melihat informasi lengkap asesi
- Informasi yang ditampilkan: nama, NIM, telepon, status, keterangan

## Informasi yang Ditampilkan

### Detail Jadwal
- Skema ujian
- TUK (Tempat Uji Kompetensi)
- Tanggal ujian dan selesai
- Status jadwal
- Kuota dan jumlah asesi

### Detail Asesi
- Nama asesi
- NIM
- Nomor telepon
- Status pendaftaran ujikom
- Keterangan (jika ada)

## UI/UX Features
- **Responsive Design**: Tabel responsive dengan DataTables
- **Status Badge**: Warna berbeda untuk setiap status
- **Clean Layout**: Tampilan yang bersih dan mudah dibaca
- **Search & Filter**: Kemampuan pencarian dan filter data

## Security
- Validasi `asesor_id` untuk memastikan hanya asesor yang berhak
- Hanya menampilkan data yang relevan dengan asesor yang login

## Troubleshooting

### Data tidak muncul
1. Pastikan ada data di tabel `pendaftaran_ujikom`
2. Pastikan `asesor_id` sesuai dengan user yang login
3. Jalankan seeder untuk testing data

### Error saat akses
1. Cek log Laravel: `tail -f storage/logs/laravel.log`
2. Pastikan user memiliki role asesor
3. Pastikan ada jadwal yang ditugaskan kepada asesor

### Tabel tidak responsive
1. Pastikan DataTables CSS dan JS sudah dimuat
2. Cek console browser untuk error JavaScript
3. Pastikan jQuery sudah dimuat sebelum script custom
