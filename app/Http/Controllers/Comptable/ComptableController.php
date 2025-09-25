<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Comptable;
use App\Models\Finance;
use App\Models\ResetCodePasswordComptable;
use App\Notifications\SendEmailToComptableAfterRegistrationNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PDF;

class ComptableController extends Controller
{
     public function index(){
        $finance = Auth::guard('finance')->user();

        $agents = Comptable::whereNull('archived_at')
                ->where('finance_id', $finance->id)
                ->paginate(10);
        return view('finance.comptable.index',compact('agents'));
    }

    public function create(){
        return view('finance.comptable.create');
    }

    public function store(Request $request){
        // Validation des données
        $request->validate([
           'name' => 'required|string|max:255',
           'prenom' => 'required|string|max:255',
           'email' => 'required|email|unique:comptables,email',
           'contact' => 'required|string|min:10',
           'commune' => 'required|string|max:255',
           'cas_urgence' => 'required|string|max:255',
           'profile_picture' => 'nullable|image|max:2048',

        ],[
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est déjà associée à un compte.',
            'contact.required' => 'Le contact est obligatoire.',
            'contact.min' => 'Le contact doit avoir au moins 10 chiffres.',
            'commune.required' => 'La commune est obligatoire.',
            'cas_urgence.required' => 'La personne à contacter est obligatoire.',
            'profile_picture.image' => 'Le fichier doit être une image.',
            'profile_picture.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou svg.',
            'profile_picture.max' => 'L\'image ne doit pas dépasser 2048 KB.',
       ]);
   
       try {
           // Récupérer le mairie connecté
           $finance = Auth::guard('finance')->user();
   
           // Création du finance
           $comptable = new Comptable();
           $comptable->name = $request->name;
           $comptable->prenom = $request->prenom;
           $comptable->email = $request->email;
           $comptable->contact = $request->contact;
           $comptable->cas_urgence = $request->cas_urgence;
           $comptable->password = Hash::make('default');
           
           if ($request->hasFile('profile_picture')) {
               $comptable->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
           }
   
           $comptable->commune = $request->commune;
           $comptable->communeM = $finance->communeM;
           $comptable->finance_id = $finance->id;
           
           $comptable->save();
   
           // Envoi de l'e-mail de vérification
           ResetCodePasswordComptable::where('email', $comptable->email)->delete();
           $code1 = rand(1000, 4000);
           $code = $code1.''.$comptable->id;
   
           ResetCodePasswordComptable::create([
               'code' => $code,
               'email' => $comptable->email,
           ]);
   
           Notification::route('mail', $comptable->email)
               ->notify(new SendEmailToComptableAfterRegistrationNotification($code, $comptable->email));
   
           return redirect()->route('comptable.index')->with('success', 'Le financier a bien été enregistré avec succès.');
       } catch (\Exception $e) {
           return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
       }
    }

    public function exportPdf($id)
    {
        $agent = Comptable::with(['timbres' => function($query) {
            $query->whereYear('created_at', now()->year);
        }])->findOrFail($id);
        
        // Calculer les totaux par mois
        $monthlyData = [];
        $currentYear = now()->year;
        
        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::create($currentYear, $month, 1)->endOfMonth();
            
            $timbres = $agent->timbres->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            $totalTimbres = $timbres->sum('nombre_timbre');
            
            $monthlyData[] = [
                'month' => $startOfMonth->locale('fr')->monthName,
                'year' => $currentYear,
                'timbres' => $totalTimbres,
                'montant' => $totalTimbres * 500
            ];
        }
        
        $totalAnnualTimbres = $agent->timbres->sum('nombre_timbre');
        $totalAnnualAmount = $totalAnnualTimbres * 500;
        
        $pdf = PDF::loadView('finance.comptable.rapport-pdf', [
            'agent' => $agent,
            'monthlyData' => $monthlyData,
            'totalTimbres' => $totalAnnualTimbres,
            'totalAmount' => $totalAnnualAmount,
            'currentYear' => $currentYear
        ]);
        
        return $pdf->download('rapport_financier_' . $agent->name . '_' . $agent->prenom . '_' . $currentYear . '.pdf');
    }
}
