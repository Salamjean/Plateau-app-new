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
            $table->renameColumn('nom_pere', 'nom_prenoms_pere');
            $table->dropColumn('prenom_pere');
            $table->renameColumn('nom_mere', 'nom_prenoms_mere');
            $table->dropColumn('prenom_mere');
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->renameColumn('nom_pere', 'nom_prenoms_pere');
            $table->dropColumn('prenom_pere');
            $table->renameColumn('nom_mere', 'nom_prenoms_mere');
            $table->dropColumn('prenom_mere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->renameColumn('nom_prenoms_pere', 'nom_pere');
            $table->string('prenom_pere')->nullable()->after('nom_pere');
            $table->renameColumn('nom_prenoms_mere', 'nom_mere');
            $table->string('prenom_mere')->nullable()->after('nom_mere');
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->renameColumn('nom_prenoms_pere', 'nom_pere');
            $table->string('prenom_pere')->nullable()->after('nom_pere');
            $table->renameColumn('nom_prenoms_mere', 'nom_mere');
            $table->string('prenom_mere')->nullable()->after('nom_mere');
        });
    }
};
