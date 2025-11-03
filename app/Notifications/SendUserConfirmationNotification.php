<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendUserConfirmationNotification extends Notification 
{
    use Queueable;

    public $user;
    public $logoUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->logoUrl = asset('assets/assets/img/logoplateau.png');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plateau-Apps : Confirmation de votre inscription')
            ->from('contact@edemarchee-ci.com', 'Plateau-Apps')
            ->view('emails.user_confirmation', [
                'user' => $this->user,
                'logoUrl' => $this->logoUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}