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
}
