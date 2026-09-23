<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historique extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'action',      // Libellé textuel de l'action accomplie (ex: "Statut modifié : En cours → Gagné")
        'date_action', // Date et heure précises de l'événement
        'dossier_id',  // Clé étrangère vers dossiers.id
        'user_id',     // Clé étrangère vers users.id (auteur de l'action)
    ];

    // -------------------------------------------------------------------------
    // Castings de types
    // -------------------------------------------------------------------------
    protected $casts = [
        'date_action' => 'datetime',
    ];

    public function dossier(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : le dossier juridique tracé par cette ligne d'historique
        // ---------------------------------------------------------------------
        return $this->belongsTo(Dossier::class);
    }

    public function user(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : l'utilisateur qui a déclenché l'événement
        // ---------------------------------------------------------------------
        return $this->belongsTo(User::class);
    }
}
