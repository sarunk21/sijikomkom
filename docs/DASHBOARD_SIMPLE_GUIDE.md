# 📊 Panduan Dashboard SIJIKOMKOM (Versi Simpel)

> Penjelasan mudah untuk presentasi - Bagaimana dashboard menghitung datanya

---

## 🎯 Daftar Isi
- [Dashboard Admin](#-dashboard-admin)
- [Dashboard Asesi](#-dashboard-asesi)
- [Dashboard Asesor](#-dashboard-asesor)
- [Dashboard Kaprodi](#-dashboard-kaprodi)
- [Dashboard Pimpinan](#-dashboard-pimpinan)
- [Dashboard TUK](#-dashboard-tuk)
- [Rumus-Rumus Penting](#-rumus-rumus-penting)

---

## 👨‍💼 Dashboard Admin

### 📈 Yang Ditampilkan

**Angka-Angka Utama (KPI)**
- **Total Pendaftaran**: Hitung semua pendaftaran yang pernah ada
- **Total Asesi**: Hitung berapa orang yang pernah daftar (1 orang bisa daftar berkali-kali)
- **Total Skema**: Hitung berapa skema sertifikasi yang tersedia
- **Total Asesor**: Hitung berapa asesor yang ada
- **Total Jadwal**: Hitung semua jadwal ujian
- **Jadwal Aktif**: Jadwal yang statusnya "Aktif"

**Metrik Keberhasilan**
- **Total Selesai**: Berapa yang sudah ujian
- **Total Lulus**: Berapa yang kompeten/lulus
- **Pass Rate (Tingkat Kelulusan)**:
  ```
  (Yang Lulus ÷ Yang Ujian) × 100%

  Contoh: 80 lulus dari 100 ujian = 80%
  ```

**Grafik Trend (6 Bulan Terakhir)**
- Hitung berapa pendaftaran per bulan
- Tampilkan dalam bentuk grafik garis/batang

**Top 5 Skema Populer**
- Urutkan skema berdasarkan jumlah pendaftaran terbanyak
- Ambil 5 teratas

**Beban Kerja Asesor (Top 10)**
- Hitung berapa asesi yang ditangani tiap asesor
- Urutkan dari yang paling banyak
- Ambil 10 teratas

**Corong Konversi (Funnel)**
```
100 Pendaftaran
  ↓
90 Diverifikasi (status ≥ 3)
  ↓
85 Selesai Ujian
  ↓
70 Lulus

Conversion Rate = (70 ÷ 100) × 100% = 70%
```

**Growth Rate (Pertumbuhan)**
```
Bulan ini: 120 pendaftaran
Bulan lalu: 100 pendaftaran

Growth = ((120 - 100) ÷ 100) × 100% = +20%
```
- **Positif (+)**: Ada pertumbuhan ✅
- **Negatif (-)**: Ada penurunan ⚠️
- **0**: Stabil

### 🤖 Insight Otomatis (AI Sederhana)

**Analisa Trend:**
- Jika growth > 20%: "Pertumbuhan sangat tinggi! 🚀"
- Jika growth > 0%: "Ada pertumbuhan positif 📈"
- Jika growth < -10%: "Ada penurunan, perlu evaluasi 📉"

**Analisa Beban Kerja:**
- Jika selisih asesor tertinggi-terendah > 50: "Perlu redistribusi asesi ⚠️"
- Jika selisih > 20: "Distribusi kurang seimbang"
- Jika selisih kecil: "Distribusi sudah seimbang ✅"

**Action Items:**
- Jika Pass Rate < 60%: "URGENT! Perlu ditingkatkan 🔴"
- Jika Pass Rate < 75%: "Perlu perhatian 🟡"
- Jika bagus: "Performance bagus! ✅"

---

## 👨‍🎓 Dashboard Asesi (UPGRADED! 🚀)

### 📋 Yang Ditampilkan

**KPI Cards (4 Metrik Utama)**

1. **Total Pendaftaran**
   - Hitung semua pendaftaran saya
   - Tampilkan growth rate vs bulan lalu
   - Contoh: "5 pendaftaran (+25% vs bulan lalu)"

2. **Sertifikat Kompeten**
   - Hitung berapa sertifikat yang saya dapat (status Kompeten)
   - Tampilkan jumlah skema yang diikuti
   - Contoh: "3 Sertifikat dari 4 Skema"

3. **Tingkat Keberhasilan (Pass Rate Personal)**
   - Formula: `(Sertifikat Kompeten ÷ Total Ujian) × 100%`
   - Tampilkan dalam progress bar
   - Contoh: "75% (3 dari 4 ujian lulus)"

4. **Jadwal Mendatang**
   - Hitung jadwal ujian yang akan datang
   - Tampilkan warning jika ada pembayaran pending
   - Contoh: "2 jadwal | ⚠️ 1 Pembayaran Pending"

**Grafik Trend Pendaftaran (6 Bulan)**
```
Jan  Feb  Mar  Apr  Mei  Jun
 1    2    0    1    1    2
```
- Line chart yang smooth
- Menunjukkan pola aktivitas pendaftaran

**Performance per Skema**
```
Web Programming     [████████░░] 80% (4/5 Kompeten)
Database Admin      [██████████] 100% (2/2 Kompeten)
Network Security    [████░░░░░░] 40% (2/5 Kompeten)
```
- Tampilkan semua skema yang pernah diikuti
- Progress bar dengan warna:
  - Hijau (≥ 80%): Bagus
  - Kuning (≥ 60%): Cukup
  - Merah (< 60%): Perlu ditingkatkan

**Timeline Progress Terakhir**
```
✅ Pendaftaran → 15 Jan 2025 10:00
✅ Verifikasi  → 16 Jan 2025 14:30
✅ Jadwal Ujian → 20 Jan 2025 09:00
⏰ Selesai     → Belum selesai

Status Sekarang: Ujian Berlangsung
Skema: Web Programming
```
- Visual timeline dengan checkmarks
- Hijau = completed, Kuning = pending
- Menampilkan status sekarang dengan jelas

**Distribusi Status (Pie Chart)**
```
Menunggu Verifikasi: 2
Menunggu Ujian: 1
Selesai: 3
Total: 6
```
- Donut chart dengan persentase
- Warna berbeda per status

**Jadwal Ujian Mendatang (Detail)**
| Tanggal | Skema | TUK | Countdown |
|---------|-------|-----|-----------|
| 25 Jan 2025 09:00 | Web Programming | Lab 1 | 5 hari lagi ⏰ |
| 28 Jan 2025 13:00 | Database | Lab 2 | 8 hari lagi |

**Riwayat Sertifikasi (5 Terakhir)**
```
✅ Web Programming - 15 Des 2024 - Kompeten
✅ Database Admin - 10 Nov 2024 - Kompeten
❌ Network Security - 5 Okt 2024 - Tidak Kompeten
✅ Office Admin - 20 Sep 2024 - Kompeten
```

**Aktivitas Terbaru (10 Terakhir)**
- Tabel dengan DataTable (bisa search & sort)
- Kolom: Tanggal, Aktivitas, Status (badge warna), Keterangan
- Status badge warna sesuai kondisi:
  - Hijau: Selesai
  - Kuning: Menunggu
  - Merah: Ditolak
  - Biru: Sedang Berlangsung

### 🎯 Fitur Advanced yang Ditambahkan

**1. Growth Tracking**
```
Bulan ini: 2 pendaftaran
Bulan lalu: 1 pendaftaran
Growth: +100% 📈
```

**2. Completion Rate**
```
Completion Rate = (Ujian Selesai ÷ Total Pendaftaran) × 100%

Contoh: 4 selesai dari 6 pendaftaran = 66.7%
```

**3. Countdown untuk Jadwal**
- Otomatis hitung berapa hari lagi ujian
- "5 hari lagi" (kuning)
- "Hari ini!" (merah, bold)
- "Sudah lewat" (abu-abu)

**4. Performance Insights per Skema**
- Lihat skema mana yang pass rate tinggi
- Lihat skema mana yang perlu improvement

**5. Interactive Timeline**
- Visual yang jelas
- Mudah lihat progress sekarang di tahap mana

---

## 👨‍🏫 Dashboard Asesor

### 📊 Yang Ditampilkan

**Statistik Saya**
- **Total Asesi yang Dinilai**: Hitung semua asesi yang sudah saya nilai (sepanjang waktu)
- **Asesi Bulan Ini**: Berapa asesi yang saya nilai bulan ini
- **Perubahan dari Bulan Lalu**:
  ```
  Bulan ini: 15 asesi
  Bulan lalu: 10 asesi
  Perubahan = ((15 - 10) ÷ 10) × 100% = +50%
  ```

- **Tingkat Kelulusan Saya**:
  ```
  (Asesi yang Kompeten ÷ Total yang Dinilai) × 100%
  ```

- **Jadwal Aktif**: Jadwal yang belum saya konfirmasi + yang sedang berlangsung
- **Skema yang Dikuasai**: Berapa skema yang saya bisa nilai
- **Rata-rata Asesi per Jadwal**: Total asesi ÷ Total jadwal

**Grafik Penilaian (6 Bulan)**
```
Bulan | Kompeten | Tidak Kompeten | Pass Rate
Jan   |    20    |       5       |   80%
Feb   |    18    |       7       |   72%
...
```

**Top 5 Skema yang Saya Nilai**
- Hitung berapa asesi per skema
- Ambil 5 skema terbanyak

**Grafik Beban Kerja**
- Hitung berapa asesi per bulan yang saya tangani
- Tampilkan dalam grafik batang

**Jadwal yang Perlu Konfirmasi**
- Tampilkan jadwal yang belum saya konfirmasi kehadiran
- Group berdasarkan jadwal (1 jadwal bisa banyak asesi)

**5 Jadwal Mendatang**
- Tampilkan jadwal yang sudah saya konfirmasi
- Urutkan dari tanggal terdekat
- Ambil 5 teratas

---

## 👨‍🎓 Dashboard Kaprodi

### 📈 Yang Ditampilkan

**Angka Utama**
- **Total Pendaftaran**: Semua pendaftaran
- **Total Asesi**: Berapa mahasiswa yang pernah daftar
- **Total Skema**: Berapa skema tersedia
- **Total Asesor**: Berapa asesor yang ada
- **Menunggu Verifikasi Saya**: Berapa pendaftaran yang statusnya = 1 (Menunggu Verifikasi Kaprodi)

**Metrik Persetujuan**
- **Approval Rate (Tingkat Disetujui)**:
  ```
  Total yang status ≥ 3 (sudah diverifikasi) ÷ Total Pendaftaran × 100%

  Contoh: 85 disetujui dari 100 pendaftaran = 85%
  ```

- **Pass Rate**: Sama seperti Admin
- **Rata-rata Waktu Verifikasi**:
  ```
  Hitung rata-rata berapa hari dari pendaftaran sampai diverifikasi
  Target: < 5 hari
  ```

**Status Pendaftaran**
```
Menunggu Verifikasi (Status 1): 15
Ditolak (Status 2): 5
Diverifikasi (Status 3): 10
Menunggu Ujian (Status 4): 8
...
```

**Grafik Trend Verifikasi**
- Hitung per bulan: berapa yang diverifikasi vs ditolak
- Tampilkan dalam grafik

**Segmentasi Mahasiswa**
```
Laki-laki: 60%
Perempuan: 40%
```

### 🤖 Insight untuk Kaprodi

**Analisa Verifikasi:**
- Jika antrian > 20: "PERHATIAN! Banyak yang menunggu ⚠️"
- Jika antrian > 10: "Workload masih wajar"
- Jika antrian < 10: "Lancar ✅"

**Analisa Performance:**
- Jika Approval Rate ≥ 80%: "Sangat baik ✅"
- Jika Approval Rate ≥ 60%: "Cukup baik"
- Jika < 60%: "Perlu evaluasi persyaratan ⚠️"

**Waktu Verifikasi:**
- Jika > 7 hari: "Terlalu lama! 🔴"
- Jika > 5 hari: "Bisa dipercepat 🟡"
- Jika < 5 hari: "Bagus! ✅"

---

## 👔 Dashboard Pimpinan

### 📊 Yang Ditampilkan (Executive View)

**KPI Tingkat Tinggi**
- **Total Pendaftaran**: Semua pendaftaran sepanjang waktu
- **Total Asesi**: Berapa mahasiswa yang ikut program
- **Total Skema**: Berapa skema sertifikasi
- **Total Asesor**: Berapa asesor
- **Total Jadwal**: Semua jadwal
- **Total TUK**: Berapa tempat ujian
- **Pass Rate**: Tingkat kelulusan keseluruhan
- **Utilisasi Kapasitas**:
  ```
  (Jadwal yang Terisi ÷ Total Jadwal) × 100%

  Contoh: 80 jadwal terisi dari 100 jadwal = 80%
  Target: > 70%
  ```

**Grafik Trend (12 Bulan!)**
- Lebih panjang dari role lain
- Untuk melihat trend jangka panjang

**Grafik Pass Rate per Bulan**
```
Bulan | Total Ujian | Kompeten | Pass Rate
Jan   |     100     |    85    |   85%
Feb   |     120     |    90    |   75%
...
```

**Top 5 Skema Terbaik**
- Urutkan berdasarkan Pass Rate tertinggi
- Filter: minimal 3 ujian (biar valid)

**Trend per Skema (Top 3)**
- Ambil 3 skema terpopuler
- Tampilkan grafik pendaftaran 6 bulan untuk masing-masing

**Distribusi Beban Asesor**
```
1-10 Asesi:    15 asesor
11-20 Asesi:   10 asesor
21-30 Asesi:    5 asesor
31-40 Asesi:    2 asesor
40+ Asesi:      1 asesor  ⚠️ (Perlu perhatian)
```

**Rata-rata Waktu Pendaftaran → Ujian**
```
Hitung rata-rata berapa hari dari daftar sampai ujian
Target: < 30 hari
```

**Pipeline Status**
```
Menunggu Verifikasi → Diverifikasi → Menunggu Ujian → Ujian → Selesai
     15           →      10       →       8        →   5   →    3
```

**Demografi**
- Persentase Laki-laki vs Perempuan

### 🎯 Executive Insights

**Pass Rate:**
- ≥ 90%: "EXCELLENT! 🏆"
- ≥ 75%: "GOOD ✅"
- < 75%: "Perlu Perhatian ⚠️"

**Growth:**
- > 20%: "Pertumbuhan Signifikan! 🚀"
- < -10%: "Penurunan, perlu strategi 📉"

**Kapasitas:**
- < 60%: "Utilisasi rendah, optimalkan ⚠️"
- > 90%: "Hampir penuh, tambah kapasitas 📈"

**Efisiensi:**
- Waktu > 30 hari: "Proses terlalu lama ⏰"

**Workload:**
- Ada asesor 40+ asesi: "Perlu redistribusi ⚖️"

---

## 🏢 Dashboard TUK

### 📋 Yang Ditampilkan

**Statistik TUK Saya**
- **Total Jadwal**: Semua jadwal di TUK ini
- **Jadwal Aktif**: Yang statusnya "Aktif"
- **Jadwal Hari Ini**: Jadwal ujian hari ini
- **Jadwal Selesai**: Yang sudah selesai
- **Total Asesi**: Berapa peserta di TUK ini

**Grafik Trend Jadwal (6 Bulan)**
- Hitung berapa jadwal per bulan

**Skema di TUK Ini**
```
Web Programming:     20 jadwal
Database:            15 jadwal
Network Admin:       12 jadwal
...
```

**Kalender Minggu Ini**
```
Senin    (10 Jan) | 3 jadwal | Selesai
Selasa   (11 Jan) | 2 jadwal | Selesai
Rabu     (12 Jan) | 5 jadwal | Hari Ini ⭐
Kamis    (13 Jan) | 4 jadwal | Akan Datang
Jumat    (14 Jan) | 1 jadwal | Akan Datang
Sabtu    (15 Jan) | 0 jadwal | -
Minggu   (16 Jan) | 0 jadwal | -
```

**5 Jadwal Terdekat**
- Tampilkan jadwal mendatang
- Urutkan dari tanggal terdekat

**Status Jadwal**
```
Pending:            5
Aktif:             10
Ditunda:            2
Sedang Berlangsung: 3
Selesai:           50
```

---

## 🧮 Rumus-Rumus Penting

### 1. Pass Rate (Tingkat Kelulusan)
```
Pass Rate = (Yang Lulus ÷ Total Ujian) × 100%

Contoh:
80 orang lulus dari 100 orang ujian
= (80 ÷ 100) × 100%
= 80%
```

### 2. Growth Rate (Pertumbuhan)
```
Growth Rate = ((Sekarang - Sebelumnya) ÷ Sebelumnya) × 100%

Contoh:
Bulan ini: 120, Bulan lalu: 100
= ((120 - 100) ÷ 100) × 100%
= 20%

Arti:
+20% = Naik 20% (Bagus! 📈)
-20% = Turun 20% (Perlu perhatian 📉)
```

### 3. Approval Rate (Tingkat Disetujui)
```
Approval Rate = (Yang Disetujui ÷ Total Pendaftaran) × 100%

Contoh:
85 disetujui dari 100 pendaftaran
= (85 ÷ 100) × 100%
= 85%
```

### 4. Conversion Rate (Tingkat Konversi)
```
Conversion Rate = (Yang Lulus ÷ Total Pendaftaran) × 100%

Contoh:
70 lulus dari 100 pendaftaran
= (70 ÷ 100) × 100%
= 70%

Artinya: 70% dari yang daftar berhasil lulus
```

### 5. Utilisasi Kapasitas
```
Utilisasi = (Jadwal Terisi ÷ Total Jadwal) × 100%

Contoh:
75 jadwal terisi dari 100 jadwal
= (75 ÷ 100) × 100%
= 75%

Target: > 70%
```

### 6. Rata-rata (Average)
```
Rata-rata = Total ÷ Jumlah Item

Contoh:
Asesor A: 10 asesi, B: 20 asesi, C: 15 asesi
Rata-rata = (10 + 20 + 15) ÷ 3 = 15 asesi
```

---

## 📊 Status-Status Penting

### Status Pendaftaran
| Nomor | Status                         | Artinya                           |
|-------|--------------------------------|-----------------------------------|
| 1     | Menunggu Verifikasi Kaprodi    | Baru daftar, belum dicek Kaprodi  |
| 2     | Ditolak                        | Ditolak Kaprodi                   |
| 3     | Menunggu Verifikasi Admin      | Lolos Kaprodi, menunggu Admin     |
| 4     | Menunggu Ujian                 | Sudah fix, tunggu tanggal ujian   |
| 5     | Ujian Berlangsung              | Sedang ujian                      |
| 6     | Selesai                        | Ujian sudah selesai               |
| 7     | Asesor Tidak Hadir             | Asesor batal, perlu cari pengganti|

### Status Report (Hasil Ujian)
| Nomor | Status         | Artinya       |
|-------|----------------|---------------|
| 0     | Tidak Kompeten | Tidak Lulus ❌ |
| 1     | Kompeten       | Lulus ✅       |
| 2     | Tidak Kompeten | Tidak Lulus ❌ |

### Status Jadwal
| Nomor | Status             | Artinya                    |
|-------|--------------------|----------------------------|
| 0     | Pending            | Belum aktif                |
| 1     | Aktif              | Jadwal aktif, bisa dipakai |
| 2     | Ditunda            | Ditunda sementara          |
| 3     | Sedang Berlangsung | Ujian sedang jalan         |
| 4     | Selesai            | Ujian sudah selesai        |

---

## 🎯 Cara Baca Dashboard (Tips Presentasi)

### Dashboard Admin
**Fokus**: Operasional keseluruhan
- "Berapa total pendaftaran?"
- "Apakah ada pertumbuhan?"
- "Berapa yang lulus?"
- "Asesor mana yang paling sibuk?"

### Dashboard Asesi
**Fokus**: Data pribadi mahasiswa
- "Berapa kali saya daftar?"
- "Kapan jadwal ujian saya?"
- "Apakah saya sudah lulus?"

### Dashboard Asesor
**Fokus**: Kinerja dan jadwal asesor
- "Berapa asesi yang sudah saya nilai?"
- "Berapa persen yang lulus?"
- "Jadwal mana yang perlu saya konfirmasi?"

### Dashboard Kaprodi
**Fokus**: Verifikasi dan approval
- "Berapa yang menunggu verifikasi?"
- "Berapa persen yang disetujui?"
- "Berapa lama waktu verifikasi?"

### Dashboard Pimpinan
**Fokus**: Strategic overview
- "Bagaimana pertumbuhan program?"
- "Apakah kualitas meningkat (Pass Rate)?"
- "Apakah kapasitas sudah optimal?"
- "Skema mana yang paling sukses?"

### Dashboard TUK
**Fokus**: Jadwal dan tempat ujian
- "Berapa jadwal di TUK ini?"
- "Jadwal apa yang hari ini?"
- "Skema apa yang paling sering di TUK ini?"

---

## 💡 Insight Cepat (Cheat Sheet)

### Angka Bagus ✅
- **Pass Rate**: > 75%
- **Growth Rate**: > 0% (positif)
- **Approval Rate**: > 80%
- **Utilisasi Kapasitas**: 70-90%
- **Waktu Verifikasi**: < 5 hari
- **Waktu Pendaftaran → Ujian**: < 30 hari
- **Conversion Rate**: > 60%

### Angka Perlu Perhatian ⚠️
- **Pass Rate**: < 60%
- **Growth Rate**: < -10% (turun banyak)
- **Approval Rate**: < 60%
- **Utilisasi Kapasitas**: < 50%
- **Waktu Verifikasi**: > 7 hari
- **Gap Workload Asesor**: > 50 asesi

---

## 📈 Contoh Interpretasi

### Contoh 1: Dashboard Admin
```
Pass Rate: 82%
Growth Rate: +15%
Conversion Rate: 68%

Interpretasi:
✅ Pass Rate bagus (> 75%)
✅ Ada pertumbuhan positif
✅ Conversion cukup baik

Kesimpulan: Program berjalan dengan baik!
```

### Contoh 2: Dashboard Kaprodi
```
Menunggu Verifikasi: 25
Approval Rate: 88%
Avg Waktu Verifikasi: 4.2 hari

Interpretasi:
⚠️ Antrian cukup banyak (> 20)
✅ Tingkat approval sangat baik
✅ Waktu verifikasi masih di target

Kesimpulan: Perlu percepat verifikasi, tapi kualitas sudah bagus
```

### Contoh 3: Dashboard Pimpinan
```
Utilisasi Kapasitas: 45%
Pass Rate: 78%
Growth Rate: -5%

Interpretasi:
⚠️ Utilisasi rendah (< 60%)
✅ Pass Rate bagus
⚠️ Ada penurunan pendaftaran

Kesimpulan: Kualitas bagus, tapi perlu strategi marketing untuk tingkatkan pendaftaran
```

---

**📌 Tips Presentasi:**
1. Mulai dari angka-angka besar (KPI)
2. Tunjukkan grafik trend
3. Highlight angka yang bagus ✅
4. Jelaskan angka yang perlu perhatian ⚠️
5. Berikan rekomendasi aksi

**Selamat Presentasi! 🎉**
