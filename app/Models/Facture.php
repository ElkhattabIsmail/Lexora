<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Facture Model — represents an invoice issued to a client.
 *
 * Each invoice belongs to a Client and optionally to a Dossier (legal case).
 * Payments (Paiement) are tracked against the invoice and the paid status
 * is automatically synchronised via synchroniserStatut().
 *
 * Relationships:
 *   - belongsTo Client    (the recipient of the invoice)
 *   - belongsTo Dossier   (the case that generated this invoice, nullable)
 *   - hasMany   Paiement  (payments recorded against this invoice)
 */
class Facture extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'numero_facture', // Auto-generated reference e.g. "FAC-2024-00001"
        'montant',        // Total amount due (decimal)
        'date_facture',   // Invoice date
        'statut',         // "Payée" | "Non payée"
        'client_id',      // FK → clients.id
        'dossier_id',     // FK → dossiers.id (nullable)
    ];

    /**
     * Automatic type casts.
     *
     * - montant      → decimal string with 2 decimal places
     * - date_facture → Carbon date instance
     *
     * @var array<string, string>
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'date_facture' => 'date',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The client this invoice is addressed to.
     *
     * Usage : $facture->client->nom_complet
     * Similar: Dossier::client() — same belongsTo(Client) pattern.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * The legal case (dossier) that generated this invoice (optional).
     * A facture can exist without a dossier (e.g. a standalone retainer).
     *
     * Usage : $facture->dossier?->numero_dossier
     * Similar: Dossier::factures() is the inverse hasMany side.
     *
     * @return BelongsTo<Dossier, $this>
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * All payments recorded against this invoice.
     * The sum of paiements determines the 'montant_restant' and 'statut'.
     *
     * Usage : $facture->paiements  →  Collection<Paiement>
     * Similar: Paiement::facture() is the inverse belongsTo side.
     *
     * @return HasMany<Paiement, $this>
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    // =========================================================================
    // Eloquent local scopes
    // =========================================================================

    /**
     * Filters invoices by a free-text search across:
     *   - numero_facture  (invoice reference number)
     *   - dossier         (case number, via whereHas)
     *   - client          (delegated to Client::scopeRecherche)
     *
     * @param  Builder $query  Injected automatically by Eloquent.
     * @param  string  $search The term to search; wildcards added automatically.
     *
     * Usage:
     *   Facture::recherche('FAC-2024')->paginate(15)
     *
     * Similar: Client::scopeRecherche(), Dossier::scopeRecherche() — same pattern.
     */
    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('numero_facture', 'like', "%{$search}%")
                ->orWhereHas('dossier', fn (Builder $q) => $q->where('numero_dossier', 'like', "%{$search}%"))
                ->orWhereHas('client', fn (Builder $q) => $q->recherche($search));
        });
    }

    // =========================================================================
    // State helpers & Accessors
    // =========================================================================

    /**
     * Returns true when this invoice has been fully paid.
     *
     * Usage : if ($facture->isPayee()) { ... }
     * Similar: Audience::isAnnulee(), Audience::isPrevue() — same status-check pattern.
     */
    public function isPayee(): bool
    {
        return $this->statut === 'Payée';
    }

    /**
     * Accessor — computes the outstanding balance on this invoice.
     *
     * Optimisation: checks three sources in order of efficiency:
     *   1. withSum() aggregate already loaded (e.g. via withSum('paiements','montant'))
     *   2. Paiement relation already in memory (lazy-loaded earlier)
     *   3. Fresh database query as a last resort
     *
     * Usage : $facture->montant_restant  →  float (always ≥ 0)
     * Similar: synchroniserStatut() uses the same total-payment calculation.
     */
    public function getMontantRestantAttribute(): float
    {
        // Prefer the pre-aggregated column injected by Eloquent withSum().
        if (array_key_exists('paiements_sum_montant', $this->attributes)) {
            $totalPaye = (float) $this->paiements_sum_montant;
        } elseif ($this->relationLoaded('paiements')) {
            // If the relation is already in memory, sum it without a new query.
            $totalPaye = (float) $this->paiements->sum('montant');
        } else {
            // Fallback: run a fresh SUM query on the paiements table.
            $totalPaye = (float) $this->paiements()->sum('montant');
        }

        // Clamp to 0 so we never return a negative balance.
        return max(0, (float) $this->montant - $totalPaye);
    }

    /**
     * Recalculates and persists the invoice 'statut' based on actual payments.
     * Sets status to "Payée" when total payments cover the full amount,
     * or "Non payée" otherwise.
     *
     * Called automatically after every payment is created or deleted:
     *   - PaiementController::store()
     *   - PaiementController::destroy()
     *
     * Usage : $facture->synchroniserStatut();
     * Similar: getMontantRestantAttribute() — both compute total paid amount.
     */
    public function synchroniserStatut(): void
    {
        // Always query the database so we have an up-to-date sum.
        $totalPaye = (float) $this->paiements()->sum('montant');

        $this->update([
            'statut' => (float) $this->montant - $totalPaye <= 0 ? 'Payée' : 'Non payée',
        ]);
    }
}
