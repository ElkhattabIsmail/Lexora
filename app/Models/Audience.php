<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audience extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'heure',
        'tribunal',
        'observations',
        'statut',
        'dossier_id',
        'avocat_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Le dossier concerné par l'audience.
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * L'avocat responsable de l'audience.
     */
    public function avocat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avocat_id');
    }

    /**
     * Vérifie si l'audience est annulée.
     */
    public function isAnnulee(): bool
    {
        return $this->statut === 'Annulée';
    }

    /**
     * Vérifie si l'audience est prévue.
     */
    public function isPrevue(): bool
    {
        return $this->statut === 'Prévue';
    }
}
