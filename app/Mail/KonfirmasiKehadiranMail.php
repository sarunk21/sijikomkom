<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KonfirmasiKehadiranMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $jadwal;
    public $jumlahAsesi;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $jadwal, $jumlahAsesi)
    {
        $this->nama = $nama;
        $this->jadwal = $jadwal;
        $this->jumlahAsesi = $jumlahAsesi;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Kehadiran - Ujian Kompetensi',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.konfirmasi-kehadiran',
            with: [
                'nama' => $this->nama,
                'jadwal' => $this->jadwal,
                'jumlahAsesi' => $this->jumlahAsesi,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
