<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Récupérer la liste des notifications de l'utilisateur
     */
    public function index()
    {
        try {
            $notifications = UserNotification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'reference' => $notification->reference,
                        'message' => $notification->message,
                        'ancien_statut' => $notification->ancien_statut,
                        'nouveau_statut' => $notification->nouveau_statut,
                        'is_read' => $notification->is_read,
                        'icon' => $notification->getIcon(),
                        'color' => $notification->getColor(),
                        'url' => $notification->getDetailUrl(),
                        'created_at' => $notification->created_at->diffForHumans(),
                        'created_at_full' => $notification->created_at->format('d/m/Y H:i'),
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'notifications' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Compter les notifications non lues
     */
    public function getUnreadCount()
    {
        $count = UserNotification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = UserNotification::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée',
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue',
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->unread()
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues',
        ]);
    }
}
