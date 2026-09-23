<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audience extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'date',         // Date de l'audience (format YYYY-MM-DD)
        'heure',        // Heure de l'audience (format HH:MM)
        'tribunal',     // Juridiction / Tribunal convoqué
        'observations', // Remarques ou consignes pour l'audience
        'statut',       // "Prévue" | "Terminée" | "Annulée"
        'dossier_id',   // Clé étrangère vers dossiers.id
        'avocat_id',    // Clé étrangère vers users.id (avocat plaidant)
    ];

    // -------------------------------------------------------------------------
    // Castings de types
    // -------------------------------------------------------------------------
    protected $casts = [
        'date' => 'date',
    ];

    public function dossier(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : le dossier juridique rattaché à cette audience
        // ---------------------------------------------------------------------
        return $this->belongsTo(Dossier::class);
    }

    public function avocat(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo personnalisée :
        // Cible l'avocat désigné pour représenter le client lors de cette audience
        // via la clé étrangère explicite 'avocat_id'.
        // ---------------------------------------------------------------------
        return $this->belongsTo(User::class, 'avocat_id');
    }

    public function isAnnulee(): bool
    {
        // ---------------------------------------------------------------------
        // Helper d'état : vérifie si l'audience a été annulée
        // ---------------------------------------------------------------------
        return $this->statut === 'Annulée';
    }

    public function isPrevue(): bool
    {
        // ---------------------------------------------------------------------
        // Helper d'état : vérifie si l'audience est toujours planifiée
        // ---------------------------------------------------------------------
        return $this->statut === 'Prévue';
    }
}
