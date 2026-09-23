<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'nom',       // Nom de famille ou raison sociale de l'entreprise
        'prenom',    // Prénom (null si entreprise)
        'telephone', // Numéro de téléphone de contact
        'email',     // Adresse email unique
        'adresse',   // Adresse postale
        'type',      // 'Particulier' ou 'Entreprise'
    ];

    // -------------------------------------------------------------------------
    // Castings de types
    // -------------------------------------------------------------------------
    protected $casts = [
        'type' => 'string',
    ];

    public function dossiers(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : tous les dossiers ouverts pour ce client
        // SQL : WHERE dossiers.client_id = clients.id
        // ---------------------------------------------------------------------
        return $this->hasMany(Dossier::class);
    }

    public function factures(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : ensemble des factures émises pour ce client
        // SQL : WHERE factures.client_id = clients.id
        // ---------------------------------------------------------------------
        return $this->hasMany(Facture::class);
    }

    public function scopeRecherche(Builder $query, string $search): Builder
    {
        // ---------------------------------------------------------------------
        // Scope local permettant la recherche multicritère sur nom, prénom ou email
        // SQL : WHERE (nom LIKE %...% OR prenom LIKE %...% OR email LIKE %...%)
        // ---------------------------------------------------------------------
        return $query->where(function (Builder $query) use ($search) {
            $query->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function isEntreprise(): bool
    {
        // ---------------------------------------------------------------------
        // Helper d'état : vérifie si le client est une personne morale ("Entreprise")
        // ---------------------------------------------------------------------
        return $this->type === 'Entreprise';
    }

    public function isParticulier(): bool
    {
        // ---------------------------------------------------------------------
        // Helper d'état : vérifie si le client est une personne physique ("Particulier")
        // ---------------------------------------------------------------------
        return $this->type === 'Particulier';
    }

    public function getNomCompletAttribute(): string
    {
        // ---------------------------------------------------------------------
        // Accesseur Eloquent ($client->nom_complet) :
        // Pour une entreprise, retourne uniquement la raison sociale.
        // Pour un particulier, concatène prénom et nom proprement.
        // ---------------------------------------------------------------------
        if ($this->isEntreprise()) {
            return $this->nom;
        }

        return trim("{$this->prenom} {$this->nom}");
    }
}
