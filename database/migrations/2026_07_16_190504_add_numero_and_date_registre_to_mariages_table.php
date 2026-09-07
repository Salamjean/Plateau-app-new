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
        Schema::table('mariages', function (Blueprint $table) {
            $table->string('numero_registre')->nullable();
            $table->date('date_registre')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mariages', function (Blueprint $table) {
            $table->dropColumn(['numero_registre', 'date_registre']);
        });
    }
};
