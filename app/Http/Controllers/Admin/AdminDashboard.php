<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mairie;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Controller
{
    public function dashboard(){
      $deces = Deces::count();
      $mariage = Mariage::count();
      $naissance = Naissance::count();

      $mairie = Mairie::whereNull('archived_at')->count();
      $total = $deces + $mariage + $naissance;


         // Récupérer le solde total de toutes les mairies
      $soldeTotalMairies = Mairie::whereNull('archived_at')->sum('solde');
      // Solde initial
      $soldeActuel =  $soldeTotalMairies;

      // Déduction pour chaque nouvelle demande
      $debit = 500; // Montant à déduire pour chaque demande
      $soldeDebite = $total * $debit; // Total débité basé sur le nombre de demandes
      $soldeRestant = $soldeActuel - $soldeDebite; // Calcul du solde restant

  

    return view('admin.dashboard', compact(
        'deces', 'mariage', 'naissance',
        'total', 'soldeActuel', 'soldeDebite', 'soldeRestant',
        'mairie', 'soldeTotalMairies',
    ));
   }

   public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
