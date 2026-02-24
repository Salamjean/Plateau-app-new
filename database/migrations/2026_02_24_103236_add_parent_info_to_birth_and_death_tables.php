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
            $table->string('nom_pere')->nullable()->after('prenom');
            $table->string('prenom_pere')->nullable()->after('nom_pere');
            $table->string('nom_mere')->nullable()->after('prenom_pere');
            $table->string('prenom_mere')->nullable()->after('nom_mere');
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->string('nom_pere')->nullable()->after('dateR');
            $table->string('prenom_pere')->nullable()->after('nom_pere');
            $table->string('nom_mere')->nullable()->after('prenom_pere');
            $table->string('prenom_mere')->nullable()->after('nom_mere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->dropColumn(['nom_pere', 'prenom_pere', 'nom_mere', 'prenom_mere']);
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->dropColumn(['nom_pere', 'prenom_pere', 'nom_mere', 'prenom_mere']);
        });
    }
};
