<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre compte a bien été supprimé',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-deleted',
            with: [
                'userName' => $this->userName,
            ],
        );
    }
}
