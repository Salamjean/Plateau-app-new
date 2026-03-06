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
        // Ajouter les colonnes pour le suivi des demandes gratuites dans la table users
        Schema::table('users', function (Blueprint $table) {
            $table->integer('free_requests_used')->default(0)->after('deactivated_at');
            $table->boolean('has_seen_free_requests_message')->default(false)->after('free_requests_used');
        });

        // Ajouter le paramètre de mode test dans maintenance_settings
        DB::table('maintenance_settings')->insert([
            'key' => 'free_requests_mode',
            'value' => false,
            'message' => 'Mode test : 2 premières demandes gratuites pour chaque nouvel utilisateur.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['free_requests_used', 'has_seen_free_requests_message']);
        });

        DB::table('maintenance_settings')->where('key', 'free_requests_mode')->delete();
    }
};
