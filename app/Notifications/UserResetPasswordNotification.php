<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $logoUrl;

    /**
     * Crée une nouvelle instance de notification.
     *
     * @param string $token Le token de réinitialisation.
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->logoUrl = asset('assets/assets/img/logoplateau.png');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Construit la représentation mail de la notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // On construit l'URL de réinitialisation ici
        $resetUrl = url(route('user.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));

        // On appelle la nouvelle vue Blade et on lui passe toutes les données nécessaires
        return (new MailMessage)
            ->subject('Plateau-Apps : Réinitialisation de votre mot de passe')
            ->from('infos@plateau-apps.com', 'Plateau-Apps')
            ->view('emails.web_password_reset', [
                'userName' => $notifiable->name,
                'resetUrl' => $resetUrl,
                'expirationMinutes' => config('auth.passwords.users.expire'),
                'logoUrl' => $this->logoUrl,
            ]);
    }
}