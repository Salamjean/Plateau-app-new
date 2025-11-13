<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ApiPasswordResetNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Votre code de réinitialisation de mot de passe'))
            ->greeting(Lang::get('Bonjour !'))
            ->line(Lang::get('Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectuée pour votre compte.'))
            ->line(Lang::get('Utilisez le code ci-dessous pour réinitialiser votre mot de passe :')) // Phrase modifiée
            ->line(new \Illuminate\Support\HtmlString('<div style="font-size: 24px; letter-spacing: 5px; font-weight: bold; text-align: center; margin: 20px 0;">' . $this->token . '</div>')) // On ajoute un peu d'espacement pour la lisibilité
            ->line(Lang::get('Ce code expirera dans :count minutes.', ['count' => config('auth.passwords.users.expire')]))
            ->line(Lang::get('Si vous n\'avez pas demandé de réinitialisation, ignorez simplement cet email.'))
            ->salutation(Lang::get('Cordialement,<br>L\'équipe de la Plateforme'));
    }
}