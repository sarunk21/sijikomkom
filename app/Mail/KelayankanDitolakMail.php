<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KelayankanDitolakMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftaran;
    public $catatan;

    /**
     * Create a new message instance.
     */
    public function __construct(Pendaftaran $pendaftaran, $catatan = null)
    {
        $this->pendaftaran = $pendaftaran;
        $this->catatan = $catatan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Pemberitahuan: Pendaftaran Tidak Lolos Kelayakan')
            ->view('emails.kelayakan-ditolak')
            ->with([
                'pendaftaran' => $this->pendaftaran,
                'catatan' => $this->catatan,
            ]);
    }
}

