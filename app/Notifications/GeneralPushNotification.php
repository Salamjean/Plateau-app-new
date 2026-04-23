<?php

namespace App\Notifications;

use App\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralPushNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $body,
        protected array $data = []
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->push_notification) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => $this->data,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => $this->data,
        ];
    }
}
