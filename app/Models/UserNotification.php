<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'demande_id',
        'reference',
        'ancien_statut',
        'nouveau_statut',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les notifications non lues
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Créer une notification de changement de statut avec messages professionnels
     */
    public static function notifyStatusChange($userId, $type, $demandeId, $reference, $ancienStatut, $nouveauStatut)
    {
        $typeLabels = [
            'naissance' => "extrait de naissance",
            'mariage' => "extrait de mariage",
            'deces' => "extrait de décès",
            'rendezvous' => "rendez-vous",
        ];

        $typeLabel = $typeLabels[$type] ?? $type;
        
        // Messages professionnels selon le nouveau statut
        switch ($nouveauStatut) {
            case 'réçu':
                $message = "Votre demande d'{$typeLabel} (Réf: {$reference}) a été bien reçue par la mairie et est en cours de traitement.";
                break;
            case 'terminé':
                $message = "Votre demande d'{$typeLabel} (Réf: {$reference}) a été traitée avec succès.";
                break;
            case 'rejetée':
                $message = "Votre demande d'{$typeLabel} (Réf: {$reference}) a été rejetée. Vous pouvez maintenant modifier les informations incorrectes.";
                break;
            case 'livré':
                $message = "Votre {$typeLabel} (Réf: {$reference}) vous a été remis avec succès.";
                break;
            case 'confirmé':
                $message = "Votre demande de {$typeLabel} (Réf: {$reference}) a été confirmée par la mairie.";
                break;
            case 'annulé':
                $message = "Votre {$typeLabel} (Réf: {$reference}) a été annulé.";
                break;
            case 'modifié':
                $message = "Votre {$typeLabel} (Réf: {$reference}) a été modifié.";
                break;
            default:
                $message = "Le statut de votre demande d'{$typeLabel} (Réf: {$reference}) a été mis à jour.";
        }

        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'demande_id' => $demandeId,
            'reference' => $reference,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Obtenir l'URL de détail selon le type
     */
    public function getDetailUrl()
    {
        try {
            switch ($this->type) {
                case 'naissance':
                    return route('user.extrait.index');
                case 'mariage':
                    return route('user.extrait.mariage.index');
                case 'deces':
                    return route('user.extrait.deces.index');
                case 'rendezvous':
                    return route('user.rendezvous.index');
                default:
                    return route('user.dashboard');
            }
        } catch (\Exception $e) {
            return route('user.dashboard');
        }
    }

    /**
     * Obtenir l'icône selon le type
     */
    public function getIcon()
    {
        switch ($this->type) {
            case 'naissance':
                return 'fa-baby';
            case 'mariage':
                return 'fa-ring';
            case 'deces':
                return 'fa-cross';
            case 'rendezvous':
                return 'fa-calendar-check';
            default:
                return 'fa-bell';
        }
    }

    /**
     * Obtenir la couleur selon le type
     */
    public function getColor()
    {
        switch ($this->type) {
            case 'naissance':
                return '#4a90e2';
            case 'mariage':
                return '#28a745';
            case 'deces':
                return '#ff0854';
            case 'rendezvous':
                return '#ffc107';
            default:
                return '#1977cc';
        }
    }
}
