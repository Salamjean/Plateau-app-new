<?php

namespace App\Notifications;

use App\Channels\FcmChannel;
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
        $channels = ['mail', 'database'];
        if ($notifiable->push_notification) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
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

    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Demande reçue ✔',
            'body'  => 'Votre demande d\'extrait de mariage a bien été enregistrée. Réf : ' . ($this->mariage->reference ?? ''),
            'data'  => [
                'type' => 'tracking',
                'id'   => (string) ($this->mariage->id ?? ''),
                'reference'=> $this->mariage->reference ?? null,
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'Demande reçue ✔',
            'body'      => 'Votre demande d\'extrait de mariage a bien été enregistrée. Réf : ' . ($this->mariage->reference ?? ''),
            'type'      => 'tracking',
            'reference'=> $this->mariage->reference ?? null,
        ];
    }
}