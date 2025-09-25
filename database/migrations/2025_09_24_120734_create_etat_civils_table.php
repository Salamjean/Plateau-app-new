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
        Schema::create('etat_civils', function (Blueprint $table) {
            $table->id();
            $table->string('name_respo');
            $table->string('email')->unique();
            $table->string('contact')->unique();
            $table->string('password');
            $table->string('commune');
            $table->string('communeM');
            $table->foreignId('mairie_id')->nullable()->constrained('mairies')->onDelete('set null');
            $table->dateTime('email_verified_at')->nullable();
            $table->string('profile_picture')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etat_civils');
    }
};
