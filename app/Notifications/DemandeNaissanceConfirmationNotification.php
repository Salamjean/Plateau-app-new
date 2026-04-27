<?php

namespace App\Notifications;

use App\Channels\FcmChannel;
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
        $channels = ['mail', 'database'];
        if ($notifiable->push_notification) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plateau-Apps : Confirmation de votre demande d\'extrait de naissance')
            ->from('infos@plateau-apps.com', 'Plateau-Apps')
            ->view('emails.demande_naissance_confirmation', [
                'user' => $this->user,
                'naissance' => $this->naissance,
                'logoUrl' => $this->logoUrl,
            ]);
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Demande reçue ✔',
            'body'  => 'Votre demande d\'extrait de naissance a bien été enregistrée. Réf : ' . ($this->naissance->reference ?? ''),
            'data'  => [
                'type' => 'tracking',
                'id'   => (string) ($this->naissance->id ?? ''),
                'reference'=> $this->naissance->reference ?? null,
            ],
           
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'Demande reçue ✔',
            'body'      => 'Votre demande d\'extrait de naissance a bien été enregistrée. Réf : ' . ($this->naissance->reference ?? ''),
            'type'      => 'tracking',
            'reference'=> $this->naissance->reference ?? null,
        ];
    }
}