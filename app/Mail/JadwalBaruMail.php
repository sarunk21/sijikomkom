<?php

namespace App\Mail;

use App\Models\Jadwal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JadwalBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public $jadwal;

    /**
     * Create a new message instance.
     */
    public function __construct(Jadwal $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jadwal Ujian Kompetensi Baru - ' . $this->jadwal->skema->nama,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.jadwal-baru',
            with: [
                'jadwal' => $this->jadwal,
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
