<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MenungguPembayaranMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftaran;
    public $pembayaran;

    /**
     * Create a new message instance.
     */
    public function __construct(Pendaftaran $pendaftaran, Pembayaran $pembayaran)
    {
        $this->pendaftaran = $pendaftaran;
        $this->pembayaran = $pembayaran;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Pendaftaran Disetujui - Silakan Lakukan Pembayaran')
            ->view('emails.menunggu-pembayaran')
            ->with([
                'pendaftaran' => $this->pendaftaran,
                'pembayaran' => $this->pembayaran,
            ]);
    }
}

