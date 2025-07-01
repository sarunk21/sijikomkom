<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Kehadiran - Ujian Kompetensi</title>
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
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
        }
        .info-box {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .action-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📋 Konfirmasi Kehadiran</h2>
        <h3>Ujian Kompetensi</h3>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $nama }}</strong>,</p>

        <p>Anda telah ditugaskan sebagai <strong>asesor</strong> untuk ujian kompetensi yang akan dilaksanakan. Mohon konfirmasi kehadiran Anda dengan mengklik tombol di bawah ini.</p>

        <div class="info-box">
            <h4>📋 Detail Jadwal Ujian:</h4>
            <ul>
                <li><strong>Skema:</strong> {{ $jadwal->skema->nama }}</li>
                <li><strong>TUK:</strong> {{ $jadwal->tuk->nama }}</li>
                <li><strong>Tanggal Ujian:</strong> {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d-m-Y') }}</li>
                <li><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d-m-Y') }}</li>
                <li><strong>Jumlah Asesi:</strong> {{ $jumlahAsesi }} orang</li>
            </ul>
        </div>

        <div class="action-box">
            <h4>⚡ Tindakan yang Diperlukan:</h4>
            <p>Silakan konfirmasi kehadiran Anda dengan mengakses sistem dan memilih salah satu opsi berikut:</p>

            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ url('/asesor/verifikasi-peserta') }}" class="btn btn-success">
                    ✅ Konfirmasi Dapat Hadir
                </a>
                <a href="{{ url('/asesor/verifikasi-peserta') }}" class="btn btn-danger">
                    ❌ Konfirmasi Tidak Dapat Hadir
                </a>
            </div>
        </div>

        <p><strong>Langkah selanjutnya:</strong></p>
        <ol>
            <li>Login ke sistem sertifikasi kompetensi</li>
            <li>Akses menu "Verifikasi Peserta"</li>
            <li>Pilih jadwal ujian yang sesuai</li>
            <li>Klik tombol konfirmasi kehadiran</li>
            <li>Isi keterangan jika diperlukan</li>
        </ol>

        <p><strong>Penting:</strong></p>
        <ul>
            <li>Konfirmasi kehadiran harus dilakukan secepat mungkin</li>
            <li>Jika tidak dapat hadir, mohon berikan alasan yang jelas</li>
            <li>Tim admin akan menghubungi Anda jika ada perubahan jadwal</li>
        </ul>

        <p>Terima kasih atas kerjasama Anda dalam pelaksanaan ujian kompetensi.</p>

        <p>Salam,<br>
        <strong>Tim Sertifikasi Kompetensi</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        <p>© {{ date('Y') }} Sistem Sertifikasi Kompetensi. All rights reserved.</p>
    </div>
</body>
</html>
