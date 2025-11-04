<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeMariageConfirmationNotification extends Notification
{
    use Queueable;

    public $user;
    public $mariage;
    public $logoUrl;

    public function __construct($user, $mariage)
    {
        $this->user = $user;
        $this->mariage = $mariage;
        $this->logoUrl = asset('assets/assets/img/logoplateau.png');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plateau-Apps : Confirmation de votre demande d\'extrait de mariage')
            ->from('infos@plateau-apps.com', 'Plateau-Apps')
            ->view('emails.demande_mariage_confirmation', [
                'user' => $this->user,
                'mariage' => $this->mariage,
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