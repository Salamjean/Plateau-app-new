<?php

namespace App\Notifications;

use App\Channels\FcmChannel;
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
        $channels = ['mail', 'database'];
        if ($notifiable->push_notification) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
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

    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Demande reçue ✔',
            'body'  => 'Votre demande d\'extrait de décès a bien été enregistrée. Réf : ' . ($this->deces->reference ?? ''),
            'data'  => [
                'type' => 'demande_deces',
                'id'   => (string) ($this->deces->id ?? ''),
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'Demande reçue ✔',
            'body'      => 'Votre demande d\'extrait de décès a bien été enregistrée. Réf : ' . ($this->deces->reference ?? ''),
            'type'      => 'demande_deces',
            'demande_id'=> $this->deces->id ?? null,
        ];
    }
}