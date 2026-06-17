<?php

namespace App\Notifications;

use App\Mail\VerifyEmailChangeConfirmed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VerifyEmailChangeConfirmedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable)
    {
        return new VerifyEmailChangeConfirmed(
            $notifiable->name,
            $notifiable->email
        );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
