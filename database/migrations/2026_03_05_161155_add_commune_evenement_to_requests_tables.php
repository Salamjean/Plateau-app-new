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
        Schema::table('naissances', function (Blueprint $table) {
            $table->string('commune_naissance')->nullable()->after('commune');
        });
        Schema::table('deces', function (Blueprint $table) {
            $table->string('commune_deces')->nullable()->after('commune');
        });
        Schema::table('mariages', function (Blueprint $table) {
            $table->string('commune_mariage')->nullable()->after('commune');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->dropColumn('commune_naissance');
        });
        Schema::table('deces', function (Blueprint $table) {
            $table->dropColumn('commune_deces');
        });
        Schema::table('mariages', function (Blueprint $table) {
            $table->dropColumn('commune_mariage');
        });
    }
};
