<?php

namespace App\Channels;

use App\Services\FcmService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    public function __construct(protected FcmService $fcmService) {}

    public function send(object $notifiable, Notification $notification): void
    {
        $token = $notifiable->push_notification ?? null;

        if (!$token) {
            return;
        }

        if (!method_exists($notification, 'toFcm')) {
            Log::warning('FcmChannel: la notification ' . get_class($notification) . ' n\'a pas de méthode toFcm().');
            return;
        }

        $message = $notification->toFcm($notifiable);

        $this->fcmService->sendNotification(
            $token,
            $message['title'] ?? 'Plateau-Apps',
            $message['body']  ?? '',
            $message['data']  ?? []
        );
    }
}
