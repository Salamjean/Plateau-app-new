<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crée la table `naissance_groupes` qui représente une "demande groupée"
 * (panier de plusieurs actes commandés en une seule transaction).
 *
 * Une demande groupée contient N lignes (sous-actes) qui sont stockées
 * dans la table `naissances` existante via la colonne `groupe_id`.
 *
 * Exemple : 2 actes simples + 3 actes intégraux = 1 ligne `naissance_groupes`
 *           + 5 lignes `naissances` toutes avec le même `groupe_id`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('naissance_groupes', function (Blueprint $table) {
            $table->id();

            // Référence "humaine" unique du groupe (ex: GRN1234X2026)
            $table->string('reference')->unique();

            // Utilisateur demandeur
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Commune de demande
            $table->string('commune')->nullable();

            // État global du groupe :
            //   'non_paye', 'en attente de paiement', 'en attente',
            //   'en cours', 'terminé', 'rejetée'
            $table->string('etat')->default('en attente');

            // Mode de retrait : 'Retrait sur place' ou 'livraison'
            $table->string('choix_option');

            // Montants (calculés serveur)
            $table->decimal('montant_timbre_total', 10, 2)->default(0);   // somme timbres payants
            $table->decimal('montant_livraison', 10, 2)->default(0);
            $table->decimal('montant_total', 10, 2)->default(0);          // ce qui sera/a été payé

            // Free requests (timbres offerts sur le groupe)
            $table->boolean('is_free_request')->default(false);
            $table->unsignedTinyInteger('free_timbres_count')->default(0);

            // Quantités totales par type (pour stats rapides sans JOIN)
            $table->unsignedTinyInteger('qty_simple')->default(0);
            $table->unsignedTinyInteger('qty_integral')->default(0);

            // Informations de livraison (partagées par toutes les lignes du groupe)
            $table->string('nom_destinataire')->nullable();
            $table->string('prenom_destinataire')->nullable();
            $table->string('email_destinataire')->nullable();
            $table->string('contact_destinataire')->nullable();
            $table->string('adresse_livraison')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->string('commune_livraison')->nullable();
            $table->string('quartier')->nullable();
            $table->string('date_livraison')->nullable();
            $table->string('heure_livraison')->nullable();

            // Code & QR de livraison (générés à l'état terminé)
            $table->string('livraison_code')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('statut_livraison')->nullable();

            // Liens vers acteurs métier
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->foreignId('livraison_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('dhl_id')->nullable()->constrained('dhls')->onDelete('set null');
            $table->foreignId('livreur_id')->nullable()->constrained('livreurs')->onDelete('set null');

            // Suivi comptable des timbres (régie)
            $table->boolean('timbre_recupere')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'etat']);
            $table->index('commune');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('naissance_groupes');
    }
};
