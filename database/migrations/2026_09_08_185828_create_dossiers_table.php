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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('numero_dossier')->unique();
            $table->string('type_affaire');
            $table->enum('statut', ['En cours', 'Gagné', 'Perdu', 'Fermé'])->default('En cours');
            $table->date('date_ouverture');
            $table->date('date_fermeture')->nullable();
            $table->boolean('archive')->default(false);
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('avocat_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('statut');
            $table->index('archive');
            $table->index('client_id');
            $table->index('avocat_id');
            $table->index('numero_dossier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
