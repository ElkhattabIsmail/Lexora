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
        Schema::create('audiences', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('heure');
            $table->string('tribunal');
            $table->text('observations')->nullable();
            $table->enum('statut', ['Prévue', 'Annulée', 'Terminée'])->default('Prévue');
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->foreignId('avocat_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('date');
            $table->index('statut');
            $table->index('dossier_id');
            $table->index('avocat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiences');
    }
};
