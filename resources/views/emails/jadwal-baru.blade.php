<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Ujian Kompetensi Baru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fc;
            padding: 20px;
            border: 1px solid #e3e6f0;
        }
        .jadwal-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #4e73df;
        }
        .jadwal-info h3 {
            margin-top: 0;
            color: #4e73df;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .footer {
            background-color: #858796;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #4e73df;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
        .btn:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Jadwal Ujian Kompetensi Baru</h1>
        <p>Sistem Informasi Ujian Kompetensi</p>
    </div>

    <div class="content">
        <p>Halo,</p>

        <p>Telah dibuat jadwal ujian kompetensi baru yang memerlukan konfirmasi dari Anda sebagai Kepala TUK.</p>

        <div class="jadwal-info">
            <h3>Detail Jadwal Ujian</h3>

            <div class="info-row">
                <span class="info-label">Skema:</span>
                <span class="info-value">{{ $jadwal->skema->nama }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">TUK:</span>
                <span class="info-value">{{ $jadwal->tuk->nama }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Tanggal Ujian:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d/m/Y') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Tanggal Selesai:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d/m/Y') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Tanggal Maksimal Pendaftaran:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($jadwal->tanggal_maksimal_pendaftaran)->format('d/m/Y') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Kuota:</span>
                <span class="info-value">{{ $jadwal->kuota }} peserta</span>
            </div>

            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ $jadwal->status_text }}</span>
            </div>
        </div>

        <p>Silakan login ke sistem untuk melakukan konfirmasi jadwal ini.</p>

        <p>Terima kasih,<br>
        Tim Sistem Informasi Ujian Kompetensi</p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis dari sistem. Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} Sistem Informasi Ujian Kompetensi. All rights reserved.</p>
    </div>
</body>
</html>
