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
            $table->boolean('timbre_recupere')->default(false);
        });
        Schema::table('deces', function (Blueprint $table) {
            $table->boolean('timbre_recupere')->default(false);
        });
        Schema::table('mariages', function (Blueprint $table) {
            $table->boolean('timbre_recupere')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->dropColumn('timbre_recupere');
        });
        Schema::table('deces', function (Blueprint $table) {
            $table->dropColumn('timbre_recupere');
        });
        Schema::table('mariages', function (Blueprint $table) {
            $table->dropColumn('timbre_recupere');
        });
    }
};
