<?php

namespace App\Mail;

use App\Models\T04Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class TransaksiApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaksi;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(T04Transaksi $transaksi, $pdfContent)
    {
        $this->transaksi = $transaksi;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Amanah Baiturrahman] Kuitansi Resmi Penerimaan Wakaf',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.transaksi_approved',
            with: [
                'transaksi' => $this->transaksi,
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
        $filename = 'Kuitansi-Wakaf-' . ($this->transaksi->kode_referensi ?? ('WKF-' . str_pad($this->transaksi->id_transaksi, 6, '0', STR_PAD_LEFT))) . '.pdf';
        
        return [
            Attachment::fromData(
                fn () => $this->pdfContent,
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
