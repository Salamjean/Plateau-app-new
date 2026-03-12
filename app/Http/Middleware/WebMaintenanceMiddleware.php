<?php

namespace App\Http\Middleware;

use App\Models\MaintenanceSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WebMaintenanceMiddleware
{
    /**
     * Routes qui ne doivent JAMAIS être bloquées (login admin, etc.)
     */
    protected array $excludedPaths = [
        'admin/*',
        'admin',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le mode maintenance web est activé
        if (MaintenanceSetting::isWebMaintenanceActive()) {
            
            // 1. Toujours autoriser les routes admin
            foreach ($this->excludedPaths as $path) {
                if ($request->is($path)) {
                    return $next($request);
                }
            }

            // 2. Autoriser si l'utilisateur est un administrateur connecté (même s'il est sur le frontend)
            if (Auth::guard('admin')->check()) {
                return $next($request);
            }

            // 3. Autoriser si une session de bypass est active (permet de tester en tant qu'utilisateur standard)
            if ($request->session()->has('maintenance_bypass')) {
                return $next($request);
            }

            // Bloquer TOUS les autres utilisateurs, même s'ils sont connectés
            // Seuls les admins sont exemptés (et ils utilisent les routes /admin/*)
            
            // Récupérer le message personnalisé
            $setting = MaintenanceSetting::getSetting('web_maintenance');
            $message = $setting ? $setting->message : 'Le site est en maintenance.';

            return response()->view('errors.maintenance-mode', [
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
