<?php

namespace App\Models;

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
        $totalPaye = $this->paiements()->sum('montant');

        return max(0, (float) $this->montant - (float) $totalPaye);
    }
}
