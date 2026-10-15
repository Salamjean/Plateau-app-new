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
        Schema::create('reversements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->decimal('montant', 15, 2);
            $table->boolean('is_premier')->default(false);
            $table->string('type_reversement')->default('Reversement standard');
            $table->dateTime('date_reversement');
            $table->json('mois_concernes');
            $table->string('annee')->nullable();
            $table->text('commentaire')->nullable();

            // Rattachement régie
            $table->foreignId('finance_id')->nullable()->constrained('finances')->nullOnDelete();
            $table->foreignId('comptable_id')->nullable()->constrained('comptables')->nullOnDelete();

            // Traçabilité de l'auteur (Financier ou Comptable)
            $table->string('auteur_role'); // 'Financier' ou 'Comptable'
            $table->unsignedBigInteger('auteur_id')->nullable();
            $table->string('enregistre_par'); // Nom complet
            $table->string('enregistre_par_email');
            $table->string('commune')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reversements');
    }
};
