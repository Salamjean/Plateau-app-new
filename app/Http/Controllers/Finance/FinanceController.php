<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceRequest;
use App\Models\Finance;
use App\Models\ResetCodePasswordFinance;
use App\Notifications\SendEmailToFinanceAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class FinanceController extends Controller
{
    public function index(){
        $mairie = Auth::guard('mairie')->user();
        $finances = Finance::whereNull('archived_at')
                ->where('mairie_id', $mairie->id)
                ->paginate(10);

        return view('mairie.finance.index',compact('finances'));
    }

    public function create(){
        return view('mairie.finance.create');
    }

    public function store(FinanceRequest $request){
           $mairie = Auth::guard('mairie')->user();

       try {
           $finance = new Finance();
           $finance->name_respo = $request->name_respo;
           $finance->email = $request->email;
           $finance->contact = $request->contact;
           $finance->password = Hash::make('default');
           
           if ($request->hasFile('profile_picture')) {
               $finance->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
           }
   
           $finance->commune = $request->commune;
           $finance->mairie_id = $mairie->id;
           $finance->communeM = $mairie->name;
           
           $finance->save();
   
           // Envoi de l'e-mail de vérification
           ResetCodePasswordFinance::where('email', $finance->email)->delete();
           $code1 = rand(1000, 4000);
           $code = $code1.''.$finance->id;
   
           ResetCodePasswordFinance::create([
               'code' => $code,
               'email' => $finance->email,
           ]);
   
           Notification::route('mail', $finance->email)
               ->notify(new SendEmailToFinanceAfterRegistrationNotification($code, $finance->email));
   
           return redirect()->route('mairie.finance.index')->with('success', 'Le service financier a bien été enregistré avec succès.');
       } catch (\Exception $e) {
           return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
       }
    }
    
     public function destroy($id)
    {
        try {
            // Recherche de l'enregistrement
            $etatCivil = Finance::findOrFail($id);
            
            // Suppression
            $etatCivil->delete();
            
            // Redirection avec message de succès
            return redirect()->route('mairie.finance.index')
                ->with('success', 'Le responsable a été supprimé avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur
            return redirect()->route('mairie.finance.index')
                ->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

     public function edit($id)
    {
        try {
            $etatCivil = Finance::findOrFail($id);
            return view('mairie.finance.edit', compact('etatCivil'));
        } catch (\Exception $e) {
            return redirect()->route('mairie.finance.index')
                ->with('error', 'Responsable non trouvé.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'name_respo' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'contact' => 'required|string|max:20',
                'commune' => 'required|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Recherche de l'enregistrement
            $etatCivil = Finance::findOrFail($id);
            
            // Traitement de l'image si elle est fournie
            if ($request->hasFile('profile_picture')) {
                // Supprimer l'ancienne image si elle existe
                if ($etatCivil->profile_picture) {
                    Storage::delete('public/profile_pictures/' . $etatCivil->profile_picture);
                }
                
                // Enregistrer la nouvelle image
                $imageName = time() . '.' . $request->profile_picture->extension();
                $request->profile_picture->storeAs('public/profile_pictures', $imageName);
                $validated['profile_picture'] = $imageName;
            }
            
            // Mise à jour
            $etatCivil->update($validated);
            
            // Redirection avec message de succès
            return redirect()->route('mairie.finance.index')
                ->with('success', 'Le service a été modifié avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur
            return redirect()->route('finance.edit', $id)
                ->with('error', 'Une erreur est survenue lors de la modification.')
                ->withInput();
        }
    }
}
