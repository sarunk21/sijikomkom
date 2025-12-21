<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pendaftaran Tidak Lolos Kelayakan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
        <div style="background-color: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h2 style="color: #dc3545; margin-top: 0;">✗ Pemberitahuan: Pendaftaran Tidak Lolos Kelayakan</h2>
            
            <p>Yth. {{ $pendaftaran->user->name }},</p>
            
            <p>Mohon maaf, pendaftaran Anda untuk ujian kompetensi <strong>tidak dapat diproses lebih lanjut</strong> karena tidak memenuhi kriteria kelayakan yang telah ditentukan.</p>
            
            <div style="background-color: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #dc3545;">
                <h3 style="margin-top: 0; color: #721c24;">Detail Pendaftaran:</h3>
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
                        <td style="padding: 5px 0;"><strong>Status</strong></td>
                        <td style="padding: 5px 0;">: Tidak Lolos Kelayakan</td>
                    </tr>
                    @if($catatan)
                    <tr>
                        <td style="padding: 5px 0; vertical-align: top;"><strong>Alasan</strong></td>
                        <td style="padding: 5px 0;">: {{ $catatan }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;">
                <p style="margin: 0;"><strong>Apa yang harus dilakukan?</strong></p>
                <ul style="margin: 10px 0;">
                    <li>Periksa dan lengkapi dokumen persyaratan yang kurang</li>
                    <li>Pastikan semua dokumen sesuai dengan ketentuan</li>
                    <li>Anda dapat mendaftar ulang setelah melengkapi persyaratan</li>
                </ul>
            </div>
            
            <p>Jika Anda memiliki pertanyaan atau membutuhkan klarifikasi lebih lanjut, silakan hubungi admin.</p>
            
            <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
            
            <p style="font-size: 12px; color: #666;">
                Email ini dikirim secara otomatis oleh Sistem Sertifikasi Kompetensi.<br>
                Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>

