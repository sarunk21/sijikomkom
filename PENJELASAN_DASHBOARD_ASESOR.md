# 📊 Dokumentasi Dashboard Asesor - Advanced Analytics

## Overview
Dashboard Asesor dirancang dengan **advanced data analytics** yang mudah dipahami untuk memberikan insight mendalam tentang performa dan aktivitas asesor dalam sistem uji kompetensi.

---

## 🎯 KPI Metrics (Key Performance Indicators)

### 1. **Total Asesi Dinilai**
- **Deskripsi**: Total asesi yang telah dinilai sejak awal (lifetime)
- **Perhitungan**:
  ```sql
  SELECT COUNT(*)
  FROM pendaftaran_ujikom
  WHERE asesor_id = [ID_ASESOR]
    AND status = 5  -- 5 = Kompeten (selesai dinilai)
  ```
- **Manfaat**: Mengukur produktivitas total asesor
- **Presentasi**: "Ini menunjukkan total kontribusi asesor dalam menilai asesi"

### 2. **Asesi Bulan Ini**
- **Deskripsi**: Jumlah asesi yang dinilai bulan berjalan
- **Perhitungan**:
  ```sql
  SELECT COUNT(*)
  FROM pendaftaran_ujikom
  WHERE asesor_id = [ID_ASESOR]
    AND status = 5
    AND MONTH(updated_at) = MONTH(CURRENT_DATE)
    AND YEAR(updated_at) = YEAR(CURRENT_DATE)
  ```
- **Perubahan dari Bulan Lalu**:
  ```php
  $perubahanAsesi = ($asesiBulanIni - $asesiBulanLalu) / $asesiBulanLalu * 100
  ```
- **Manfaat**: Tracking performa bulanan dengan tren pertumbuhan
- **Presentasi**: "Menampilkan produktivitas terkini dengan indikator pertumbuhan (+/-)"

### 3. **Tingkat Kelulusan (Pass Rate)**
- **Deskripsi**: Persentase asesi yang dinilai kompeten
- **Perhitungan**:
  ```php
  $tingkatKelulusan = ($totalKompeten / $totalPenilaian) * 100
  ```
  ```sql
  -- Total Kompeten
  SELECT COUNT(*)
  FROM report r
  JOIN pendaftaran p ON r.pendaftaran_id = p.id
  JOIN pendaftaran_ujikom pu ON p.id = pu.pendaftaran_id
  WHERE pu.asesor_id = [ID_ASESOR]
    AND r.status = 1  -- 1 = Kompeten

  -- Total Penilaian
  SELECT COUNT(*)
  FROM report r
  JOIN pendaftaran p ON r.pendaftaran_id = p.id
  JOIN pendaftaran_ujikom pu ON p.id = pu.pendaftaran_id
  WHERE pu.asesor_id = [ID_ASESOR]
  ```
- **Manfaat**: Mengukur kualitas penilaian asesor
- **Presentasi**: "Metrik ini menunjukkan seberapa konsisten asesor dalam menilai kompetensi asesi"

### 4. **Jadwal Aktif**
- **Deskripsi**: Jumlah jadwal yang perlu dikonfirmasi + sedang berlangsung
- **Perhitungan**:
  ```sql
  SELECT COUNT(DISTINCT jadwal_id)
  FROM pendaftaran_ujikom pu
  LEFT JOIN jadwal j ON pu.jadwal_id = j.id
  WHERE pu.asesor_id = [ID_ASESOR]
    AND (
      pu.asesor_confirmed = FALSE  -- Belum konfirmasi
      OR j.status = 3              -- Atau sedang berlangsung
    )
  ```
- **Manfaat**: Monitoring beban kerja aktif
- **Presentasi**: "Memberikan visibility terhadap jadwal yang memerlukan perhatian asesor"

---

## 📈 Secondary KPIs

### 5. **Skema Dikuasai**
- **Perhitungan**:
  ```sql
  SELECT COUNT(*)
  FROM asesor_skema
  WHERE asesor_id = [ID_ASESOR]
  ```
- **Manfaat**: Menunjukkan kompetensi dan keahlian asesor

### 6. **Avg Waktu Penilaian**
- **Deskripsi**: Rata-rata waktu dari assignment sampai selesai penilaian (dalam jam)
- **Perhitungan**:
  ```sql
  SELECT AVG(TIMESTAMPDIFF(HOUR, pu.created_at, r.updated_at))
  FROM report r
  JOIN pendaftaran p ON r.pendaftaran_id = p.id
  JOIN pendaftaran_ujikom pu ON p.id = pu.pendaftaran_id
  WHERE pu.asesor_id = [ID_ASESOR]
    AND r.updated_at IS NOT NULL
  ```
- **Manfaat**: Mengukur efisiensi kerja asesor
- **Presentasi**: "Metrik efisiensi - semakin rendah semakin baik (tanpa mengorbankan kualitas)"

### 7. **Total Jadwal Selesai**
- **Deskripsi**: Jumlah jadwal ujikom yang sudah selesai dinilai
- **Perhitungan**:
  ```sql
  SELECT COUNT(DISTINCT jadwal_id)
  FROM pendaftaran_ujikom
  WHERE asesor_id = [ID_ASESOR]
    AND status = 5  -- 5 = Kompeten (selesai dinilai)
  ```
- **Manfaat**: Mengukur total jadwal yang sudah diselesaikan asesor
- **Presentasi**: "Metrik ini menunjukkan berapa kali asesor menyelesaikan sesi ujikom"

### 8. **Rata-rata Asesi per Jadwal**
- **Deskripsi**: Rata-rata jumlah asesi yang dinilai per jadwal
- **Perhitungan**:
  ```php
  $avgAsesiPerJadwal = $totalAsesiDinilai / $totalJadwalSelesai
  ```
- **Manfaat**: Mengukur beban kerja rata-rata asesor per sesi ujikom
- **Presentasi**: "Indikator efisiensi yang menunjukkan kapasitas asesor dalam menilai per sesi"

---

## 📊 Advanced Analytics & Visualizations

### 1. **Trend Penilaian (Line Chart)**
- **Tipe**: Line Chart (6 bulan terakhir)
- **Data**:
  - **Garis Hijau**: Jumlah asesi kompeten per bulan
  - **Garis Merah**: Jumlah asesi tidak kompeten per bulan
- **Perhitungan**:
  ```php
  for ($i = 5; $i >= 0; $i--) {
      $bulan = now()->subMonths($i);

      // Kompeten
      $kompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', ...)
          ->where('status', 1)
          ->whereMonth('created_at', $bulan->month)
          ->whereYear('created_at', $bulan->year)
          ->count();

      // Tidak Kompeten
      $tidakKompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', ...)
          ->where('status', 0)
          ->whereMonth('created_at', $bulan->month)
          ->whereYear('created_at', $bulan->year)
          ->count();
  }
  ```
- **Insight**: Melihat pola penilaian dari waktu ke waktu
- **Presentasi**: "Grafik ini menunjukkan konsistensi penilaian asesor dan dapat mendeteksi anomali"

### 2. **Ringkasan Performa (Doughnut Chart)**
- **Tipe**: Doughnut/Pie Chart
- **Data**:
  - Hijau: Total Kompeten (%)
  - Merah: Total Tidak Kompeten (%)
- **Manfaat**: Visualisasi proporsi hasil penilaian secara keseluruhan
- **Presentasi**: "Distribusi hasil penilaian dalam bentuk persentase untuk evaluasi cepat"

### 3. **Top 5 Skema yang Dinilai (Horizontal Bar Chart)**
- **Tipe**: Horizontal Bar Chart
- **Perhitungan**:
  ```sql
  SELECT s.nama, COUNT(*) as jumlah
  FROM pendaftaran_ujikom pu
  JOIN pendaftaran p ON pu.pendaftaran_id = p.id
  JOIN skema s ON p.skema_id = s.id
  WHERE pu.asesor_id = [ID_ASESOR]
    AND pu.status = 5
  GROUP BY s.nama
  ORDER BY jumlah DESC
  LIMIT 5
  ```
- **Insight**: Identifikasi area keahlian utama asesor
- **Presentasi**: "Menunjukkan skema yang paling dikuasai asesor berdasarkan pengalaman penilaian"

### 4. **Analisis Beban Kerja (Bar Chart)**
- **Tipe**: Bar Chart (6 bulan terakhir)
- **Perhitungan**:
  ```php
  for ($i = 5; $i >= 0; $i--) {
      $bulan = now()->subMonths($i);
      $count = PendaftaranUjikom::where('asesor_id', ...)
          ->whereMonth('created_at', $bulan->month)
          ->whereYear('created_at', $bulan->year)
          ->count();
  }
  ```
- **Insight**: Tren beban kerja untuk capacity planning
- **Presentasi**: "Membantu asesor dan admin merencanakan distribusi workload yang optimal"

---

## 📅 Jadwal Ujikom Mendatang

### Data yang Ditampilkan:
- Tanggal & Waktu Ujian
- Skema Sertifikasi
- Tempat Uji Kompetensi (TUK)
- Jumlah Asesi
- Status Konfirmasi

### Query:
```sql
SELECT DISTINCT j.*, s.nama as skema, t.nama as tuk,
       COUNT(pu.id) as jumlah_asesi
FROM pendaftaran_ujikom pu
JOIN jadwal j ON pu.jadwal_id = j.id
JOIN skema s ON j.skema_id = s.id
JOIN tuk t ON j.tuk_id = t.id
WHERE pu.asesor_id = [ID_ASESOR]
  AND pu.asesor_confirmed = TRUE
  AND j.status = 1
  AND j.tanggal_ujian >= CURRENT_DATE
GROUP BY j.id
ORDER BY j.tanggal_ujian ASC
LIMIT 5
```

---

## 🎨 UI/UX Features

### 1. **Gradient Cards**
- Menggunakan gradient modern untuk KPI utama
- Warna berbeda untuk setiap kategori:
  - **Purple**: Total Asesi Dinilai
  - **Green**: Asesi Bulan Ini
  - **Blue**: Tingkat Kelulusan
  - **Pink**: Jadwal Aktif

### 2. **Responsive Charts**
- Menggunakan Chart.js 3.9.1
- Responsive dan mobile-friendly
- Interactive tooltips

### 3. **Alert System**
- Warning alert untuk pending confirmations
- Link langsung ke halaman Review & Verifikasi

---

## 📌 Penjelasan untuk Presentasi

### Slide 1: Overview
> "Dashboard Asesor kami dirancang dengan **advanced analytics** yang mudah dipahami. Menampilkan 8 KPI utama dan 4 visualisasi data untuk memberikan insight komprehensif tentang performa asesor."

### Slide 2: KPI Metrics
> "Kami menggunakan **8 metrik utama**:
> 1. **Total Asesi Dinilai** - Produktivitas lifetime
> 2. **Asesi Bulan Ini** - Tracking bulanan dengan tren pertumbuhan
> 3. **Tingkat Kelulusan** - Quality metric (pass rate)
> 4. **Jadwal Aktif** - Workload monitoring
> 5. **Skema Dikuasai** - Kompetensi dan keahlian
> 6. **Avg Waktu Penilaian** - Efisiensi kerja
> 7. **Total Jadwal Selesai** - Jumlah sesi ujikom yang diselesaikan
> 8. **Avg Asesi per Jadwal** - Kapasitas per sesi"

### Slide 3: Advanced Analytics
> "4 visualisasi data interaktif:
> - **Trend Penilaian**: Line chart untuk melihat pola 6 bulan terakhir
> - **Ringkasan Performa**: Pie chart untuk proporsi hasil
> - **Top 5 Skema**: Horizontal bar untuk area keahlian
> - **Analisis Workload**: Bar chart untuk capacity planning"

### Slide 4: Value Proposition
> "Keuntungan dashboard ini:
> ✅ **Real-time insights** - Data selalu update
> ✅ **Easy to understand** - Visualisasi intuitif
> ✅ **Actionable metrics** - Mendukung decision making
> ✅ **Performance tracking** - Monitor KPI dari waktu ke waktu"

---

## 🔧 Technical Implementation

### Backend (Laravel):
- Query optimization dengan eager loading
- Menggunakan DB facade untuk query kompleks
- Caching untuk performa (bisa ditambahkan)

### Frontend:
- Bootstrap 4 untuk responsive layout
- Chart.js untuk visualisasi
- Gradient CSS untuk modern UI

### Performance:
- Semua query sudah dioptimasi
- Menggunakan aggregation di database level
- Minimal N+1 query problem

---

## 📝 Notes
- Semua perhitungan menggunakan **data real-time** dari database
- Tidak ada data hardcode
- Formula dapat disesuaikan sesuai kebutuhan bisnis
- Dashboard dapat di-extend dengan metrics tambahan
