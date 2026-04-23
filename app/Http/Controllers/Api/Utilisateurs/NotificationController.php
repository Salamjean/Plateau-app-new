<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Notifications\GeneralPushNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    /**
     * Récupère la liste des notifications de l'utilisateur.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $notifications = $user->notifications()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Liste des notifications récupérée avec succès.',
            'data' => $notifications
        ]);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification introuvable.'
        ], 404);
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues.'
        ]);
    }

    /**
     * Endpoint de test pour l'envoi d'une notification push Firebase FCM.
     */
    public function testPushNotification(Request $request)
    {
        $request->validate([
            'title'   => 'required|string',
            'body'    => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $user = $request->filled('user_id') ? User::find($request->user_id) : $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.',
            ], 404);
        }

        if (empty($user->push_notification)) {
            return response()->json([
                'success' => false,
                'message' => 'L\'utilisateur cible ne possède pas de token FCM configuré (push_notification).',
                'hint'    => 'Envoyez le token FCM depuis l\'app mobile lors du login (champ push_notification), ou via POST /api/utilisateurs/toggle-push-notification.',
            ], 422);
        }

        // Envoi du push via le canal FCM
        $user->notify(new GeneralPushNotification(
            $request->title,
            $request->body,
            ['type' => 'tracking', 'reference' => '1234567890', 'timestamp' => now()->toDateTimeString()]
        ));

        return response()->json([
            'success'     => true,
            'message'     => 'Notification push envoyée avec succès.',
            'token_used'  => $user->push_notification,
            'destination' => 'user_id=' . $user->id . ' (' . ($user->email ?? $user->contact) . ')',
        ]);
    }
}
