<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deces', function (Blueprint $table) {
            $table->foreignId('groupe_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('deces_groupes')
                  ->onDelete('cascade');
            $table->unsignedTinyInteger('position_in_groupe')->nullable()->after('groupe_id');
            $table->string('type_document')->nullable()->after('position_in_groupe');
            $table->index('groupe_id');
        });
    }

    public function down(): void
    {
        Schema::table('deces', function (Blueprint $table) {
            $table->dropForeign(['groupe_id']);
            $table->dropColumn(['groupe_id', 'position_in_groupe', 'type_document']);
        });
    }
};
