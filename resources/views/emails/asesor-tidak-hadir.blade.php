<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Asesor Tidak Dapat Hadir</title>
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
            background-color: #dc3545;
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
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .alert {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>⚠️ Pemberitahuan Penting</h2>
        <h3>Asesor Tidak Dapat Hadir</h3>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $nama }}</strong>,</p>

        <p>Kami ingin memberitahu Anda bahwa asesor yang ditugaskan untuk ujian kompetensi Anda <strong>tidak dapat hadir</strong> pada jadwal yang telah ditentukan.</p>

        <div class="info-box">
            <h4>📋 Detail Jadwal Ujian:</h4>
            <ul>
                <li><strong>Skema:</strong> {{ $data['skema'] }}</li>
                <li><strong>TUK:</strong> {{ $data['tuk'] }}</li>
                <li><strong>Tanggal Ujian:</strong> {{ $data['tanggal_ujian'] }}</li>
                <li><strong>Asesor:</strong> {{ $data['asesor']->name }}</li>
            </ul>
        </div>

        <div class="alert">
            <h4>📝 Alasan Ketidakhadiran:</h4>
            <p><em>{{ $data['alasan'] }}</em></p>
        </div>

        <p><strong>Langkah selanjutnya:</strong></p>
        <ul>
            <li>Tim admin akan segera menghubungi Anda untuk mengatur jadwal ulang</li>
            <li>Mohon bersabar dan tetap memantau email Anda untuk informasi lebih lanjut</li>
            <li>Jika ada pertanyaan, silakan hubungi admin melalui sistem</li>
        </ul>

        <p>Kami mohon maaf atas ketidaknyamanannya dan berterima kasih atas pengertian Anda.</p>

        <p>Salam,<br>
        <strong>Tim Sertifikasi Kompetensi</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        <p>© {{ date('Y') }} Sistem Sertifikasi Kompetensi. All rights reserved.</p>
    </div>
</body>
</html>
