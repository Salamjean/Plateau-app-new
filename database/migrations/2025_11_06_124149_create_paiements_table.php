<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deces_id')->nullable()->index(); // lien optionnel avec la demande
            $table->unsignedBigInteger('user_id')->nullable()->index(); // qui a payé (si dispo)
            
            $table->string('transaction_id')->nullable()->index(); // référence AD00...
            $table->string('operator_id')->nullable(); // ID opérateur / pay id retourné par CinetPay
            $table->string('payment_token')->nullable();
            $table->string('payer_name')->nullable(); // ex: "Paul Kouamé"
            $table->decimal('montant', 15, 2)->nullable(); // le montant payé
            $table->string('currency', 10)->default('XOF');
            $table->string('status')->nullable(); // ACCEPTED / REFUSED / PENDING
            $table->timestamp('paid_at')->nullable(); // date paiement fournie par CinetPay
            $table->json('raw_response')->nullable(); // stocker la réponse brute pour debug
            $table->timestamps();

            // clefs étrangères (optionnelles — à ajuster selon tes tables)
            $table->foreign('deces_id')->references('id')->on('deces')->onDelete('set null');
            // user_id foreign si tu as users table :
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}
