<?php

namespace App\Services;

use App\Mail\JadwalBaruMail;
use App\Mail\PendaftaranDitolakMail;
use App\Mail\AsesorTidakHadirMail;
use App\Mail\AsesorTidakHadirAdminMail;
use App\Mail\KonfirmasiKehadiranMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Kirim email notifikasi jadwal baru ke kepala TUK
     */
    public function sendJadwalBaruNotification($jadwal)
    {
        try {
            // Ambil semua user dengan user_type kepala_tuk
            $kepalaTukUsers = User::where('user_type', 'tuk')->get();

            if ($kepalaTukUsers->isEmpty()) {
                return false;
            }

            // Kirim email ke setiap kepala TUK
            foreach ($kepalaTukUsers as $user) {
                Mail::to($user->email)->send(new JadwalBaruMail($jadwal));
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Kirim email notifikasi pendaftaran ditolak ke asesi
     */
    public function sendPendaftaranDitolakNotification($pendaftaran)
    {
        try {
            // Kirim email ke asesi yang pendaftarannya ditolak
            Mail::to($pendaftaran->user->email)->send(new PendaftaranDitolakMail($pendaftaran));

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Kirim email notifikasi asesor tidak hadir ke asesi
     */
    public function sendAsesorTidakHadirEmail($email, $nama, $data)
    {
        try {
            Mail::to($email)->send(new AsesorTidakHadirMail($nama, $data));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Kirim email notifikasi asesor tidak hadir ke admin
     */
    public function sendAsesorTidakHadirAdminEmail($email, $nama, $data)
    {
        try {
            Mail::to($email)->send(new AsesorTidakHadirAdminMail($nama, $data));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Kirim email konfirmasi kehadiran ke asesor
     */
    public function sendKonfirmasiKehadiranEmail($email, $nama, $jadwal, $jumlahAsesi)
    {
        try {
            Mail::to($email)->send(new KonfirmasiKehadiranMail($nama, $jadwal, $jumlahAsesi));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
