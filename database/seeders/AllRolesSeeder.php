<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Comptable;
use App\Models\Dhl;
use App\Models\EtatCivil;
use App\Models\Finance;
use App\Models\Livreur;
use App\Models\Mairie;
use App\Models\Poste;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AllRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'redfieldluise@gmail.com';
        $password = Hash::make('12345678');
        $contact = '0102030405';
        $commune = 'Plateau';

        // 1. Mairie (Base for Finance and EtatCivil)
        $mairie = Mairie::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'plateau',
                'password' => $password,
            ]
        );

        // 2. Finance
        $finance = Finance::updateOrCreate(
            ['email' => $email],
            [
                'name_respo' => 'Luise Redfield',
                'contact' => $contact,
                'password' => $password,
                'commune' => $commune,
                'communeM' => $commune,
                'mairie_id' => $mairie->id,
            ]
        );

        // 3. EtatCivil
        $etatCivil = EtatCivil::updateOrCreate(
            ['email' => $email],
            [
                'name_respo' => 'Luise Redfield',
                'contact' => $contact,
                'password' => $password,
                'commune' => $commune,
                'communeM' => $commune,
                'mairie_id' => $mairie->id,
            ]
        );

        // 4. Agent
        Agent::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Redfield',
                'prenom' => 'Luise',
                'contact' => $contact,
                'password' => $password,
                'commune' => $commune,
                'communeM' => $commune,
                'etat_civil_id' => $etatCivil->id,
            ]
        );

        // 5. Comptable
        Comptable::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Redfield',
                'prenom' => 'Luise',
                'contact' => $contact,
                'password' => $password,
                'commune' => $commune,
                'communeM' => $commune,
                'finance_id' => $finance->id,
            ]
        );

        // 6. Poste
        $poste = Poste::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Redfield',
                'prenom' => 'Luise',
                'contact' => $contact,
                'password' => $password,
                'commune' => $commune,
                'communeM' => $commune,
            ]
        );

        // 7. Livreur
        Livreur::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Redfield',
                'prenom' => 'Luise',
                'contact' => $contact,
                'password' => $password,
                'commune' => $commune,
                'communeM' => $commune,
                'poste_id' => $poste->id,
                'disponible' => true,
            ]
        );

        // 8. Dhl
        Dhl::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Redfield',
                'prenom' => 'Luise',
                'contact' => $contact,
                'password' => $password,
                'commune' => $commune,
                'communeM' => $commune,
            ]
        );
    }
}
