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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->decimal('montant', 10, 2);
            $table->date('date_facture');
            $table->enum('statut', ['Payée', 'Non payée'])->default('Non payée');
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('dossier_id')->nullable()->constrained('dossiers')->nullOnDelete();
            $table->timestamps();

            $table->index('statut');
            $table->index('client_id');
            $table->index('dossier_id');
            $table->index('numero_facture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
