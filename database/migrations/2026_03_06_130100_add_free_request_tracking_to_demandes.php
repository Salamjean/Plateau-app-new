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
        // Ajouter les colonnes de suivi des demandes gratuites dans les tables de demandes
        $tables = ['naissances', 'deces', 'mariages'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->boolean('is_free_request')->default(false)->after('etat');
                $blueprint->integer('free_timbres_count')->default(0)->after('is_free_request');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['naissances', 'deces', 'mariages'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn(['is_free_request', 'free_timbres_count']);
            });
        }
    }
};
