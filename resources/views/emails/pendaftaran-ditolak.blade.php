<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ujian Kompetensi Ditolak</title>
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
            background-color: #e74a3b;
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
        .status-badge {
            background-color: #e74a3b;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
            margin: 10px 0;
        }
        .pendaftaran-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #e74a3b;
        }
        .pendaftaran-info h3 {
            margin-top: 0;
            color: #e74a3b;
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
        .alasan-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .alasan-box h4 {
            margin-top: 0;
            color: #856404;
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
        .warning-text {
            color: #e74a3b;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pendaftaran Ujian Kompetensi Ditolak</h1>
        <p>Sistem Informasi Ujian Kompetensi</p>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $pendaftaran->user->name }}</strong>,</p>

        <p>Mohon maaf, pendaftaran Anda untuk ujian kompetensi telah <span class="warning-text">DITOLAK</span> oleh Kaprodi.</p>

        <div class="status-badge">STATUS: DITOLAK</div>

        <div class="pendaftaran-info">
            <h3>Detail Pendaftaran</h3>

            <div class="info-row">
                <span class="info-label">Nama:</span>
                <span class="info-value">{{ $pendaftaran->user->name }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">NIM:</span>
                <span class="info-value">{{ $pendaftaran->user->nim }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Skema:</span>
                <span class="info-value">{{ $pendaftaran->jadwal->skema->nama }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">TUK:</span>
                <span class="info-value">{{ $pendaftaran->jadwal->tuk->nama }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Tanggal Ujian:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian)->format('d/m/Y H:i') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Tanggal Pendaftaran:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($pendaftaran->created_at)->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        @if($pendaftaran->keterangan)
        <div class="alasan-box">
            <h4>Alasan Penolakan:</h4>
            <p>{{ $pendaftaran->keterangan }}</p>
        </div>
        @endif

        <p><strong>Langkah selanjutnya:</strong></p>
        <ul>
            <li>Perbaiki dokumen atau data yang kurang sesuai dengan alasan penolakan</li>
            <li>Pastikan semua persyaratan pendaftaran terpenuhi</li>
            <li>Daftar ulang dengan data yang sudah diperbaiki</li>
            <li>Jika ada pertanyaan, silakan hubungi Kaprodi atau admin</li>
        </ul>

        <p>Terima kasih atas pengertiannya,<br>
        Tim Sistem Informasi Ujian Kompetensi</p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis dari sistem. Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} Sistem Informasi Ujian Kompetensi. All rights reserved.</p>
    </div>
</body>
</html>
