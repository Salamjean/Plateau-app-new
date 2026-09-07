<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailToAgentForPasswordResetNotification extends Notification
{
    use Queueable;

    public $code;
    public $email;
    public $logoUrl;

    public function __construct($codeToSend, $sendToemail)
    {
        $this->code = $codeToSend;
        $this->email = $sendToemail;
        $this->logoUrl = asset('assets/assets/img/logo plateau.png');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plateau-Apps : Demande de réinitialisation de mot de passe (Agent)')
            ->from('infos@plateau-apps.com', 'Plateau-Apps')
            ->view('emails.agent_password_reset', [
                'code' => $this->code,
                'email' => $this->email,
                'logoUrl' => $this->logoUrl,
            ]);
    }
}
