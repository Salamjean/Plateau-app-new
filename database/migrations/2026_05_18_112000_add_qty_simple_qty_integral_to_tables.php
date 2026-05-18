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
        foreach (['naissances', 'mariages', 'deces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'qty_simple')) {
                    $table->integer('qty_simple')->default(0)->after('reference');
                }
                if (!Schema::hasColumn($tableName, 'qty_integral')) {
                    $table->integer('qty_integral')->default(0)->after('qty_simple');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['naissances', 'mariages', 'deces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'qty_simple')) {
                    $table->dropColumn('qty_simple');
                }
                if (Schema::hasColumn($tableName, 'qty_integral')) {
                    $table->dropColumn('qty_integral');
                }
            });
        }
    }
};
