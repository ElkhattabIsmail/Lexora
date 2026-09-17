<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Paiement Model — a single payment recorded against an invoice (Facture).
 *
 * Multiple payments can be applied to one invoice until its balance reaches
 * zero, at which point Facture::synchroniserStatut() marks it as "Payée".
 *
 * Relationships:
 *   - belongsTo Facture  (the invoice this payment is applied to)
 */
class Paiement extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'montant',       // Amount paid in this single payment (decimal)
        'date_paiement', // Date the payment was received
        'mode_paiement', // Payment method e.g. "Virement", "Chèque", "Espèces"
        'reference',     // Bank reference or cheque number (optional)
        'facture_id',    // FK → factures.id
    ];

    /**
     * Automatic type casts.
     *
     * - montant       → decimal string with 2 decimal places
     * - date_paiement → Carbon date instance
     *
     * @var array<string, string>
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The invoice (Facture) this payment is applied against.
     * After create/delete, Facture::synchroniserStatut() is called by
     * PaiementController to keep the invoice status in sync.
     *
     * Usage : $paiement->facture->numero_facture
     * Similar: Facture::paiements() is the inverse hasMany side.
     *
     * @return BelongsTo<Facture, $this>
     */
    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }
}
