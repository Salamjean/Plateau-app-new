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
            $table->json('champs_a_modifier')->nullable()->after('motif_de_rejet');
            $table->boolean('peut_modifier')->default(false)->after('champs_a_modifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deces', function (Blueprint $table) {
            $table->dropColumn(['champs_a_modifier', 'peut_modifier']);
        });
    }
};
