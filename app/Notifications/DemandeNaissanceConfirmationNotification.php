<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeNaissanceConfirmationNotification extends Notification
{
    use Queueable;

    public $user;
    public $naissance;
    public $logoUrl;

    public function __construct($user, $naissance)
    {
        $this->user = $user;
        $this->naissance = $naissance;
        $this->logoUrl = asset('assets/assets/img/logoplateau.png');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plateau-Apps : Confirmation de votre demande d\'extrait de naissance')
            ->from('contact@maelysimo.com', 'Plateau-Apps')
            ->view('emails.demande_naissance_confirmation', [
                'user' => $this->user,
                'naissance' => $this->naissance,
                'logoUrl' => $this->logoUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}