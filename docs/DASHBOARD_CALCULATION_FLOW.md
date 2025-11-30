# Dokumentasi Alur Perhitungan Dashboard SIJIKOMKOM

Dokumentasi ini menjelaskan secara detail bagaimana perhitungan dilakukan untuk setiap metrik dan grafik yang ditampilkan di dashboard berdasarkan role pengguna.

## Daftar Isi
- [1. Dashboard Admin](#1-dashboard-admin)
- [2. Dashboard Asesi](#2-dashboard-asesi)
- [3. Dashboard Asesor](#3-dashboard-asesor)
- [4. Dashboard Kaprodi](#4-dashboard-kaprodi)
- [5. Dashboard Pimpinan](#5-dashboard-pimpinan)
- [6. Dashboard TUK](#6-dashboard-tuk)
- [7. Analytics Service](#7-analytics-service)

---

## 1. Dashboard Admin

**Controller**: `App\Http\Controllers\Admin\DashboardController`

### 1.1 Key Performance Indicators (KPIs)

#### Total Pendaftaran
```php
$totalPendaftaran = Pendaftaran::count();
```
- **Sumber Data**: Tabel `pendaftaran`
- **Perhitungan**: Menghitung semua record dalam tabel pendaftaran (tanpa filter)

#### Total Asesi
```php
$totalAsesi = Pendaftaran::distinct('user_id')->count('user_id');
```
- **Sumber Data**: Tabel `pendaftaran`
- **Perhitungan**: Menghitung jumlah user_id unik dari pendaftaran

#### Total Skema Sertifikasi
```php
$totalSkema = Skema::count();
```
- **Sumber Data**: Tabel `skema`
- **Perhitungan**: Menghitung semua skema yang tersedia

#### Total Asesor Aktif
```php
$totalAsesor = User::where('user_type', 'asesor')->count();
```
- **Sumber Data**: Tabel `users`
- **Perhitungan**: Menghitung user dengan user_type = 'asesor'

#### Total Jadwal
```php
$totalJadwal = Jadwal::count();
```
- **Sumber Data**: Tabel `jadwal`
- **Perhitungan**: Menghitung semua jadwal (tanpa filter)

#### Jadwal Aktif
```php
$jadwalAktif = Jadwal::where('status', 1)->count();
```
- **Sumber Data**: Tabel `jadwal`
- **Perhitungan**: Menghitung jadwal dengan status = 1 (Aktif)

### 1.2 Success Metrics

#### Total Selesai
```php
$totalSelesai = Pendaftaran::where('status', 6)->count();
```
- **Sumber Data**: Tabel `pendaftaran`
- **Perhitungan**: Menghitung pendaftaran dengan status = 6 (Selesai)

#### Total Lulus/Kompeten
```php
$totalLulus = Report::where('status', 1)->count();
```
- **Sumber Data**: Tabel `report`
- **Perhitungan**: Menghitung report dengan status = 1 (Kompeten)

#### Tingkat Keberhasilan (Pass Rate)
```php
$passRate = $totalSelesai > 0 ? round(($totalLulus / $totalSelesai) * 100, 1) : 0;
```
- **Formula**: `(Total Lulus / Total Selesai) × 100`
- **Satuan**: Persentase (%)
- **Pembulatan**: 1 desimal

### 1.3 Trend Analysis (6 Bulan Terakhir)

```php
$trenPendaftaran = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $count = Pendaftaran::whereMonth('created_at', $bulan->month)
        ->whereYear('created_at', $bulan->year)
        ->count();
    $trenPendaftaran[] = [
        'bulan' => $bulan->format('M Y'),
        'jumlah' => $count
    ];
}
```
- **Periode**: 6 bulan terakhir
- **Perhitungan**: Loop dari 5 bulan lalu sampai bulan ini
- **Output**: Array berisi bulan dan jumlah pendaftaran per bulan

### 1.4 Distribusi Skema (Top 5)

```php
$distribusiSkema = Pendaftaran::select('skema_id', DB::raw('count(*) as total'))
    ->with('skema')
    ->groupBy('skema_id')
    ->orderBy('total', 'desc')
    ->take(5)
    ->get()
    ->mapWithKeys(function ($item) {
        return [$item->skema->nama => $item->total];
    });
```
- **Sumber Data**: Tabel `pendaftaran` JOIN `skema`
- **Perhitungan**: GROUP BY skema_id, ORDER BY total DESC, LIMIT 5
- **Output**: Key-value pair (nama skema => jumlah pendaftaran)

### 1.5 Workload Asesor (Top 10)

```php
$workloadAsesor = PendaftaranUjikom::select('asesor_id', DB::raw('count(*) as total_asesi'))
    ->with('asesor')
    ->groupBy('asesor_id')
    ->orderBy('total_asesi', 'desc')
    ->take(10)
    ->get()
    ->map(function ($item) {
        return [
            'nama' => $item->asesor->name ?? 'Unknown',
            'total' => $item->total_asesi
        ];
    });
```
- **Sumber Data**: Tabel `pendaftaran_ujikom` JOIN `users`
- **Perhitungan**: GROUP BY asesor_id, COUNT asesi, ORDER BY DESC, LIMIT 10
- **Output**: Nama asesor dan total asesi yang ditangani

### 1.6 Conversion Funnel

```php
$funnelData = [
    'pendaftaran' => Pendaftaran::count(),
    'diverifikasi' => Pendaftaran::whereIn('status', [3, 4, 5, 6])->count(),
    'ujian_selesai' => Pendaftaran::where('status', 6)->count(),
    'lulus' => Report::where('status', 1)->count(),
];

$conversionRate = $funnelData['pendaftaran'] > 0
    ? round(($funnelData['lulus'] / $funnelData['pendaftaran']) * 100, 1)
    : 0;
```
- **Tahapan Funnel**:
  1. Pendaftaran: Semua pendaftaran
  2. Diverifikasi: Status >= 3 (Menunggu Verifikasi Admin, Menunggu Ujian, Ujian Berlangsung, Selesai)
  3. Ujian Selesai: Status = 6
  4. Lulus: Report dengan status = 1
- **Conversion Rate**: `(Lulus / Total Pendaftaran) × 100`

### 1.7 Top Performing Skema

```php
$topSkema = Report::select('report.skema_id',
        DB::raw('COUNT(*) as total_ujian'),
        DB::raw('SUM(CASE WHEN report.status = 1 THEN 1 ELSE 0 END) as total_lulus'))
    ->with('skema')
    ->groupBy('report.skema_id')
    ->havingRaw('COUNT(*) >= 5')
    ->get()
    ->map(function ($item) {
        $passRate = $item->total_ujian > 0
            ? round(($item->total_lulus / $item->total_ujian) * 100, 1)
            : 0;
        return [
            'nama' => $item->skema->nama ?? 'Unknown',
            'total_ujian' => $item->total_ujian,
            'total_lulus' => $item->total_lulus,
            'pass_rate' => $passRate
        ];
    })
    ->sortByDesc('pass_rate')
    ->take(5);
```
- **Filter**: Minimal 5 ujian per skema
- **Perhitungan Pass Rate**: `(Total Lulus / Total Ujian) × 100`
- **Sorting**: Berdasarkan pass_rate tertinggi
- **Output**: Top 5 skema dengan pass rate terbaik

### 1.8 Statistik Status Pendaftaran

```php
$statusStats = [
    'menunggu_verifikasi' => Pendaftaran::where('status', 1)->count(),
    'ditolak' => Pendaftaran::where('status', 2)->count(),
    'diverifikasi' => Pendaftaran::where('status', 3)->count(),
    'menunggu_ujian' => Pendaftaran::where('status', 4)->count(),
    'ujian_berlangsung' => Pendaftaran::where('status', 5)->count(),
    'selesai' => Pendaftaran::where('status', 6)->count(),
    'asesor_tidak_hadir' => Pendaftaran::where('status', 7)->count(),
];
```
- **Mapping Status**:
  - 1: Menunggu Verifikasi Kaprodi
  - 2: Ditolak
  - 3: Menunggu Verifikasi Admin
  - 4: Menunggu Ujian
  - 5: Ujian Berlangsung
  - 6: Selesai
  - 7: Asesor Tidak Dapat Hadir

### 1.9 Growth Metrics

```php
$bulanIni = Pendaftaran::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

$bulanLalu = Pendaftaran::whereMonth('created_at', now()->subMonth()->month)
    ->whereYear('created_at', now()->subMonth()->year)
    ->count();

$growthRate = $bulanLalu > 0
    ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1)
    : 0;
```
- **Formula Growth Rate**: `((Bulan Ini - Bulan Lalu) / Bulan Lalu) × 100`
- **Satuan**: Persentase (%)
- **Arti**:
  - Positif: Pertumbuhan
  - Negatif: Penurunan
  - 0: Stabil

### 1.10 AI Insights (Rule-Based)

**Trend Analysis**:
- Growth > 20%: "Pertumbuhan sangat tinggi"
- Growth > 0%: "Pertumbuhan positif"
- Growth < -10%: "Penurunan signifikan"
- Else: "Relatif stabil"

**Capacity Planning**:
- Gap workload > 50: "Gap sangat tinggi, perlu redistribusi"
- Gap > 20: "Distribusi perlu penyesuaian"
- Else: "Distribusi cukup seimbang"

**Action Items**:
- Pass rate < 60%: "URGENT: Pass rate rendah"
- Pass rate < 75%: "Pass rate perlu ditingkatkan"
- Conversion rate < 50%: "Conversion rate rendah"
- Else: "Performance bagus"

---

## 2. Dashboard Asesi (UPGRADED VERSION)

**Controller**: `App\Http\Controllers\Asesi\DashboardController`

### 2.1 Key Performance Indicators (KPIs)

#### Total Pendaftaran
```php
$totalPendaftaran = Pendaftaran::where('user_id', $user->id)->count();
```
- **Filter**: Berdasarkan user_id yang sedang login
- **Perhitungan**: COUNT semua pendaftaran milik user

#### Jadwal Ujikom yang Akan Datang
```php
$jadwalUjikom = Pendaftaran::where('user_id', $user->id)
    ->whereHas('jadwal', function($query) {
        $query->where('tanggal_ujian', '>=', now())
              ->where('status', 1); // Aktif
    })
    ->count();
```
- **Filter**: user_id, tanggal >= hari ini, dan jadwal aktif
- **Perhitungan**: COUNT pendaftaran dengan jadwal mendatang yang aktif

#### Total Sertifikat (Kompeten)
```php
$totalSertifikat = Report::where('user_id', $user->id)
    ->where('status', 1) // Kompeten
    ->count();
```
- **Filter**: user_id dan status = 1 (Kompeten)
- **Perhitungan**: COUNT report dengan status kompeten

#### Pembayaran Pending
```php
$pembayaranPending = Pembayaran::where('user_id', $user->id)
    ->where('status', 2)
    ->count();
```
- **Filter**: user_id dan status = 2 (Menunggu Verifikasi)

#### Total Skema yang Diikuti
```php
$totalSkema = Pendaftaran::where('user_id', $user->id)
    ->distinct('skema_id')
    ->count('skema_id');
```
- **Perhitungan**: COUNT DISTINCT skema_id dari pendaftaran

#### Ujian yang Sudah Selesai
```php
$ujianSelesai = Pendaftaran::where('user_id', $user->id)
    ->where('status', 6) // Selesai
    ->count();
```
- **Filter**: status = 6 (Selesai)

### 2.2 Success Metrics

#### Status Sertifikasi (Pass Rate Personal)
```php
$totalReport = Report::where('user_id', $user->id)->count();
$totalKompeten = Report::where('user_id', $user->id)->where('status', 1)->count();
$statusSertifikasi = $totalReport > 0
    ? round(($totalKompeten / $totalReport) * 100, 1)
    : 0;
```
- **Formula**: `(Total Kompeten / Total Report) × 100`
- **Output**: Persentase keberhasilan personal
- **Contoh**: 3 kompeten dari 4 ujian = 75%

#### Tingkat Completion (Completion Rate)
```php
$completionRate = $totalPendaftaran > 0
    ? round(($ujianSelesai / $totalPendaftaran) * 100, 1)
    : 0;
```
- **Formula**: `(Ujian Selesai / Total Pendaftaran) × 100`
- **Output**: Persentase pendaftaran yang diselesaikan
- **Contoh**: 4 selesai dari 6 pendaftaran = 66.7%

### 2.3 Trend Analysis (6 Bulan Terakhir)

```php
$trenPendaftaran = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $count = Pendaftaran::where('user_id', $user->id)
        ->whereMonth('created_at', $bulan->month)
        ->whereYear('created_at', $bulan->year)
        ->count();
    $trenPendaftaran[] = [
        'bulan' => $bulan->format('M Y'),
        'jumlah' => $count
    ];
}
```
- **Filter**: Hanya pendaftaran milik user yang login
- **Output**: Data per bulan dalam 6 bulan terakhir

### 2.4 Performance by Skema

```php
$performanceSkema = Report::where('user_id', $user->id)
    ->with('skema')
    ->get()
    ->groupBy('skema_id')
    ->map(function($reports, $skemaId) {
        $total = $reports->count();
        $kompeten = $reports->where('status', 1)->count();
        $passRate = $total > 0 ? round(($kompeten / $total) * 100, 1) : 0;

        return [
            'nama' => $reports->first()->skema->nama ?? 'Unknown',
            'total_ujian' => $total,
            'kompeten' => $kompeten,
            'tidak_kompeten' => $total - $kompeten,
            'pass_rate' => $passRate
        ];
    })
    ->sortByDesc('pass_rate')
    ->values();
```
- **Group By**: skema_id
- **Perhitungan**:
  - Total ujian per skema
  - Kompeten vs Tidak Kompeten
  - Pass rate per skema
- **Sorting**: Berdasarkan pass_rate tertinggi
- **Output**: Performance data untuk setiap skema

### 2.5 Status Pipeline

```php
$statusPendaftaran = Pendaftaran::where('user_id', $user->id)
    ->selectRaw('status, COUNT(*) as jumlah')
    ->groupBy('status')
    ->get()
    ->mapWithKeys(function($item) {
        $statusText = $this->getStatusText($item->status);
        return [$statusText => $item->jumlah];
    });
```
- **Perhitungan**: GROUP BY status
- **Output**: Key-value pair (status text => jumlah)

### 2.6 Timeline Progress

```php
$lastPendaftaran = Pendaftaran::where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->first();

if ($lastPendaftaran) {
    $timelineProgress = [
        'pendaftaran' => $lastPendaftaran->created_at,
        'verifikasi' => $lastPendaftaran->status >= 3 ? $lastPendaftaran->updated_at : null,
        'jadwal_ujian' => $lastPendaftaran->jadwal ? $lastPendaftaran->jadwal->tanggal_ujian : null,
        'selesai' => $lastPendaftaran->status == 6 ? $lastPendaftaran->updated_at : null,
        'status_sekarang' => $this->getStatusText($lastPendaftaran->status),
        'skema' => $lastPendaftaran->skema->nama ?? 'Unknown'
    ];
}
```
- **Data**: Pendaftaran terakhir user
- **Output**: Timeline dengan 4 tahapan (Pendaftaran → Verifikasi → Jadwal → Selesai)
- **Logika**:
  - Pendaftaran: Selalu ada (created_at)
  - Verifikasi: Jika status >= 3
  - Jadwal: Jika ada jadwal terkait
  - Selesai: Jika status = 6

### 2.7 Jadwal Mendatang (Detail)

```php
$jadwalMendatang = Pendaftaran::where('user_id', $user->id)
    ->whereHas('jadwal', function($query) {
        $query->where('tanggal_ujian', '>=', now())
              ->where('status', 1);
    })
    ->with(['jadwal.skema', 'jadwal.tuk', 'skema'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get()
    ->map(function($pendaftaran) {
        return [
            'tanggal_ujian' => $pendaftaran->jadwal
                ? $pendaftaran->jadwal->tanggal_ujian->format('d M Y H:i')
                : '-',
            'skema' => $pendaftaran->skema->nama ?? 'Unknown',
            'tuk' => $pendaftaran->jadwal->tuk->name ?? 'Unknown',
            'status' => $this->getStatusText($pendaftaran->status),
            'hari_lagi' => $pendaftaran->jadwal
                ? now()->diffInDays($pendaftaran->jadwal->tanggal_ujian, false)
                : null
        ];
    });
```
- **Limit**: 5 jadwal terdekat
- **Output**: Tanggal, skema, TUK, dan countdown (berapa hari lagi)
- **Countdown**:
  - Positif: "X hari lagi"
  - 0: "Hari ini"
  - Negatif: "Sudah lewat"

### 2.8 Riwayat Sertifikasi

```php
$riwayatSertifikasi = Report::where('user_id', $user->id)
    ->with(['skema', 'jadwal'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get()
    ->map(function($report) {
        return [
            'tanggal' => $report->created_at->format('d M Y'),
            'skema' => $report->skema->nama ?? 'Unknown',
            'status' => $report->status == 1 ? 'Kompeten' : 'Tidak Kompeten',
            'status_badge' => $report->status == 1 ? 'success' : 'danger'
        ];
    });
```
- **Limit**: 5 riwayat terakhir
- **Output**: Tanggal, skema, dan status (Kompeten/Tidak Kompeten)
- **Badge**: success (hijau) atau danger (merah)

### 2.9 Aktivitas Terbaru

```php
$aktivitas = Pendaftaran::where('user_id', $user->id)
    ->with(['jadwal', 'skema'])
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get()
    ->map(function($pendaftaran) {
        $statusBadge = 'secondary';
        if ($pendaftaran->status == 6) $statusBadge = 'success';
        elseif (in_array($pendaftaran->status, [1, 3, 4])) $statusBadge = 'warning';
        elseif (in_array($pendaftaran->status, [2, 7])) $statusBadge = 'danger';
        elseif ($pendaftaran->status == 5) $statusBadge = 'info';

        return [
            'tanggal' => $pendaftaran->created_at->format('d M Y H:i'),
            'aktivitas' => 'Pendaftaran Ujikom',
            'status' => $this->getStatusText($pendaftaran->status),
            'status_badge' => $statusBadge,
            'keterangan' => 'Skema: ' . ($pendaftaran->skema->nama ?? 'Tidak diketahui')
        ];
    });
```
- **Limit**: 10 aktivitas terakhir
- **Badge Color Logic**:
  - success (hijau): Status = 6 (Selesai)
  - warning (kuning): Status 1, 3, 4 (Menunggu)
  - danger (merah): Status 2, 7 (Ditolak/Bermasalah)
  - info (biru): Status 5 (Sedang Berlangsung)
- **Sorting**: Berdasarkan created_at DESC (terbaru)

### 2.10 Growth Metrics

```php
$bulanIni = Pendaftaran::where('user_id', $user->id)
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

$bulanLalu = Pendaftaran::where('user_id', $user->id)
    ->whereMonth('created_at', now()->subMonth()->month)
    ->whereYear('created_at', now()->subMonth()->year)
    ->count();

$growthRate = $bulanLalu > 0
    ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1)
    : ($bulanIni > 0 ? 100 : 0);
```
- **Formula**: `((Bulan Ini - Bulan Lalu) / Bulan Lalu) × 100`
- **Special Case**: Jika bulan lalu = 0 dan bulan ini > 0, return 100%
- **Output**: Persentase pertumbuhan personal

---

## 3. Dashboard Asesor

**Controller**: `App\Http\Controllers\Asesor\DashboardController`

### 3.1 KPI Metrics

#### Total Asesi yang Dinilai (Lifetime)
```php
$totalAsesiDinilai = PendaftaranUjikom::where('asesor_id', $user->id)
    ->where('status', 5)
    ->count();
```
- **Filter**: asesor_id dan status = 5 (Kompeten/Selesai dinilai)
- **Perhitungan**: COUNT total asesi yang sudah dinilai

#### Total Asesi Bulan Ini
```php
$asesiBulanIni = PendaftaranUjikom::where('asesor_id', $user->id)
    ->where('status', 5)
    ->whereMonth('updated_at', now()->month)
    ->whereYear('updated_at', now()->year)
    ->count();
```
- **Filter**: Bulan dan tahun ini (berdasarkan updated_at)

#### Perubahan dari Bulan Lalu
```php
$asesiBulanLalu = PendaftaranUjikom::where('asesor_id', $user->id)
    ->where('status', 5)
    ->whereMonth('updated_at', now()->subMonth()->month)
    ->whereYear('updated_at', now()->subMonth()->year)
    ->count();

$perubahanAsesi = $asesiBulanLalu > 0
    ? round((($asesiBulanIni - $asesiBulanLalu) / $asesiBulanLalu) * 100)
    : ($asesiBulanIni > 0 ? 100 : 0);
```
- **Formula**: `((Bulan Ini - Bulan Lalu) / Bulan Lalu) × 100`
- **Special Case**: Jika bulan lalu = 0 dan bulan ini > 0, return 100%

#### Tingkat Kelulusan (Pass Rate)
```php
$totalKompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
    $query->where('asesor_id', $user->id);
})->where('status', 1)->count();

$totalReport = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
    $query->where('asesor_id', $user->id);
})->count();

$tingkatKelulusan = $totalReport > 0 ? round(($totalKompeten / $totalReport) * 100) : 0;
```
- **Filter**: Report dari asesi yang dinilai oleh asesor ini
- **Formula**: `(Total Kompeten / Total Report) × 100`

#### Jadwal Aktif/Upcoming
```php
$jadwalAktif = PendaftaranUjikom::where('asesor_id', $user->id)
    ->where(function($q) {
        $q->where('asesor_confirmed', false)
          ->orWhereHas('jadwal', function($sq) {
              $sq->where('status', 3);
          });
    })
    ->distinct('jadwal_id')
    ->count('jadwal_id');
```
- **Logika**: Jadwal yang belum dikonfirmasi ATAU sedang berlangsung (status = 3)
- **Perhitungan**: COUNT distinct jadwal_id

#### Total Skema yang Dikuasai
```php
$totalSkema = \DB::table('asesor_skema')
    ->where('asesor_id', $user->id)
    ->count();
```
- **Sumber Data**: Tabel pivot `asesor_skema`

#### Total Jadwal Selesai
```php
$totalJadwalSelesai = PendaftaranUjikom::where('asesor_id', $user->id)
    ->where('status', 5)
    ->distinct('jadwal_id')
    ->count('jadwal_id');
```
- **Perhitungan**: COUNT distinct jadwal_id dengan status selesai

#### Rata-rata Asesi per Jadwal
```php
$avgAsesiPerJadwal = $totalJadwalSelesai > 0
    ? round($totalAsesiDinilai / $totalJadwalSelesai, 1)
    : 0;
```
- **Formula**: `Total Asesi Dinilai / Total Jadwal Selesai`

#### Avg Waktu Penilaian
```php
$avgWaktuPenilaian = \DB::table('report')
    ->join('pendaftaran', 'report.pendaftaran_id', '=', 'pendaftaran.id')
    ->join('pendaftaran_ujikom', 'pendaftaran.id', '=', 'pendaftaran_ujikom.pendaftaran_id')
    ->where('pendaftaran_ujikom.asesor_id', $user->id)
    ->whereNotNull('report.updated_at')
    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, pendaftaran_ujikom.created_at, report.updated_at)) as avg_hours')
    ->value('avg_hours');
```
- **Perhitungan**: Average dari selisih waktu antara created_at pendaftaran_ujikom dan updated_at report
- **Satuan**: Jam (hours)

### 3.2 Analytics Data

#### Trend Penilaian (6 Bulan Terakhir)
```php
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);

    $kompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
        $query->where('asesor_id', $user->id);
    })
    ->where('status', 1)
    ->whereMonth('created_at', $bulan->month)
    ->whereYear('created_at', $bulan->year)
    ->count();

    $tidakKompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
        $query->where('asesor_id', $user->id);
    })
    ->where('status', 0)
    ->whereMonth('created_at', $bulan->month)
    ->whereYear('created_at', $bulan->year)
    ->count();

    $trendPenilaian[] = [
        'bulan' => $bulan->format('M Y'),
        'kompeten' => $kompeten,
        'tidak_kompeten' => $tidakKompeten,
        'total' => $kompeten + $tidakKompeten,
        'persentase_lulus' => ($kompeten + $tidakKompeten) > 0
            ? round(($kompeten / ($kompeten + $tidakKompeten)) * 100, 1)
            : 0
    ];
}
```
- **Output**: Data kompeten vs tidak kompeten per bulan + persentase lulus

#### Distribusi per Skema
```php
$distribusiSkema = \DB::table('pendaftaran_ujikom')
    ->join('pendaftaran', 'pendaftaran_ujikom.pendaftaran_id', '=', 'pendaftaran.id')
    ->join('skema', 'pendaftaran.skema_id', '=', 'skema.id')
    ->where('pendaftaran_ujikom.asesor_id', $user->id)
    ->where('pendaftaran_ujikom.status', 5)
    ->select('skema.nama', \DB::raw('COUNT(*) as jumlah'))
    ->groupBy('skema.nama')
    ->orderByDesc('jumlah')
    ->limit(5)
    ->get();
```
- **Filter**: Status = 5 (selesai dinilai)
- **Limit**: Top 5 skema

#### Workload Analysis
```php
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $count = PendaftaranUjikom::where('asesor_id', $user->id)
        ->whereMonth('created_at', $bulan->month)
        ->whereYear('created_at', $bulan->year)
        ->count();
    $workloadAnalysis[] = [
        'bulan' => $bulan->format('M'),
        'jumlah' => $count
    ];
}
```
- **Output**: Jumlah asesi per bulan dalam 6 bulan terakhir

#### Performance Summary
```php
$performanceSummary = [
    'total_kompeten' => $totalKompeten,
    'total_tidak_kompeten' => $totalReport - $totalKompeten,
    'pass_rate' => $tingkatKelulusan,
    'total_penilaian' => $totalReport
];
```

#### Pending Confirmations
```php
$pendingConfirmations = PendaftaranUjikom::where('asesor_id', $user->id)
    ->where('asesor_confirmed', false)
    ->whereHas('jadwal', function($query) {
        $query->where('status', 1)
            ->where('tanggal_ujian', '>', now());
    })
    ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk'])
    ->get()
    ->groupBy('jadwal_id')
    ->map(function($items) {
        $first = $items->first();
        return [
            'jadwal_id' => $first->jadwal_id,
            'jadwal' => $first->jadwal,
            'jumlah_asesi' => $items->count(),
        ];
    })
    ->values();
```
- **Filter**: Belum dikonfirmasi, jadwal aktif, tanggal > hari ini
- **Group By**: jadwal_id

#### Upcoming Jadwal
```php
$upcomingJadwal = PendaftaranUjikom::where('asesor_id', $user->id)
    ->where('asesor_confirmed', true)
    ->whereHas('jadwal', function($query) {
        $query->where('status', 1)
            ->where('tanggal_ujian', '>=', now());
    })
    ->with(['jadwal.skema', 'jadwal.tuk'])
    ->get()
    ->groupBy('jadwal_id')
    ->map(function($items) {
        $first = $items->first();
        return [
            'jadwal' => $first->jadwal,
            'jumlah_asesi' => $items->count(),
        ];
    })
    ->sortBy('jadwal.tanggal_ujian')
    ->take(5)
    ->values();
```
- **Filter**: Sudah dikonfirmasi, jadwal aktif, tanggal >= hari ini
- **Limit**: 5 jadwal terdekat
- **Sorting**: Berdasarkan tanggal_ujian ASC

---

## 4. Dashboard Kaprodi

**Controller**: `App\Http\Controllers\Kaprodi\DashboardController`

### 4.1 Key Performance Indicators (KPIs)

#### Total Pendaftaran
```php
$totalPendaftaran = Pendaftaran::count();
```

#### Total Asesi Unik
```php
$totalAsesi = Pendaftaran::distinct('user_id')->count('user_id');
```

#### Total Skema
```php
$totalSkema = Skema::count();
```

#### Total Asesor
```php
$totalAsesor = User::where('user_type', 'asesor')->count();
```

#### Pendaftaran Menunggu Verifikasi
```php
$menungguVerifikasi = Pendaftaran::where('status', 1)->count();
```
- **Status 1**: Menunggu Verifikasi Kaprodi

#### Tingkat Persetujuan (Approval Rate)
```php
$totalDiverifikasi = Pendaftaran::whereIn('status', [3, 4, 5, 6])->count();
$approvalRate = $totalPendaftaran > 0
    ? round(($totalDiverifikasi / $totalPendaftaran) * 100, 1)
    : 0;
```
- **Formula**: `(Total Diverifikasi / Total Pendaftaran) × 100`
- **Total Diverifikasi**: Status >= 3 (telah melewati verifikasi Kaprodi)

#### Tingkat Keberhasilan (Pass Rate)
```php
$totalKompeten = Report::where('status', 1)->count();
$totalTidakKompeten = Report::where('status', 2)->count();
$totalUjikom = $totalKompeten + $totalTidakKompeten;
$passRate = $totalUjikom > 0
    ? round(($totalKompeten / $totalUjikom) * 100, 1)
    : 0;
```
- **Formula**: `(Total Kompeten / Total Ujikom) × 100`

### 4.2 Verifikasi Performance Metrics

#### Statistik Status Pendaftaran
```php
$statusStats = [
    'menunggu_verifikasi' => Pendaftaran::where('status', 1)->count(),
    'ditolak' => Pendaftaran::where('status', 2)->count(),
    'diverifikasi' => Pendaftaran::where('status', 3)->count(),
    'menunggu_ujian' => Pendaftaran::where('status', 4)->count(),
    'ujian_berlangsung' => Pendaftaran::where('status', 5)->count(),
    'selesai' => Pendaftaran::where('status', 6)->count(),
];
```

#### Rata-rata Waktu Verifikasi
```php
$avgVerificationTime = Pendaftaran::whereIn('status', [3, 4, 5, 6])
    ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
    ->value('avg_days');
$avgVerificationTime = $avgVerificationTime ? round($avgVerificationTime, 1) : 0;
```
- **Filter**: Hanya pendaftaran yang sudah diverifikasi
- **Perhitungan**: AVG dari selisih updated_at - created_at
- **Satuan**: Hari

### 4.3 Trend Analysis

#### Tren Pendaftaran (6 Bulan Terakhir)
```php
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $count = Pendaftaran::whereMonth('created_at', $bulan->month)
        ->whereYear('created_at', $bulan->year)
        ->count();
    $trenPendaftaran[] = [
        'bulan' => $bulan->format('M Y'),
        'jumlah' => $count
    ];
}
```

#### Growth Rate
```php
$bulanIni = Pendaftaran::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

$bulanLalu = Pendaftaran::whereMonth('created_at', now()->subMonth()->month)
    ->whereYear('created_at', now()->subMonth()->year)
    ->count();

$growthRate = $bulanLalu > 0
    ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1)
    : 0;
```

### 4.4 Distribusi Skema (Top 5)

```php
$distribusiSkema = Pendaftaran::select('skema_id', DB::raw('count(*) as total'))
    ->with('skema')
    ->groupBy('skema_id')
    ->orderBy('total', 'desc')
    ->take(5)
    ->get()
    ->mapWithKeys(function ($item) {
        return [$item->skema->nama ?? 'Unknown' => $item->total];
    });
```

### 4.5 Top Performing Skema by Pass Rate

```php
$topSkema = Report::select('report.skema_id',
        DB::raw('COUNT(*) as total_ujian'),
        DB::raw('SUM(CASE WHEN report.status = 1 THEN 1 ELSE 0 END) as total_lulus'))
    ->with('skema')
    ->groupBy('report.skema_id')
    ->havingRaw('COUNT(*) >= 3')
    ->get()
    ->map(function ($item) {
        $passRate = $item->total_ujian > 0
            ? round(($item->total_lulus / $item->total_ujian) * 100, 1)
            : 0;
        return [
            'nama' => $item->skema->nama ?? 'Unknown',
            'total_ujian' => $item->total_ujian,
            'total_lulus' => $item->total_lulus,
            'pass_rate' => $passRate
        ];
    })
    ->sortByDesc('pass_rate')
    ->take(5)
    ->values();
```
- **Filter**: Minimal 3 ujian
- **Sorting**: Pass rate tertinggi

### 4.6 Workload Asesor (Top 10)

```php
$workloadAsesor = DB::table('pendaftaran_ujikom')
    ->join('users', 'pendaftaran_ujikom.asesor_id', '=', 'users.id')
    ->select('users.name as nama', DB::raw('COUNT(pendaftaran_ujikom.id) as total'))
    ->groupBy('users.id', 'users.name')
    ->orderBy('total', 'desc')
    ->limit(10)
    ->get();
```

### 4.7 Segmentasi Demografi

```php
$segmentasiJenisKelamin = User::whereIn('id', function($query) {
        $query->select('user_id')->from('pendaftaran')->distinct();
    })
    ->select('jenis_kelamin', DB::raw('COUNT(id) as jumlah'))
    ->whereNotNull('jenis_kelamin')
    ->where('jenis_kelamin', '!=', '')
    ->groupBy('jenis_kelamin')
    ->get()
    ->mapWithKeys(function($item) {
        $label = $item->jenis_kelamin == 'L' ? 'Laki-laki' :
                ($item->jenis_kelamin == 'P' ? 'Perempuan' : $item->jenis_kelamin);
        return [$label => $item->jumlah];
    });
```
- **Filter**: Hanya user yang pernah mendaftar

### 4.8 Verifikasi Trend

```php
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $diverifikasi = Pendaftaran::whereMonth('updated_at', $bulan->month)
        ->whereYear('updated_at', $bulan->year)
        ->whereIn('status', [3, 4, 5, 6])
        ->count();
    $ditolak = Pendaftaran::whereMonth('updated_at', $bulan->month)
        ->whereYear('updated_at', $bulan->year)
        ->where('status', 2)
        ->count();

    $verifikasiTrend[] = [
        'bulan' => $bulan->format('M Y'),
        'diverifikasi' => $diverifikasi,
        'ditolak' => $ditolak
    ];
}
```
- **Basis Waktu**: updated_at (waktu verifikasi)

### 4.9 AI Insights (Rule-Based)

**Verifikasi Workload Analysis**:
- Menunggu > 20: "PERHATIAN: Perlu ditindaklanjuti segera"
- Menunggu > 10: "Workload masih dalam batas wajar"
- Else: "Proses verifikasi berjalan lancar"

**Performance Analysis**:
- Approval Rate >= 80%: "Sangat baik"
- Approval Rate >= 60%: "Cukup baik"
- Else: "Perlu evaluasi"

**Action Items**:
- Avg Verification > 7 hari: "Terlalu lama"
- Avg Verification > 5 hari: "Masih acceptable"
- Pass Rate < 60%: "Perlu perhatian"
- Growth Rate < -15%: "Penurunan signifikan"
- Else: "Semua metrik dalam kondisi baik"

---

## 5. Dashboard Pimpinan

**Controller**: `App\Http\Controllers\Pimpinan\DashboardController`

### 5.1 Executive KPIs (High-Level Overview)

#### Total Pendaftaran (All-time)
```php
$totalPendaftaran = Pendaftaran::count();
```

#### Total Asesi Unik
```php
$totalAsesi = Pendaftaran::distinct('user_id')->count('user_id');
```

#### Total Skema Aktif
```php
$totalSkema = Skema::count();
```

#### Total Asesor Aktif
```php
$totalAsesor = User::where('user_type', 'asesor')->count();
```

#### Total Jadwal (All-time)
```php
$totalJadwal = Jadwal::count();
```

#### Total TUK
```php
$totalTuk = DB::table('tuk')->count();
```

#### Tingkat Keberhasilan Keseluruhan (Pass Rate)
```php
$totalKompeten = Report::where('status', 1)->count();
$totalTidakKompeten = Report::where('status', 0)->count();
$totalUjikom = $totalKompeten + $totalTidakKompeten;
$passRate = $totalUjikom > 0
    ? round(($totalKompeten / $totalUjikom) * 100, 1)
    : 0;
```

#### Utilisasi Kapasitas
```php
$jadwalTerisi = Jadwal::whereHas('pendaftaran')->count();
$utilisasiKapasitas = $totalJadwal > 0
    ? round(($jadwalTerisi / $totalJadwal) * 100, 1)
    : 0;
```
- **Formula**: `(Jadwal Terisi / Total Jadwal) × 100`
- **Jadwal Terisi**: Jadwal yang memiliki pendaftaran

### 5.2 Growth & Trend Metrics

#### Pendaftaran Bulan Ini vs Bulan Lalu
```php
$pendaftaranBulanIni = Pendaftaran::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

$pendaftaranBulanLalu = Pendaftaran::whereMonth('created_at', now()->subMonth()->month)
    ->whereYear('created_at', now()->subMonth()->year)
    ->count();

$growthRate = $pendaftaranBulanLalu > 0
    ? round((($pendaftaranBulanIni - $pendaftaranBulanLalu) / $pendaftaranBulanLalu) * 100, 1)
    : ($pendaftaranBulanIni > 0 ? 100 : 0);
```

#### Trend Pendaftaran (12 Bulan Terakhir)
```php
$trendPendaftaran = [];
for ($i = 11; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $count = Pendaftaran::whereMonth('created_at', $bulan->month)
        ->whereYear('created_at', $bulan->year)
        ->count();
    $trendPendaftaran[] = [
        'bulan' => $bulan->format('M Y'),
        'jumlah' => $count
    ];
}
```
- **Periode**: 12 bulan (lebih panjang dari role lain untuk strategic view)

### 5.3 Performance Analytics

#### Pass Rate Trend (6 Bulan Terakhir)
```php
$passRateTrend = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $kompeten = Report::whereMonth('created_at', $bulan->month)
        ->whereYear('created_at', $bulan->year)
        ->where('status', 1)
        ->count();
    $tidakKompeten = Report::whereMonth('created_at', $bulan->month)
        ->whereYear('created_at', $bulan->year)
        ->where('status', 0)
        ->count();
    $total = $kompeten + $tidakKompeten;
    $rate = $total > 0 ? round(($kompeten / $total) * 100, 1) : 0;

    $passRateTrend[] = [
        'bulan' => $bulan->format('M Y'),
        'pass_rate' => $rate,
        'kompeten' => $kompeten,
        'tidak_kompeten' => $tidakKompeten
    ];
}
```
- **Output**: Trend pass rate per bulan + detail kompeten/tidak kompeten

#### Top Performing Skema by Pass Rate
```php
$topSkemaByPassRate = Report::select('report.skema_id',
        DB::raw('COUNT(*) as total_ujian'),
        DB::raw('SUM(CASE WHEN report.status = 1 THEN 1 ELSE 0 END) as total_kompeten'))
    ->with('skema')
    ->groupBy('report.skema_id')
    ->havingRaw('COUNT(*) >= 3')
    ->get()
    ->map(function ($item) {
        $passRate = $item->total_ujian > 0
            ? round(($item->total_kompeten / $item->total_ujian) * 100, 1)
            : 0;
        return [
            'nama' => $item->skema->nama ?? 'Unknown',
            'total_ujian' => $item->total_ujian,
            'pass_rate' => $passRate
        ];
    })
    ->sortByDesc('pass_rate')
    ->take(5)
    ->values();
```

### 5.4 Skema Analytics

#### Distribusi Pendaftaran per Skema (Top 5)
```php
$distribusiSkema = Pendaftaran::select('skema_id', DB::raw('count(*) as total'))
    ->with('skema')
    ->groupBy('skema_id')
    ->orderBy('total', 'desc')
    ->take(5)
    ->get()
    ->map(function ($item) {
        return [
            'nama' => $item->skema->nama ?? 'Unknown',
            'total' => $item->total
        ];
    });
```

#### Skema Growth (6 Bulan Terakhir untuk Top 3 Skema)
```php
$topSkemaIds = $distribusiSkema->take(3)->pluck('nama')->toArray();
$skemaGrowthTrend = [];

foreach ($topSkemaIds as $skemaNama) {
    $skemaData = [];
    $skema = Skema::where('nama', $skemaNama)->first();

    if ($skema) {
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Pendaftaran::where('skema_id', $skema->id)
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $skemaData[] = $count;
        }
        $skemaGrowthTrend[] = [
            'nama' => $skemaNama,
            'data' => $skemaData
        ];
    }
}
```
- **Fokus**: Top 3 skema populer
- **Output**: Data per bulan untuk setiap skema

### 5.5 Asesor Analytics

#### Top 10 Asesor by Workload
```php
$topAsesor = DB::table('pendaftaran_ujikom')
    ->join('users', 'pendaftaran_ujikom.asesor_id', '=', 'users.id')
    ->select('users.name as nama', DB::raw('COUNT(pendaftaran_ujikom.id) as total_asesi'))
    ->groupBy('users.id', 'users.name')
    ->orderBy('total_asesi', 'desc')
    ->limit(10)
    ->get();
```

#### Workload Distribution
```php
$workloadDistribution = DB::table('pendaftaran_ujikom')
    ->select('asesor_id', DB::raw('COUNT(*) as total_asesi'))
    ->groupBy('asesor_id')
    ->get()
    ->groupBy(function($item) {
        if ($item->total_asesi <= 10) return '1-10 Asesi';
        if ($item->total_asesi <= 20) return '11-20 Asesi';
        if ($item->total_asesi <= 30) return '21-30 Asesi';
        if ($item->total_asesi <= 40) return '31-40 Asesi';
        return '40+ Asesi';
    })
    ->map(function($group) {
        return $group->count();
    })
    ->sortKeys();
```
- **Kategori**:
  - 1-10 Asesi
  - 11-20 Asesi
  - 21-30 Asesi
  - 31-40 Asesi
  - 40+ Asesi

### 5.6 Operational Efficiency Metrics

#### Rata-rata Waktu dari Pendaftaran ke Ujian
```php
$avgTimeToExam = Pendaftaran::whereIn('pendaftaran.status', [4, 5, 6])
    ->whereNotNull('pendaftaran.jadwal_id')
    ->join('jadwal', 'pendaftaran.jadwal_id', '=', 'jadwal.id')
    ->whereRaw('jadwal.tanggal_ujian > pendaftaran.created_at')
    ->selectRaw('AVG(DATEDIFF(jadwal.tanggal_ujian, pendaftaran.created_at)) as avg_days')
    ->value('avg_days');
$avgTimeToExam = $avgTimeToExam ? round($avgTimeToExam, 1) : 0;
```
- **Filter**: Pendaftaran yang sudah punya jadwal
- **Perhitungan**: AVG dari selisih tanggal_ujian - created_at
- **Satuan**: Hari

#### Status Pipeline Distribution
```php
$statusPipeline = [
    'Menunggu Verifikasi' => Pendaftaran::where('status', 1)->count(),
    'Ditolak' => Pendaftaran::where('status', 2)->count(),
    'Menunggu Verifikasi Admin' => Pendaftaran::where('status', 3)->count(),
    'Menunggu Ujian' => Pendaftaran::where('status', 4)->count(),
    'Ujian Berlangsung' => Pendaftaran::where('status', 5)->count(),
    'Selesai' => Pendaftaran::where('status', 6)->count(),
];
```

### 5.7 Demographic Analytics

#### Segmentasi Jenis Kelamin
```php
$segmentasiGender = User::whereIn('id', function($query) {
        $query->select('user_id')->from('pendaftaran')->distinct();
    })
    ->select('jenis_kelamin', DB::raw('COUNT(id) as jumlah'))
    ->whereNotNull('jenis_kelamin')
    ->where('jenis_kelamin', '!=', '')
    ->groupBy('jenis_kelamin')
    ->get()
    ->mapWithKeys(function($item) {
        $label = $item->jenis_kelamin == 'L' ? 'Laki-laki' :
                ($item->jenis_kelamin == 'P' ? 'Perempuan' : $item->jenis_kelamin);
        return [$label => $item->jumlah];
    });
```

### 5.8 Executive Insights (Rule-Based AI)

**Pass Rate Analysis**:
- >= 90%: "Excellent Pass Rate" (Success)
- >= 75%: "Good Pass Rate" (Info)
- < 75%: "Pass Rate Perlu Perhatian" (Warning)

**Growth Analysis**:
- > 20%: "Pertumbuhan Signifikan" (Success)
- < -10%: "Penurunan Pendaftaran" (Danger)

**Capacity Utilization**:
- < 60%: "Utilisasi Kapasitas Rendah" (Warning)
- > 90%: "Kapasitas Hampir Penuh" (Info)

**Operational Efficiency**:
- Avg Time > 30 hari: "Proses Terlalu Lama" (Warning)

**Workload Balance**:
- Jika ada asesor dengan 40+ asesi: "Pertimbangkan redistribusi" (Info)

**Pipeline Health**:
- Menunggu Verifikasi > 10: "Backlog Verifikasi" (Warning)

---

## 6. Dashboard TUK

**Controller**: `App\Http\Controllers\Tuk\DashboardController`

### 6.1 Key Metrics

#### Total Jadwal di TUK
```php
$totalJadwal = Jadwal::when($tukId, $tukFilter)->count();
```
- **Filter**: Berdasarkan tuk_id (jika ada)

#### Jadwal Aktif
```php
$jadwalAktif = Jadwal::when($tukId, $tukFilter)
    ->where('status', 1)
    ->count();
```
- **Status 1**: Aktif

#### Jadwal Hari Ini
```php
$jadwalHariIni = Jadwal::when($tukId, $tukFilter)
    ->whereDate('tanggal_ujian', today())
    ->whereIn('status', [1, 3])
    ->count();
```
- **Filter**: Tanggal hari ini, status Aktif atau Sedang Berlangsung

#### Jadwal Selesai
```php
$jadwalSelesai = Jadwal::when($tukId, $tukFilter)
    ->where('status', 4)
    ->count();
```
- **Status 4**: Selesai

#### Total Asesi/Peserta di TUK
```php
$totalAsesi = Pendaftaran::whereHas('jadwal', function($query) use ($tukId, $tukFilter) {
    $query->when($tukId, $tukFilter);
})->count();
```
- **Perhitungan**: COUNT pendaftaran yang terhubung ke jadwal di TUK ini

### 6.2 Tren Jadwal Ujikom (6 Bulan Terakhir)

```php
$trenJadwal = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan = now()->subMonths($i);
    $count = Jadwal::when($tukId, $tukFilter)
        ->whereMonth('tanggal_ujian', $bulan->month)
        ->whereYear('tanggal_ujian', $bulan->year)
        ->count();
    $trenJadwal[] = [
        'bulan' => $bulan->format('M Y'),
        'jumlah' => $count
    ];
}
```

### 6.3 Distribusi Skema di TUK

```php
$distribusiSkema = Jadwal::when($tukId, $tukFilter)
    ->with('skema')
    ->get()
    ->groupBy('skema.nama')
    ->map(function ($group) {
        return $group->count();
    });
```
- **Perhitungan**: GROUP BY skema.nama

### 6.4 Jadwal Minggu Ini (Per Hari)

```php
$jadwalMingguan = [];
$hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

foreach ($hari as $index => $namaHari) {
    $tanggal = now()->startOfWeek()->addDays($index);
    $jumlah = Jadwal::when($tukId, $tukFilter)
        ->whereDate('tanggal_ujian', $tanggal)
        ->count();

    $status = $tanggal->isPast() ? 'Selesai' : 'Akan Datang';
    if ($tanggal->isToday()) {
        $status = 'Hari Ini';
    }

    $jadwalMingguan[] = [
        'hari' => $namaHari,
        'tanggal' => $tanggal->format('d M'),
        'jumlah' => $jumlah,
        'status' => $status
    ];
}
```
- **Periode**: Senin - Minggu (minggu ini)
- **Status**:
  - "Selesai": Jika tanggal sudah lewat
  - "Hari Ini": Jika tanggal hari ini
  - "Akan Datang": Jika tanggal belum tiba

### 6.5 Jadwal Mendatang

```php
$jadwalMendatang = Jadwal::when($tukId, $tukFilter)
    ->with(['skema', 'tuk'])
    ->where('tanggal_ujian', '>=', now())
    ->whereIn('status', [1, 3])
    ->orderBy('tanggal_ujian', 'asc')
    ->take(5)
    ->get();
```
- **Filter**: Tanggal >= hari ini, status Aktif atau Sedang Berlangsung
- **Limit**: 5 jadwal terdekat
- **Sorting**: Berdasarkan tanggal_ujian ASC

### 6.6 Statistik Jadwal per Status

```php
$statusJadwal = [
    'pending' => Jadwal::when($tukId, $tukFilter)->where('status', 0)->count(),
    'aktif' => Jadwal::when($tukId, $tukFilter)->where('status', 1)->count(),
    'ditunda' => Jadwal::when($tukId, $tukFilter)->where('status', 2)->count(),
    'sedang_berlangsung' => Jadwal::when($tukId, $tukFilter)->where('status', 3)->count(),
    'selesai' => Jadwal::when($tukId, $tukFilter)->where('status', 4)->count(),
];
```
- **Status Mapping**:
  - 0: Pending
  - 1: Aktif
  - 2: Ditunda
  - 3: Sedang Berlangsung
  - 4: Selesai

---

## 7. Analytics Service

**Service**: `App\Services\AnalyticsService`

### 7.1 getTrendPendaftaran

```php
public function getTrendPendaftaran($skemaId = null, $startDate = null, $endDate = null)
{
    $query = Pendaftaran::select(
        DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
        DB::raw('COUNT(id) as total_pendaftaran')
    );

    if ($skemaId) {
        $query->where('skema_id', $skemaId);
    }

    if ($startDate) {
        $query->where('created_at', '>=', $startDate);
    }

    if ($endDate) {
        $query->where('created_at', '<=', $endDate);
    }

    $results = $query->groupBy('month')
                    ->orderBy('month')
                    ->get();
}
```
- **Parameter**:
  - `$skemaId` (optional): Filter berdasarkan skema
  - `$startDate` (optional): Tanggal mulai
  - `$endDate` (optional): Tanggal akhir
- **Output**: Array berisi month dan total_pendaftaran per bulan
- **Format Month**: YYYY-MM (e.g., "2024-01")

### 7.2 getStatistikKompetensi

```php
public function getStatistikKompetensi($startDate = null, $endDate = null)
{
    $query = Report::select(
        'pendaftaran.skema_id',
        'skema.nama as skema_nama',
        'skema.kode as skema_kode',
        'report.status',
        DB::raw('COUNT(report.id) as jumlah')
    )
    ->join('pendaftaran', 'report.pendaftaran_id', '=', 'pendaftaran.id')
    ->join('skema', 'pendaftaran.skema_id', '=', 'skema.id');

    // Filter by date...

    $results = $query->groupBy('pendaftaran.skema_id', 'skema.nama', 'skema.kode', 'report.status')
                    ->get();

    // Mapping status
    $statusKey = $result->status == 1 ? 5 : 4;
    $response[$result->skema_id][$statusKey] = $result->jumlah;
}
```
- **Parameter**:
  - `$startDate` (optional): Tanggal mulai
  - `$endDate` (optional): Tanggal akhir
- **Status Mapping**:
  - Report status = 1 (Kompeten) → key 5
  - Report status != 1 (Tidak Kompeten) → key 4
- **Output**: Array dengan skema_id sebagai key, berisi jumlah per status + info skema

### 7.3 getSegmentasiDemografi

```php
public function getSegmentasiDemografi()
{
    $userIdsYangMendaftar = Pendaftaran::distinct()->pluck('user_id');

    // Jenis Kelamin
    $genderData = User::select('jenis_kelamin', DB::raw('COUNT(id) as jumlah'))
                       ->whereIn('id', $userIdsYangMendaftar)
                       ->whereNotNull('jenis_kelamin')
                       ->where('jenis_kelamin', '!=', '')
                       ->groupBy('jenis_kelamin')
                       ->get();

    $genderCounts = [];
    foreach ($genderData as $item) {
        $label = match($item->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => 'Lainnya'
        };
        $genderCounts[$label] = $item->jumlah;
    }

    // Pendidikan
    $pendidikanCounts = User::select('pendidikan', DB::raw('COUNT(id) as jumlah'))
                           ->whereIn('id', $userIdsYangMendaftar)
                           ->whereNotNull('pendidikan')
                           ->where('pendidikan', '!=', '')
                           ->groupBy('pendidikan')
                           ->get()
                           ->pluck('jumlah', 'pendidikan')
                           ->toArray();

    // Pekerjaan
    $pekerjaanCounts = User::select('pekerjaan', DB::raw('COUNT(id) as jumlah'))
                          ->whereIn('id', $userIdsYangMendaftar)
                          ->whereNotNull('pekerjaan')
                          ->where('pekerjaan', '!=', '')
                          ->groupBy('pekerjaan')
                          ->get()
                          ->pluck('jumlah', 'pekerjaan')
                          ->toArray();

    return [
        'jenis_kelamin' => $genderCounts,
        'pendidikan' => $pendidikanCounts,
        'pekerjaan' => $pekerjaanCounts,
    ];
}
```
- **Filter**: Hanya user yang pernah mendaftar (ada di tabel pendaftaran)
- **Output**:
  - `jenis_kelamin`: Laki-laki, Perempuan, Lainnya
  - `pendidikan`: Berdasarkan data di database
  - `pekerjaan`: Berdasarkan data di database

### 7.4 getWorkloadAsesor

```php
public function getWorkloadAsesor($startDate = null, $endDate = null)
{
    // Laporan per asesor
    $laporanQuery = Report::select(
        'pendaftaran_ujikom.asesor_id',
        'users.name as asesor_name',
        DB::raw('COUNT(report.id) as jumlah_laporan')
    )
    ->join('pendaftaran', 'report.pendaftaran_id', '=', 'pendaftaran.id')
    ->join('pendaftaran_ujikom', 'pendaftaran.id', '=', 'pendaftaran_ujikom.pendaftaran_id')
    ->join('users', 'pendaftaran_ujikom.asesor_id', '=', 'users.id');

    // Pembayaran asesor
    $pembayaranQuery = PembayaranAsesor::select(
        'asesor_id',
        DB::raw('COUNT(id) as jumlah_pembayaran')
    );

    // Gabungkan hasil
    $response[] = [
        'asesor_name' => $laporan ? $laporan->asesor_name : null,
        'jumlah_laporan' => $laporan ? $laporan->jumlah_laporan : 0,
        'jumlah_pembayaran' => $pembayaran ? $pembayaran->jumlah_pembayaran : 0
    ];
}
```
- **Parameter**:
  - `$startDate` (optional): Tanggal mulai
  - `$endDate` (optional): Tanggal akhir
- **Output**: Array berisi asesor_name, jumlah_laporan, jumlah_pembayaran

### 7.5 getDashboardSummary

```php
public function getDashboardSummary()
{
    $totalPendaftaran = Pendaftaran::count();
    $totalSkema = Skema::count();
    $totalAsesor = User::where('user_type', 'asesor')->count();
    $pendaftaranBulanIni = Pendaftaran::whereMonth('created_at', Carbon::now()->month)
                                     ->whereYear('created_at', Carbon::now()->year)
                                     ->count();

    return [
        'total_pendaftaran' => $totalPendaftaran,
        'total_skema' => $totalSkema,
        'total_asesor' => $totalAsesor,
        'pendaftaran_bulan_ini' => $pendaftaranBulanIni
    ];
}
```
- **Output**: Ringkasan data untuk dashboard (all-time + bulan ini)

### 7.6 getTrenPeminatSkema

```php
public function getTrenPeminatSkema($startDate = null, $endDate = null)
{
    $skemas = Skema::all();
    $result = [];

    foreach ($skemas as $skema) {
        $query = Pendaftaran::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
            DB::raw('COUNT(id) as registrations')
        )
        ->where('skema_id', $skema->id);

        // Apply date filters...

        $trend = $query->groupBy('period')
                      ->orderBy('period')
                      ->get();

        $result[] = [
            'skema_id' => $skema->id,
            'skema_name' => $skema->nama,
            'trend' => $trend->map(function ($item) {
                return [
                    'period' => $item->period,
                    'registrations' => $item->registrations
                ];
            })
        ];
    }

    return $result;
}
```
- **Parameter**:
  - `$startDate` (optional): Tanggal mulai
  - `$endDate` (optional): Tanggal akhir
- **Output**: Array berisi semua skema dengan trend pendaftaran per periode
- **Format Period**: YYYY-MM

---

## Mapping Status Pendaftaran

Berikut adalah mapping status yang digunakan di seluruh sistem:

| Status | Keterangan                     | Digunakan di                   |
|--------|--------------------------------|--------------------------------|
| 1      | Menunggu Verifikasi Kaprodi    | Pendaftaran                    |
| 2      | Tidak Lolos Verifikasi Kaprodi | Pendaftaran                    |
| 3      | Menunggu Verifikasi Admin      | Pendaftaran                    |
| 4      | Menunggu Ujian                 | Pendaftaran                    |
| 5      | Ujian Berlangsung              | Pendaftaran, PendaftaranUjikom |
| 6      | Selesai                        | Pendaftaran, PendaftaranUjikom |
| 7      | Asesor Tidak Dapat Hadir       | Pendaftaran                    |

## Mapping Status Report

| Status | Keterangan      |
|--------|-----------------|
| 0      | Tidak Kompeten  |
| 1      | Kompeten        |
| 2      | Tidak Kompeten  |

**Catatan**: Dalam beberapa query, status report 1 = Kompeten, status 0 atau 2 = Tidak Kompeten

## Mapping Status Jadwal

| Status | Keterangan          |
|--------|---------------------|
| 0      | Pending             |
| 1      | Aktif               |
| 2      | Ditunda             |
| 3      | Sedang Berlangsung  |
| 4      | Selesai             |

---

## Formula Umum yang Digunakan

### Pass Rate
```
Pass Rate = (Total Kompeten / Total Ujikom) × 100
```
- Total Kompeten: Report dengan status = 1
- Total Ujikom: Total Kompeten + Total Tidak Kompeten

### Growth Rate
```
Growth Rate = ((Bulan Ini - Bulan Lalu) / Bulan Lalu) × 100
```
- Positif: Pertumbuhan
- Negatif: Penurunan
- 0: Stabil

### Conversion Rate
```
Conversion Rate = (Total Lulus / Total Pendaftaran) × 100
```

### Approval Rate
```
Approval Rate = (Total Diverifikasi / Total Pendaftaran) × 100
```
- Total Diverifikasi: Pendaftaran dengan status >= 3

### Utilisasi Kapasitas
```
Utilisasi Kapasitas = (Jadwal Terisi / Total Jadwal) × 100
```
- Jadwal Terisi: Jadwal yang memiliki minimal 1 pendaftaran

---

## Catatan Penting

1. **Pembulatan**: Semua persentase dibulatkan ke 1 desimal menggunakan `round($value, 1)`
2. **Division by Zero**: Semua perhitungan persentase dilindungi dengan pengecekan pembagi > 0
3. **Filter Tanggal**: Menggunakan `whereMonth()` dan `whereYear()` untuk filter per bulan
4. **Distinct Count**: Untuk menghitung user unik, menggunakan `distinct('user_id')->count('user_id')`
5. **Eager Loading**: Menggunakan `with()` untuk menghindari N+1 query problem
6. **Null Handling**: Semua data nullable di-filter dengan `whereNotNull()` dan `where() != ''`

---

## Diagram Alur Data

### Flow Pendaftaran → Report
```
Pendaftaran (created_at)
    ↓ (status 1-3: Verifikasi)
    ↓ (status 4: Menunggu Ujian)
Jadwal (tanggal_ujian)
    ↓
PendaftaranUjikom (asesor_id)
    ↓ (status 5: Ujian Berlangsung)
    ↓ (status 6: Selesai)
Report (status: 0/1/2)
    ↓
PembayaranAsesor
```

### Timeline Metrics
```
created_at (Pendaftaran)
    → updated_at (Verifikasi)
    → tanggal_ujian (Jadwal)
    → created_at (Report)
    → updated_at (Report selesai dinilai)
```

---

**Dokumentasi ini menjelaskan alur perhitungan lengkap untuk semua dashboard di sistem SIJIKOMKOM.**

**Versi**: 1.0
**Terakhir Diperbarui**: 2025-11-30
