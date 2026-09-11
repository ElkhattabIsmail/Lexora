<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Les dossiers juridiques du client.
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    /**
     * Les factures du client.
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    /**
     * Récupère les clients correspondant à une recherche (nom, prénom ou email).
     */
    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Vérifie si le client est une entreprise.
     */
    public function isEntreprise(): bool
    {
        return $this->type === 'Entreprise';
    }

    /**
     * Vérifie si le client est un particulier.
     */
    public function isParticulier(): bool
    {
        return $this->type === 'Particulier';
    }

    /**
     * Retourne le nom complet ou la raison sociale.
     */
    public function getNomCompletAttribute(): string
    {
        if ($this->isEntreprise()) {
            return $this->nom;
        }

        return trim("{$this->prenom} {$this->nom}");
    }
}
