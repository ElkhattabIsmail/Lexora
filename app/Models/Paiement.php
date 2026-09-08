<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference',
        'facture_id',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    /**
     * La facture associée au paiement.
     */
    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }
}
