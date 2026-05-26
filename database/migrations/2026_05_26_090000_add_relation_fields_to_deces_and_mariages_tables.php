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
            $table->string('pour')->nullable()->after('name');
            $table->string('relation')->nullable()->after('pour');
            $table->string('document_autorisation')->nullable()->after('CNIdcl');
        });

        Schema::table('mariages', function (Blueprint $table) {
            $table->string('pour')->nullable()->after('nomEpoux');
            $table->string('relation')->nullable()->after('pour');
            $table->string('document_autorisation')->nullable()->after('pieceIdentite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deces', function (Blueprint $table) {
            $table->dropColumn(['pour', 'relation', 'document_autorisation']);
        });

        Schema::table('mariages', function (Blueprint $table) {
            $table->dropColumn(['pour', 'relation', 'document_autorisation']);
        });
    }
};
