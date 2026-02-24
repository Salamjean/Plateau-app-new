<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->date('date_livraison')->nullable()->after('choix_option');
            $table->time('heure_livraison')->nullable()->after('date_livraison');
        });

        Schema::table('mariages', function (Blueprint $table) {
            $table->date('date_livraison')->nullable()->after('choix_option');
            $table->time('heure_livraison')->nullable()->after('date_livraison');
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->date('date_livraison')->nullable()->after('choix_option');
            $table->time('heure_livraison')->nullable()->after('date_livraison');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->dropColumn(['date_livraison', 'heure_livraison']);
        });

        Schema::table('mariages', function (Blueprint $table) {
            $table->dropColumn(['date_livraison', 'heure_livraison']);
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->dropColumn(['date_livraison', 'heure_livraison']);
        });
    }
};
