<?php

namespace App\Mail;

use App\Models\T02User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class WakifEmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct(T02User $user)
    {
        $this->user = $user;
        $this->verificationUrl = URL::temporarySignedRoute(
            'wakif.verify-email',
            now()->addHours(24),
            ['id' => $user->id_user, 'hash' => sha1($user->email)]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Amanah Baiturrahman] Verifikasi Email Akun Wakif Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wakif_email_verification',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
