<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecondRegistrationService
{
    /**
     * Cek apakah user sudah pernah mendaftar sebelumnya
     */
    public function hasPreviousRegistration($userId = null)
    {
        $userId = $userId ?? Auth::id();
        return Pendaftaran::where('user_id', $userId)->exists();
    }

    /**
     * Cek status pembayaran terakhir
     */
    public function getLastPaymentStatus($userId = null)
    {
        $userId = $userId ?? Auth::id();

        $lastPayment = Pembayaran::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastPayment ? $lastPayment->status : null;
    }

    /**
     * Cek apakah user bisa mendaftar lagi
     */
    public function canRegisterAgain($userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$this->hasPreviousRegistration($userId)) {
            return true; // Belum pernah daftar, bisa daftar
        }

        $lastPaymentStatus = $this->getLastPaymentStatus($userId);

        // Bisa daftar lagi jika:
        // 1. Belum pernah ada pembayaran
        // 2. Pembayaran sudah dikonfirmasi (status 4)
        // 3. Pembayaran ditolak (status 3)
        return in_array($lastPaymentStatus, [null, 3, 4]);
    }

    /**
     * Buat pembayaran untuk pendaftaran kedua
     */
    public function createSecondRegistrationPayment($jadwalId, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return DB::transaction(function () use ($jadwalId, $userId) {
            // Cek apakah sudah ada pembayaran untuk jadwal ini
            $existingPayment = Pembayaran::where('user_id', $userId)
                ->where('jadwal_id', $jadwalId)
                ->first();

            // Jika pembayaran sudah ada, cek statusnya
            if ($existingPayment) {
                // Jika pembayaran ditolak (status 3), hapus dan buat baru
                if ($existingPayment->status == 3) {
                    $existingPayment->delete();
                }
                // Jika pembayaran dikonfirmasi (status 4), cek apakah pendaftaran ditolak kaprodi
                elseif ($existingPayment->status == 4) {
                    $registration = Pendaftaran::where('user_id', $userId)
                        ->where('jadwal_id', $jadwalId)
                        ->first();

                    // Jika pendaftaran ditolak kaprodi (status 2), hapus pembayaran dan pendaftaran lama
                    if ($registration && $registration->status == 2) {
                        $registration->delete();
                        $existingPayment->delete();
                    } else {
                        throw new \Exception('Anda sudah mendaftar untuk jadwal ini.');
                    }
                }
                // Status lain (1, 2) tidak boleh daftar lagi
                else {
                    throw new \Exception('Anda memiliki pembayaran yang belum diselesaikan untuk jadwal ini. Silakan selesaikan pembayaran terlebih dahulu.');
                }
            }

            // Tentukan status pembayaran berdasarkan riwayat
            $hasPreviousRegistration = $this->hasPreviousRegistration($userId);
            $lastPaymentStatus = $this->getLastPaymentStatus($userId);

            $status = 1; // Default: Belum Bayar
            $keterangan = 'Pendaftaran Pertama';

            if ($hasPreviousRegistration) {
                $keterangan = 'Pendaftaran Kedua';
                if ($lastPaymentStatus == 4) {
                    // Jika pembayaran sebelumnya sudah dikonfirmasi, langsung status 1 (Belum Bayar - perlu upload bukti)
                    $status = 1;
                } else {
                    // Jika belum pernah bayar atau ditolak, status 1 (Belum Bayar)
                    $status = 1;
                }
            } else {
                // Pendaftaran pertama, langsung status 4 (Dikonfirmasi) - gratis/otomatis approve
                $status = 4;
            }

            $payment = Pembayaran::create([
                'user_id' => $userId,
                'jadwal_id' => $jadwalId,
                'status' => $status,
                'keterangan' => $keterangan
            ]);

            // Jika pendaftaran pertama (status 4), langsung buat pendaftaran
            if ($status == 4) {
                $this->createRegistrationFromPayment($payment);
            }

            return $payment;
        });
    }

    /**
     * Dapatkan informasi pendaftaran kedua
     */
    public function getSecondRegistrationInfo($userId = null)
    {
        $userId = $userId ?? Auth::id();

        $previousRegistration = Pendaftaran::where('user_id', $userId)->first();
        $lastPayment = Pembayaran::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'has_previous_registration' => $this->hasPreviousRegistration($userId),
            'can_register_again' => $this->canRegisterAgain($userId),
            'last_payment_status' => $lastPayment ? $lastPayment->status : null,
            'last_payment_status_text' => $lastPayment ? $lastPayment->status_text : null,
            'previous_registration' => $previousRegistration,
            'last_payment' => $lastPayment
        ];
    }

    /**
     * Verifikasi pembayaran untuk pendaftaran kedua
     */
    public function verifySecondRegistrationPayment($paymentId, $status, $keterangan = null)
    {
        return DB::transaction(function () use ($paymentId, $status, $keterangan) {
            $payment = Pembayaran::findOrFail($paymentId);

            $payment->update([
                'status' => $status,
                'keterangan' => $keterangan
            ]);

            // Jika pembayaran dikonfirmasi, buat pendaftaran
            if ($status == 4) {
                $this->createRegistrationFromPayment($payment);
            }

            return $payment;
        });
    }

    /**
     * Buat pendaftaran dari pembayaran yang sudah dikonfirmasi
     */
    private function createRegistrationFromPayment($payment)
    {
        // Cek apakah sudah ada pendaftaran untuk jadwal ini
        $existingRegistration = Pendaftaran::where('user_id', $payment->user_id)
            ->where('jadwal_id', $payment->jadwal_id)
            ->first();

        if (!$existingRegistration) {
            Pendaftaran::create([
                'user_id' => $payment->user_id,
                'jadwal_id' => $payment->jadwal_id,
                'skema_id' => $payment->jadwal->skema_id,
                'tuk_id' => $payment->jadwal->tuk_id,
                'status' => 1 // Menunggu Verifikasi Kaprodi
            ]);
        }
    }
}
