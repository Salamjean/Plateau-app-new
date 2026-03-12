<?php

namespace App\Http\Middleware;

use App\Models\MaintenanceSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiMaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le mode maintenance API est activé
        if (MaintenanceSetting::isApiMaintenanceActive()) {

            // Autoriser si l'utilisateur est un administrateur connecté ou possède un bypass
            if (auth()->guard('admin')->check() || request()->session()->has('maintenance_bypass')) {
                return $next($request);
            }
            // Récupérer le message personnalisé
            $setting = MaintenanceSetting::getSetting('api_maintenance');
            $message = $setting ? $setting->message : 'L\'application est en maintenance.';

            return response()->json([
                'success' => false,
                'maintenance' => true,
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
