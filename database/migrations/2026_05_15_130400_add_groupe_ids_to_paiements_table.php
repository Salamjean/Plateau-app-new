<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permet aux paiements de référencer des demandes groupées mariage et décès.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('mariage_groupe_id')
                  ->nullable()
                  ->after('mariage_id')
                  ->constrained('mariage_groupes')
                  ->onDelete('set null');
            $table->foreignId('deces_groupe_id')
                  ->nullable()
                  ->after('deces_id')
                  ->constrained('deces_groupes')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['mariage_groupe_id']);
            $table->dropForeign(['deces_groupe_id']);
            $table->dropColumn(['mariage_groupe_id', 'deces_groupe_id']);
        });
    }
};
