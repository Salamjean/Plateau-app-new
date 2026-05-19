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
            if (!Schema::hasColumn('mariages', 'nomEpouse')) {
                $table->string('nomEpouse')->nullable()->after('lieuNaissanceEpoux');
            }
            if (!Schema::hasColumn('mariages', 'prenomEpouse')) {
                $table->string('prenomEpouse')->nullable()->after('nomEpouse');
            }
            if (!Schema::hasColumn('mariages', 'dateNaissanceEpouse')) {
                $table->date('dateNaissanceEpouse')->nullable()->after('prenomEpouse');
            }
            if (!Schema::hasColumn('mariages', 'lieuNaissanceEpouse')) {
                $table->string('lieuNaissanceEpouse')->nullable()->after('dateNaissanceEpouse');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mariages', function (Blueprint $table) {
            $table->dropColumn(['nomEpouse', 'prenomEpouse', 'dateNaissanceEpouse', 'lieuNaissanceEpouse']);
        });
    }
};
