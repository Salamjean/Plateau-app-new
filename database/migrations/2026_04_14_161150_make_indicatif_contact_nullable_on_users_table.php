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
        Schema::table('users', function (Blueprint $table) {
            $table->string('indicatif')->nullable()->change();
            $table->string('contact')->nullable()->change();
            $table->string('commune')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('indicatif')->nullable(false)->change();
            $table->string('contact')->nullable(false)->change();
            $table->string('commune')->nullable(false)->change();
        });
    }
};
