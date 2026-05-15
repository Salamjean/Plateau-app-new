<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute le lien vers `naissance_groupes` à la table `naissances` existante.
 *
 * - `groupe_id` NULL  → demande individuelle (rétrocompatibilité)
 * - `groupe_id` != NULL → ligne d'une demande groupée
 *
 * Permet aussi à l'agent de rejeter sélectivement une ligne du groupe :
 * `motif_de_rejet` peut être rempli même si l'état du groupe global suit
 * la logique "tout ou rien".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->foreignId('groupe_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('naissance_groupes')
                  ->onDelete('cascade');

            // Position de la ligne dans le groupe (1, 2, 3...) — utile pour l'affichage
            $table->unsignedTinyInteger('position_in_groupe')
                  ->nullable()
                  ->after('groupe_id');

            // Type de document de la ligne : 'simple' ou 'integral'
            // (redondance volontaire avec `type` qui pouvait avoir d'autres valeurs)
            $table->string('type_document')->nullable()->after('position_in_groupe');

            $table->index('groupe_id');
        });
    }

    public function down(): void
    {
        Schema::table('naissances', function (Blueprint $table) {
            $table->dropForeign(['groupe_id']);
            $table->dropColumn(['groupe_id', 'position_in_groupe', 'type_document']);
        });
    }
};
