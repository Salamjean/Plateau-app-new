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
        Schema::create('deleted_demandes', function (Blueprint $table) {
            $table->id();
            $table->string('type_demande'); // e.g., 'App\Models\Naissance'
            $table->unsignedBigInteger('original_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('data'); // Stores the full JSON of the deleted model
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_demandes');
    }
};
