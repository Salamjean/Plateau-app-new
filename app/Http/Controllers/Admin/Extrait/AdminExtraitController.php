<?php

namespace App\Http\Controllers\Admin\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;

class AdminExtraitController extends Controller
{
    public function birth(Request $request){
        $request->validate([
            'demandeur' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'month'     => 'nullable|string|date_format:Y-m',
        ]);

        $searchDemandeur = $request->input('demandeur');
        $searchReference = $request->input('reference');
        $selectedMonth = $request->input('month');

        $query = Naissance::paye()->with('user');

        if (!empty($searchDemandeur)) {
            $query->whereHas('user', function ($q) use ($searchDemandeur) {
                $q->where(function ($sub) use ($searchDemandeur) {
                    $sub->where('name', 'like', "%{$searchDemandeur}%")
                        ->orWhere('prenom', 'like', "%{$searchDemandeur}%");
                });
            });
        }

        if (!empty($searchReference)) {
            $query->where('reference', 'like', "%{$searchReference}%");
        }

        if (!empty($selectedMonth)) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$selectedMonth]);
        }

        $naissances = $query->get();

        $availableMonths = Naissance::paye()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('admin.extraits.naissance', compact('naissances', 'searchDemandeur', 'searchReference', 'selectedMonth', 'availableMonths'));
    }
    public function death(Request $request){
        $request->validate([
            'demandeur' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'month'     => 'nullable|string|date_format:Y-m',
        ]);

        $searchDemandeur = $request->input('demandeur');
        $searchReference = $request->input('reference');
        $selectedMonth = $request->input('month');

        $query = Deces::paye()->with('user');

        if (!empty($searchDemandeur)) {
            $query->whereHas('user', function ($q) use ($searchDemandeur) {
                $q->where(function ($sub) use ($searchDemandeur) {
                    $sub->where('name', 'like', "%{$searchDemandeur}%")
                        ->orWhere('prenom', 'like', "%{$searchDemandeur}%");
                });
            });
        }

        if (!empty($searchReference)) {
            $query->where('reference', 'like', "%{$searchReference}%");
        }

        if (!empty($selectedMonth)) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$selectedMonth]);
        }

        $deces = $query->get();

        $availableMonths = Deces::paye()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('admin.extraits.deces', compact('deces', 'searchDemandeur', 'searchReference', 'selectedMonth', 'availableMonths'));
    }
    public function mariage(Request $request){
        $request->validate([
            'demandeur' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'month'     => 'nullable|string|date_format:Y-m',
        ]);

        $searchDemandeur = $request->input('demandeur');
        $searchReference = $request->input('reference');
        $selectedMonth = $request->input('month');

        $query = Mariage::paye()->with('user');

        if (!empty($searchDemandeur)) {
            $query->whereHas('user', function ($q) use ($searchDemandeur) {
                $q->where(function ($sub) use ($searchDemandeur) {
                    $sub->where('name', 'like', "%{$searchDemandeur}%")
                        ->orWhere('prenom', 'like', "%{$searchDemandeur}%");
                });
            });
        }

        if (!empty($searchReference)) {
            $query->where('reference', 'like', "%{$searchReference}%");
        }

        if (!empty($selectedMonth)) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$selectedMonth]);
        }

        $mariages = $query->get();

        $availableMonths = Mariage::paye()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('admin.extraits.mariage', compact('mariages', 'searchDemandeur', 'searchReference', 'selectedMonth', 'availableMonths'));
    }
}
