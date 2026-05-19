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
        if (!Schema::hasColumn('mariages', 'type_document')) {
            Schema::table('mariages', function (Blueprint $table) {
                $table->string('type_document')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mariages', function (Blueprint $table) {
            if (Schema::hasColumn('mariages', 'type_document')) {
                $table->dropColumn('type_document');
            }
        });
    }
};
