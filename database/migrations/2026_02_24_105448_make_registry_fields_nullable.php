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
            $table->string('number')->nullable()->change();
            $table->string('DateR')->nullable()->change();
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->string('numberR')->nullable()->change();
            $table->string('dateR')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->string('number')->nullable(false)->change();
            $table->string('DateR')->nullable(false)->change();
        });

        Schema::table('deces', function (Blueprint $table) {
            $table->string('numberR')->nullable(false)->change();
            $table->string('dateR')->nullable(false)->change();
        });
    }
};
