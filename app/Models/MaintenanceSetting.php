<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MaintenanceSetting extends Model
{
    protected $fillable = ['key', 'value', 'message', 'updated_by'];

    protected $casts = [
        'value' => 'boolean',
    ];

    /**
     * Récupérer un paramètre de maintenance par sa clé
     */
    public static function getSetting(string $key): ?self
    {
        return Cache::remember("maintenance_setting_{$key}", 60, function () use ($key) {
            return self::where('key', $key)->first();
        });
    }

    /**
     * Vérifier si le mode maintenance web est activé
     */
    public static function isWebMaintenanceActive(): bool
    {
        $setting = self::getSetting('web_maintenance');
        return $setting ? $setting->value : false;
    }

    /**
     * Vérifier si le mode maintenance API est activé
     */
    public static function isApiMaintenanceActive(): bool
    {
        $setting = self::getSetting('api_maintenance');
        return $setting ? $setting->value : false;
    }

    /**
     * Mettre à jour un paramètre et vider le cache
     */
    public static function updateSetting(string $key, bool $value, ?string $message = null, ?int $adminId = null): bool
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return false;
        }

        $setting->value = $value;
        if ($message !== null) {
            $setting->message = $message;
        }
        $setting->updated_by = $adminId;
        $setting->save();

        // Vider le cache pour ce paramètre
        Cache::forget("maintenance_setting_{$key}");

        return true;
    }
}
