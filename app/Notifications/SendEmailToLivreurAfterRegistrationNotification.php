<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailToLivreurAfterRegistrationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $code;
    public $email;
    public $logoUrl;

    public function __construct($codeToSend, $sendToemail)
    {
        $this->code = $codeToSend;
        $this->email = $sendToemail;
        $this->logoUrl = asset('assets/assets/img/logoplateau.png'); // URL du logo
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
            ->subject('Plateau-Apps : Vous êtes enregistré auprès de votre mairie') 
            ->from('contact@edemarchee-ci.com', 'Plateau-Apps')
            ->view('emails.livreur_registration', [
                'code' => $this->code,
                'email' => $this->email,
                'logoUrl' => $this->logoUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}