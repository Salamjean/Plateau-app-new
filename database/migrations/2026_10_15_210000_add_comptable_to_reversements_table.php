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
        Schema::table('reversements', function (Blueprint $table) {
            if (!Schema::hasColumn('reversements', 'auteur_role')) {
                $table->string('auteur_role')->default('Financier')->after('finance_id');
            }
            if (!Schema::hasColumn('reversements', 'comptable_id')) {
                $table->foreignId('comptable_id')->nullable()->after('finance_id')->constrained('comptables')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reversements', function (Blueprint $table) {
            if (Schema::hasColumn('reversements', 'comptable_id')) {
                $table->dropForeign(['comptable_id']);
                $table->dropColumn('comptable_id');
            }
            if (Schema::hasColumn('reversements', 'auteur_role')) {
                $table->dropColumn('auteur_role');
            }
        });
    }
};
