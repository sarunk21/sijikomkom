<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Menunggu Pembayaran</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
        <div style="background-color: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h2 style="color: #28a745; margin-top: 0;">✓ Selamat! Pendaftaran Anda Disetujui</h2>
            
            <p>Yth. {{ $pendaftaran->user->name }},</p>
            
            <p>Selamat! Pendaftaran ujian kompetensi Anda telah <strong>disetujui</strong> dan telah lolos verifikasi kelayakan.</p>
            
            <div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #2e7d32;">Detail Pendaftaran:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; width: 40%;"><strong>Nama</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>Skema</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->skema->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>TUK</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->tuk->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>Jadwal Ujian</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian)->format('d M Y') : '-' }}</td>
                    </tr>
                </table>
            </div>
            
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;">
                <h3 style="margin-top: 0; color: #856404;">⚠️ Langkah Selanjutnya:</h3>
                <p style="margin: 0;"><strong>Silakan lakukan pembayaran untuk melanjutkan proses pendaftaran</strong></p>
                <ol style="margin: 10px 0;">
                    <li>Login ke sistem</li>
                    <li>Buka menu "Informasi Pembayaran"</li>
                    <li>Upload bukti pembayaran</li>
                    <li>Tunggu verifikasi dari admin</li>
                </ol>
            </div>
            
            <p style="margin-top: 30px;">
                <a href="{{ url('/asesi/informasi-pembayaran') }}" style="display: inline-block; padding: 12px 30px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Upload Bukti Pembayaran
                </a>
            </p>
            
            <p style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <strong>Catatan:</strong> Pembayaran harus diselesaikan sebelum tanggal ujian. Pendaftaran yang belum melakukan pembayaran tidak akan dapat mengikuti ujian.
            </p>
            
            <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
            
            <p style="font-size: 12px; color: #666;">
                Email ini dikirim secara otomatis oleh Sistem Sertifikasi Kompetensi.<br>
                Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>

