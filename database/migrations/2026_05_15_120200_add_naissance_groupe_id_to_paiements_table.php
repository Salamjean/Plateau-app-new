<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permet aux paiements de référencer une demande groupée naissance
 * (en plus des naissances individuelles).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('naissance_groupe_id')
                  ->nullable()
                  ->after('naissance_id')
                  ->constrained('naissance_groupes')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['naissance_groupe_id']);
            $table->dropColumn('naissance_groupe_id');
        });
    }
};
