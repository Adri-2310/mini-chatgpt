<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject('Réinitialiser votre mot de passe')
            ->line('🌶️ **SaveurIA**')
            ->line('Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
            ->action('Réinitialiser le mot de passe', url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false)))
            ->line('Ce lien de réinitialisation expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas demandé une réinitialisation de mot de passe, aucune action n\'est nécessaire.')
            ->salutation('Cordialement, SaveurIA');
    }
}
