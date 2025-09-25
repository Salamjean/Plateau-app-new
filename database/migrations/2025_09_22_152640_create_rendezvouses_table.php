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
        Schema::create('rendezvouses', function (Blueprint $table) {
            $table->id();
            $table->string('nom_epoux');
            $table->string('prenom_epoux');
            $table->date('date_naissance_epoux');
            $table->string('lieu_naissance_epoux');
            $table->string('nom_epouse');
            $table->string('prenom_epouse');
            $table->date('date_naissance_epouse');
            $table->string('lieu_naissance_epouse');
            $table->string('adresse');
            $table->string('telephone');
            $table->string('email');
            $table->date('date_mariage_souhaitee');
            $table->time('heure_souhaitee');
            $table->string('mairie');
            $table->string('statut')->default('en attente');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendezvouses');
    }
};
