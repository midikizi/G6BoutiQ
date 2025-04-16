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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->dateTime('date_vente');
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->enum('statut', ['en_cours', 'payee', 'annulee'])->default('en_cours');
            $table->enum('mode_paiement', ['especes', 'carte', 'virement'])->nullable();
            $table->enum('statut_paiement', ['en_attente', 'valide', 'refuse'])->nullable();
            $table->dateTime('date_paiement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
