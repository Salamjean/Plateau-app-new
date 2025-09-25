<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('naissances', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Type de demande
            $table->string('pour'); // pour qui la demande
            $table->string('name'); // Nom de la demande
            $table->string('prenom'); // Prenom de la demande
            $table->string('number'); // Numéro associé
            $table->string('DateR'); // Numéro associé
            $table->string('CNI'); // Numéro associé
            $table->string('reference');
            $table->string('commune')->nullable(); // Commune, nullable
            $table->string('etat')->default('en attente'); // État par défaut
            $table->string('statut_livraison')->nullable(); // État par défaut
            $table->boolean('is_read')->default(false); // Statut de lecture
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ajout de user_id
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null'); // Ajout de agent_id
            $table->foreignId('livraison_id')->nullable()->constrained('postes')->onDelete('set null'); // Ajout de livraison
            $table->foreignId('dhl_id')->nullable()->constrained('dhls')->onDelete('set null');
            $table->foreignId('livreur_id')->nullable()->constrained('livreurs')->onDelete('set null'); // Ajout de livreur
            // $table->foreignId('agence_id')->nullable()->constrained('d_h_l_agences')->onDelete('set null'); // Ajout de livreur

            //informations de livraison 
             $table->string('montant_timbre')->nullable();
             $table->string('montant_livraison')->nullable();
             $table->string('nom_destinataire')->nullable();
             $table->string('prenom_destinataire')->nullable();
             $table->string('email_destinataire')->nullable();
             $table->string('contact_destinataire')->nullable();
             $table->string('adresse_livraison')->nullable();
             $table->string('choix_option');
             $table->string('code_postal')->nullable();
             $table->string('ville')->nullable();
             $table->string('commune_livraison')->nullable();
             $table->string('quartier')->nullable();
             $table->string('livraison_code')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naissances');
    }
};
