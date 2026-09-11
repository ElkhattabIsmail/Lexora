<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_facture',
        'montant',
        'date_facture',
        'statut',
        'client_id',
        'dossier_id',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_facture' => 'date',
    ];

    /**
     * Le client destinataire de la facture.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Le dossier ayant généré la facture (optionnel).
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * Les paiements reçus pour cette facture.
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Récupère les factures correspondant à une recherche (numéro, dossier ou client).
     */
    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('numero_facture', 'like', "%{$search}%")
                ->orWhereHas('dossier', fn (Builder $q) => $q->where('numero_dossier', 'like', "%{$search}%"))
                ->orWhereHas('client', fn (Builder $q) => $q->recherche($search));
        });
    }

    /**
     * Vérifie si la facture est payée.
     */
    public function isPayee(): bool
    {
        return $this->statut === 'Payée';
    }

    /**
     * Calcule le montant restant dû.
     */
    public function getMontantRestantAttribute(): float
    {
        if (array_key_exists('paiements_sum_montant', $this->attributes)) {
            $totalPaye = (float) $this->paiements_sum_montant;
        } elseif ($this->relationLoaded('paiements')) {
            $totalPaye = (float) $this->paiements->sum('montant');
        } else {
            $totalPaye = (float) $this->paiements()->sum('montant');
        }

        return max(0, (float) $this->montant - $totalPaye);
    }

    /**
     * Synchronise le statut de la facture selon le solde restant dû.
     */
    public function synchroniserStatut(): void
    {
        $totalPaye = (float) $this->paiements()->sum('montant');

        $this->update([
            'statut' => (float) $this->montant - $totalPaye <= 0 ? 'Payée' : 'Non payée',
        ]);
    }
}
