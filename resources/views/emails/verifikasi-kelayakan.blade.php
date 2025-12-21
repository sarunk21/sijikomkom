<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Kelayakan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
        <div style="background-color: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h2 style="color: #28a745; margin-top: 0;">✓ Verifikasi Kelayakan - Menunggu Approval Admin</h2>
            
            <p>Yth. Admin,</p>
            
            <p>Pendaftaran ujikom telah diverifikasi oleh asesor dan dinyatakan <strong>LAYAK</strong>. Mohon untuk melakukan approval akhir.</p>
            
            <div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #2e7d32;">Detail Pendaftaran:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; width: 40%;"><strong>Nama Asesi</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>NIM</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->user->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>Skema</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->skema->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>Jadwal Ujian</strong></td>
                        <td style="padding: 5px 0;">: {{ $pendaftaran->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian)->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>Diverifikasi oleh</strong></td>
                        <td style="padding: 5px 0;">: {{ $verifikasi->asesor->name }}</td>
                    </tr>
                    @if($verifikasi->catatan)
                    <tr>
                        <td style="padding: 5px 0; vertical-align: top;"><strong>Catatan Asesor</strong></td>
                        <td style="padding: 5px 0;">: {{ $verifikasi->catatan }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <p style="margin-top: 30px;">
                <a href="{{ url('/admin/kelayakan') }}" style="display: inline-block; padding: 12px 30px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Lihat Pendaftaran
                </a>
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

