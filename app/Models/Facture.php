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

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'numero_facture', // Référence unique (ex: "FAC-2026-00001")
        'montant',        // Montant total facturé TTC
        'date_facture',   // Date d'émission de la facture
        'statut',         // "Payée" | "Non payée"
        'client_id',      // Clé étrangère vers clients.id
        'dossier_id',     // Clé étrangère facultative vers dossiers.id
    ];

    // -------------------------------------------------------------------------
    // Castings de types
    // -------------------------------------------------------------------------
    protected $casts = [
        'montant' => 'decimal:2',
        'date_facture' => 'date',
    ];

    public function client(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : le client débiteur de la facture
        // ---------------------------------------------------------------------
        return $this->belongsTo(Client::class);
    }

    public function dossier(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo facultative : le dossier juridique concerné
        // ---------------------------------------------------------------------
        return $this->belongsTo(Dossier::class);
    }

    public function paiements(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : règlements partiels ou totaux versés pour cette facture
        // ---------------------------------------------------------------------
        return $this->hasMany(Paiement::class);
    }

    public function scopeRecherche(Builder $query, string $search): Builder
    {
        // ---------------------------------------------------------------------
        // Recherche multicritère : numéro de facture, numéro de dossier ou client lié
        // ---------------------------------------------------------------------
        return $query->where(function (Builder $query) use ($search) {
            $query->where('numero_facture', 'like', "%{$search}%")
                ->orWhereHas('dossier', fn (Builder $q) => $q->where('numero_dossier', 'like', "%{$search}%"))
                ->orWhereHas('client', fn (Builder $q) => $q->recherche($search));
        });
    }

    public function isPayee(): bool
    {
        // ---------------------------------------------------------------------
        // Vérifie si la facture est entièrement soldée
        // ---------------------------------------------------------------------
        return $this->statut === 'Payée';
    }

    public function getMontantRestantAttribute(): float
    {
        // ---------------------------------------------------------------------
        // Accesseur dynamique ($facture->montant_restant) :
        // Optimisé par ordre d'efficacité :
        // 1. Colonne agrégée SQL préchargée avec withSum('paiements', 'montant')
        // 2. Collection déjà chargée en mémoire
        // 3. Requête SQL de repli SUM(montant)
        // ---------------------------------------------------------------------
        if (array_key_exists('paiements_sum_montant', $this->attributes)) {
            $totalPaye = (float) $this->paiements_sum_montant;
        } elseif ($this->relationLoaded('paiements')) {
            $totalPaye = (float) $this->paiements->sum('montant');
        } else {
            $totalPaye = (float) $this->paiements()->sum('montant');
        }

        // max(0, ...) garantit que le reste à payer ne soit jamais négatif
        return max(0, (float) $this->montant - $totalPaye);
    }

    public function synchroniserStatut(): void
    {
        // ---------------------------------------------------------------------
        // Recalcule la somme totale des paiements enregistrés en base
        // et met à jour le statut en 'Payée' si le solde restant est nul ou négatif.
        // ---------------------------------------------------------------------
        $totalPaye = (float) $this->paiements()->sum('montant');

        $this->update([
            'statut' => (float) $this->montant - $totalPaye <= 0 ? 'Payée' : 'Non payée',
        ]);
    }
}
