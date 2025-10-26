# Analytics API Documentation

API Analytics untuk Sijikomkom yang telah dipindahkan dari Python FastAPI ke Laravel.

## Base URL
- Admin: `/admin/analytics`
- Kaprodi: `/kaprodi/analytics`
- Pimpinan: `/pimpinan/analytics`

## Endpoints

### 1. Root Endpoint
**GET** `/`

Mengecek status API dan menampilkan daftar endpoint yang tersedia.

**Response:**
```json
{
    "message": "Sijikomkom Analytics API running",
    "version": "1.0.0",
    "docs": "/analytics/docs",
    "endpoints": {
        "skema-trend": "/analytics/skema-trend",
        "kompetensi-skema": "/analytics/kompetensi-skema",
        "segmentasi-demografi": "/analytics/segmentasi-demografi",
        "workload-asesor": "/analytics/workload-asesor",
        "dashboard-summary": "/analytics/dashboard-summary",
        "debug-tables": "/analytics/debug-tables"
    }
}
```

### 2. Health Check
**GET** `/health`

Mengecek kesehatan API.

**Response:**
```json
{
    "status": "healthy",
    "message": "API is running normally"
}
```

### 3. Trend Pendaftaran Skema
**GET** `/skema-trend`

Mendapatkan trend pendaftaran berdasarkan skema dan periode waktu.

**Query Parameters:**
- `skema_id` (optional): ID skema untuk filter
- `start_date` (optional): Tanggal mulai filter (format: YYYY-MM-DD)
- `end_date` (optional): Tanggal akhir filter (format: YYYY-MM-DD)

**Example Request:**
```
GET /admin/analytics/skema-trend?skema_id=1&start_date=2024-01-01&end_date=2024-12-31
```

**Response:**
```json
[
    {
        "month": "2024-01",
        "total_pendaftaran": 10
    },
    {
        "month": "2024-02",
        "total_pendaftaran": 15
    }
]
```

### 4. Statistik Kompetensi Skema
**GET** `/kompetensi-skema`

Mendapatkan statistik kompetensi berdasarkan skema dan status pendaftaran.

**Query Parameters:**
- `start_date` (optional): Tanggal mulai filter (format: YYYY-MM-DD)
- `end_date` (optional): Tanggal akhir filter (format: YYYY-MM-DD)

**Example Request:**
```
GET /admin/analytics/kompetensi-skema?start_date=2024-01-01&end_date=2024-12-31
```

**Response:**
```json
{
    "1": {
        "pending": 5,
        "approved": 10,
        "rejected": 2
    },
    "2": {
        "pending": 3,
        "approved": 8,
        "rejected": 1
    }
}
```

### 5. Segmentasi Demografi
**GET** `/segmentasi-demografi`

Mendapatkan data segmentasi demografi pengguna.

**Response:**
```json
{
    "jenis_kelamin": {
        "L": 100,
        "P": 80,
        "Tidak Diketahui": 5
    },
    "pendidikan": {
        "SMA": 50,
        "S1": 100,
        "S2": 30,
        "Tidak Diketahui": 5
    },
    "pekerjaan": {
        "Mahasiswa": 80,
        "Karyawan": 100,
        "Tidak Diketahui": 5
    }
}
```

### 6. Workload Asesor
**GET** `/workload-asesor`

Mendapatkan data workload asesor berdasarkan jumlah laporan dan pembayaran.

**Query Parameters:**
- `start_date` (optional): Tanggal mulai filter (format: YYYY-MM-DD)
- `end_date` (optional): Tanggal akhir filter (format: YYYY-MM-DD)

**Example Request:**
```
GET /admin/analytics/workload-asesor?start_date=2024-01-01&end_date=2024-12-31
```

**Response:**
```json
[
    {
        "asesor_name": "Nama Asesor 1",
        "jumlah_laporan": 10,
        "jumlah_pembayaran": 5
    },
    {
        "asesor_name": "Nama Asesor 2",
        "jumlah_laporan": 8,
        "jumlah_pembayaran": 3
    }
]
```

### 7. Tren Peminat Skema
**GET** `/tren-peminat-skema`

Mendapatkan tren peminat skema dari waktu ke waktu.

**Query Parameters:**
- `start_date` (optional): Tanggal mulai filter (format: YYYY-MM-DD)
- `end_date` (optional): Tanggal akhir filter (format: YYYY-MM-DD)

**Example Request:**
```
GET /admin/analytics/tren-peminat-skema?start_date=2024-01-01&end_date=2024-12-31
```

**Response:**
```json
[
    {
        "skema_id": 1,
        "skema_name": "Nama Skema 1",
        "trend": [
            {
                "period": "2024-01",
                "registrations": 10
            },
            {
                "period": "2024-02",
                "registrations": 15
            }
        ]
    }
]
```

### 8. Dashboard Summary
**GET** `/dashboard-summary`

Mendapatkan ringkasan data untuk dashboard utama.

**Response:**
```json
{
    "total_pendaftaran": 1000,
    "total_skema": 50,
    "total_asesor": 20,
    "pendaftaran_bulan_ini": 100
}
```

### 9. Debug Tables
**GET** `/debug-tables`

Mendapatkan informasi struktur tabel database untuk debugging.

**Response:**
```json
{
    "tables": {
        "users": ["id", "jenis_kelamin", "pendidikan", "pekerjaan", "tanggal_lahir"],
        "skema": ["id", "nama", "kode"],
        "pendaftaran": ["id", "jadwal_id", "user_id", "skema_id", "status", "created_at"],
        "tuk": ["id", "name"],
        "report": ["id", "tuk_id", "created_at"],
        "pembayaran_asesor": ["id", "asesor_id", "bukti_pembayaran", "status", "created_at"]
    },
    "message": "Struktur tabel database"
}
```

### 10. Dashboard Data (Legacy)
**GET** `/dashboard-data`

Mendapatkan semua data analytics untuk dashboard frontend (method legacy untuk kompatibilitas).

**Response:**
```json
{
    "success": true,
    "data": {
        "skema_trend": [...],
        "kompetensi_skema": {...},
        "segmentasi_demografi": {...},
        "workload_asesor": [...],
        "dashboard_summary": {...}
    },
    "message": "Data dashboard berhasil dimuat"
}
```

### 11. Clear Cache
**POST** `/clear-cache`

Menghapus cache analytics.

**Response:**
```json
{
    "success": true,
    "message": "Cache analytics berhasil dihapus"
}
```

## Error Handling

Semua endpoint mengembalikan error dengan format:

```json
{
    "error": "Error message description"
}
```

**HTTP Status Codes:**
- `200`: Success
- `422`: Validation Error
- `500`: Internal Server Error

## Authentication

Semua endpoint memerlukan autentikasi dan otorisasi sesuai dengan role user:
- Admin: `/admin/analytics/*`
- Kaprodi: `/kaprodi/analytics/*`
- Pimpinan: `/pimpinan/analytics/*`

## Migration Notes

API ini telah dipindahkan dari Python FastAPI ke Laravel dengan fitur-fitur berikut:
- ✅ Semua endpoint dari Python API
- ✅ Validasi input menggunakan Laravel Request classes
- ✅ Error handling yang konsisten
- ✅ Logging untuk debugging
- ✅ Support untuk multiple user roles (Admin, Kaprodi, Pimpinan)
- ✅ Carbon date parsing untuk konsistensi
- ✅ Database query optimization menggunakan Eloquent ORM

## Testing

Untuk test endpoint, gunakan tools seperti Postman, curl, atau browser:

```bash
# Test root endpoint
curl http://localhost:8000/admin/analytics/

# Test health check
curl http://localhost:8000/admin/analytics/health

# Test skema trend
curl "http://localhost:8000/admin/analytics/skema-trend?start_date=2024-01-01&end_date=2024-12-31"
```
