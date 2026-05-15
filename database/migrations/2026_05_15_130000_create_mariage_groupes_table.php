<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crée la table `mariage_groupes` pour les demandes groupées d'actes de mariage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mariage_groupes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('commune')->nullable();
            $table->string('etat')->default('en attente');
            $table->string('choix_option');

            $table->decimal('montant_timbre_total', 10, 2)->default(0);
            $table->decimal('montant_livraison', 10, 2)->default(0);
            $table->decimal('montant_total', 10, 2)->default(0);

            $table->boolean('is_free_request')->default(false);
            $table->unsignedTinyInteger('free_timbres_count')->default(0);

            $table->unsignedTinyInteger('qty_simple')->default(0);
            $table->unsignedTinyInteger('qty_integral')->default(0);

            // Livraison (partagée par toutes les lignes)
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

            $table->string('livraison_code')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('statut_livraison')->nullable();

            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->foreignId('livraison_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('dhl_id')->nullable()->constrained('dhls')->onDelete('set null');
            $table->foreignId('livreur_id')->nullable()->constrained('livreurs')->onDelete('set null');

            $table->boolean('timbre_recupere')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'etat']);
            $table->index('commune');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mariage_groupes');
    }
};
