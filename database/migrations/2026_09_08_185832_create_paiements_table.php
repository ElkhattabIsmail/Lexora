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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->string('mode_paiement'); // Espèces / Virement / Carte / etc.
            $table->string('reference')->nullable();
            $table->foreignId('facture_id')->constrained('factures')->cascadeOnDelete();
            $table->timestamps();

            $table->index('facture_id');
            $table->index('date_paiement');
            $table->index('mode_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
