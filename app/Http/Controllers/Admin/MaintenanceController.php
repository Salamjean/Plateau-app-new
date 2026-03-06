<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Afficher la page de gestion de maintenance
     */
    public function index()
    {
        $webMaintenance = MaintenanceSetting::where('key', 'web_maintenance')->first();
        $apiMaintenance = MaintenanceSetting::where('key', 'api_maintenance')->first();

        return view('admin.maintenance', compact('webMaintenance', 'apiMaintenance'));
    }

    /**
     * Afficher la page dédiée aux demandes gratuites
     */
    public function freeRequestsIndex()
    {
        $freeRequestsMode = MaintenanceSetting::where('key', 'free_requests_mode')->first();

        // Statistiques des demandes gratuites
        $totalFreeNaissance = \App\Models\Naissance::where('is_free_request', true)->sum('free_timbres_count');
        $totalFreeDeces = \App\Models\Deces::where('is_free_request', true)->sum('free_timbres_count');
        $totalFreeMariage = \App\Models\Mariage::where('is_free_request', true)->sum('free_timbres_count');
        $totalFreeTimbres = $totalFreeNaissance + $totalFreeDeces + $totalFreeMariage;
        $totalFreeMontant = $totalFreeTimbres * 500;

        // Nombre d'utilisateurs ayant bénéficié
        $usersWithFreeRequests = \App\Models\User::where('free_requests_used', '>', 0)->count();

        return view('admin.free-requests', compact(
            'freeRequestsMode',
            'totalFreeNaissance',
            'totalFreeDeces',
            'totalFreeMariage',
            'totalFreeTimbres',
            'totalFreeMontant',
            'usersWithFreeRequests'
        ));
    }

    /**
     * Basculer le mode maintenance web
     */
    public function toggleWebMaintenance(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
            'message' => 'nullable|string|max:500',
        ]);

        $adminId = Auth::guard('admin')->id();
        
        MaintenanceSetting::updateSetting(
            'web_maintenance',
            $request->status,
            $request->message,
            $adminId
        );

        $statusText = $request->status ? 'activé' : 'désactivé';
        
        return redirect()->back()->with('success', "Mode maintenance web {$statusText} avec succès.");
    }

    /**
     * Basculer le mode maintenance API
     */
    public function toggleApiMaintenance(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
            'message' => 'nullable|string|max:500',
        ]);

        $adminId = Auth::guard('admin')->id();
        
        MaintenanceSetting::updateSetting(
            'api_maintenance',
            $request->status,
            $request->message,
            $adminId
        );

        $statusText = $request->status ? 'activé' : 'désactivé';
        
        return redirect()->back()->with('success', "Mode maintenance API {$statusText} avec succès.");
    }

    /**
     * Basculer le mode demandes gratuites (phase test)
     */
    public function toggleFreeRequestsMode(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $adminId = Auth::guard('admin')->id();
        
        MaintenanceSetting::updateSetting(
            'free_requests_mode',
            $request->status,
            'Mode test : 2 premières demandes gratuites pour chaque nouvel utilisateur.',
            $adminId
        );

        $statusText = $request->status ? 'activé' : 'désactivé';
        
        return redirect()->back()->with('success', "Mode demandes gratuites {$statusText} avec succès.");
    }
}
