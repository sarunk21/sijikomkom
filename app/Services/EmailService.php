<?php

namespace App\Services;

use App\Mail\JadwalBaruMail;
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
            $kepalaTukUsers = User::where('user_type', 'kepala_tuk')->get();

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
}
