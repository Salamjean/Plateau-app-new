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
        Schema::create('comptables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('contact')->unique();
            $table->string('password');
            $table->string('profile_picture')->nullable();
            $table->string('commune');
            $table->string('communeM');
            $table->string('cas_urgence')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('finance_id')->nullable()->constrained('finances')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptables');
    }
};
