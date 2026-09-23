<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'titre',   // Titre concis (ex: "Audience à venir")
        'message', // Corps complet du message de rappel
        'type',    // Type de notification (ex: "Audience")
        'lu',      // Booléen : true si la notification a été consultée
        'user_id', // Clé étrangère vers users.id (destinataire)
    ];

    // -------------------------------------------------------------------------
    // Castings de types
    // -------------------------------------------------------------------------
    protected $casts = [
        'lu' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : l'utilisateur destinataire de l'alerte
        // ---------------------------------------------------------------------
        return $this->belongsTo(User::class);
    }

    public function marquerCommeLue(): void
    {
        // ---------------------------------------------------------------------
        // Action métier : bascule le booléen 'lu' à true et persiste en base
        // ---------------------------------------------------------------------
        $this->update(['lu' => true]);
    }

    public function isNonLue(): bool
    {
        // ---------------------------------------------------------------------
        // Helper d'état : vérifie si l'alerte n'a pas encore été lue
        // ---------------------------------------------------------------------
        return $this->lu === false;
    }
}
