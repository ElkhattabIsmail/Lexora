<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'montant',       // Montant versé en euros (décimal)
        'date_paiement', // Date de réception du paiement
        'mode_paiement', // Mode de règlement : "Virement", "Chèque", "Carte bancaire", "Espèces"
        'reference',     // Référence bancaire ou numéro de chèque facultatif
        'facture_id',    // Clé étrangère vers factures.id
    ];

    // -------------------------------------------------------------------------
    // Castings de types
    // -------------------------------------------------------------------------
    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function facture(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : la facture à laquelle ce règlement est affecté
        // ---------------------------------------------------------------------
        return $this->belongsTo(Facture::class);
    }
}
