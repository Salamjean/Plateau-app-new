<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeDecesConfirmationNotification extends Notification
{
    use Queueable;

    public $user;
    public $deces;
    public $logoUrl;

    public function __construct($user, $deces)
    {
        $this->user = $user;
        $this->deces = $deces;
        $this->logoUrl = asset('assets/assets/img/logoplateau.png');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plateau-Apps : Confirmation de votre demande d\'extrait de décès')
            ->from('infos@plateau-apps.com', 'Plateau-Apps')
            ->view('emails.demande_deces_confirmation', [
                'user' => $this->user,
                'deces' => $this->deces,
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