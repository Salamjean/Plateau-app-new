<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\agentRequest;
use App\Models\Agent;
use App\Models\ResetCodePasswordAgent;
use App\Notifications\SendEmailToAgentAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AgentController extends Controller
{
    public function index(){
        $mairie = Auth::guard('etatCivil')->user();

        $agents = Agent::whereNull('archived_at')
                ->where('etat_civil_id', $mairie->id)
                ->paginate(10);
        return view('etatCivil.agent.index',compact('agents'));
    }

    public function create(){
        return view('etatCivil.agent.create');
    }

    public function store(agentRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $etatCivil = Auth::guard('etatCivil')->user();

            $existingAgent = Agent::where('email', $request->email)->first();
            if ($existingAgent) {
                return redirect()->back()->withErrors(['email' => 'Cet email est déjà utilisé.'])->withInput();
            }

            $agent = new Agent();
            $agent->name = $request->name;
            $agent->prenom = $request->prenom;
            $agent->email = $request->email;
            $agent->contact = $request->contact;
            $agent->cas_urgence = $request->cas_urgence;
            $agent->password = Hash::make('default');
            
            if ($request->hasFile('profile_picture')) {
                $request->validate([
                    'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
                
                $agent->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $agent->commune = $request->commune;
            $agent->etat_civil_id = $etatCivil->id;
            $agent->communeM = $etatCivil->communeM;
            
            $agent->save();

            // Envoi de l'e-mail de vérification
            ResetCodePasswordAgent::where('email', $agent->email)->delete();
            $code1 = rand(1000, 4000);
            $code = $code1 . '' . $agent->id;

            ResetCodePasswordAgent::create([
                'code' => $code,
                'email' => $agent->email,
            ]);

            Notification::route('mail', $agent->email)
                ->notify(new SendEmailToAgentAfterRegistrationNotification($code, $agent->email));
            DB::commit();

            return redirect()->route('etat_civil.agent.state.index')->with('success', 'L\'agent a bien été enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enregistrement de l\'agent: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.'])->withInput();
        }
    }
}
