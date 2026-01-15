<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->boolean('value')->default(false);
            $table->text('message')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // Insérer les paramètres par défaut
        DB::table('maintenance_settings')->insert([
            [
                'key' => 'web_maintenance',
                'value' => false,
                'message' => 'Le site est actuellement en maintenance. Veuillez réessayer plus tard.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'api_maintenance',
                'value' => false,
                'message' => 'L\'application est actuellement en maintenance. Veuillez réessayer plus tard.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_settings');
    }
};
