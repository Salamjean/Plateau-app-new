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
        Schema::table('deces', function (Blueprint $table) {
            $table->string('commune_deces')->nullable()->change();
        });
        Schema::table('mariages', function (Blueprint $table) {
            $table->string('commune_mariage')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deces', function (Blueprint $table) {
            $table->string('commune_deces')->nullable(false)->change();
        });
        Schema::table('mariages', function (Blueprint $table) {
            $table->string('commune_mariage')->nullable(false)->change();
        });
    }
};
