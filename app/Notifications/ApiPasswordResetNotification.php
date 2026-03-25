<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApiPasswordResetNotification extends Notification
{
    use Queueable;

    public $token;
    public $logoUrl;

    /**
     * Crée une nouvelle instance de notification.
     *
     * @param string $token Le code de réinitialisation à 6 chiffres.
     */
    public function __construct($token)
    {
        $this->token = $token;
        // Nous définissons l'URL du logo ici pour la passer à la vue
        $this->logoUrl = asset('assets/assets/img/logoplateau.png');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Construit la représentation mail de la notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plateau-Apps : Votre code de réinitialisation')
            ->from('contact@maelysimo.com', 'Plateau-Apps')
            ->view('emails.api_password_reset', [
                'userName' => $notifiable->name,
                'code' => $this->token,
                'expirationMinutes' => config('auth.passwords.users.expire'),
                'logoUrl' => $this->logoUrl,
            ]);
    }
}