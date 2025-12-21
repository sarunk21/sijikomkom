<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use App\Models\KelayankanVerifikasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifikasiKelayankanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftaran;
    public $verifikasi;

    /**
     * Create a new message instance.
     */
    public function __construct(Pendaftaran $pendaftaran, KelayankanVerifikasi $verifikasi)
    {
        $this->pendaftaran = $pendaftaran;
        $this->verifikasi = $verifikasi;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verifikasi Kelayakan - Menunggu Approval Admin')
            ->view('emails.verifikasi-kelayakan')
            ->with([
                'pendaftaran' => $this->pendaftaran,
                'verifikasi' => $this->verifikasi,
            ]);
    }
}

