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
        Schema::create('action_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->string('demande_type'); // naissance, mariage, deces
            $table->unsignedBigInteger('demande_id');
            $table->string('reference')->nullable();
            $table->string('action'); // changement_etat, rejet, recuperation, livraison
            $table->string('ancien_etat')->nullable();
            $table->string('nouvel_etat')->nullable();
            $table->text('motif')->nullable();
            $table->json('champs_modifies')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
            
            // Index pour les recherches
            $table->index(['demande_type', 'demande_id']);
            $table->index('agent_id');
            $table->index('reference');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_histories');
    }
};
