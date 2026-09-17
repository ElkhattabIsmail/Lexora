<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Client Model — represents a customer of the law firm.
 *
 * A client may be either a private individual ("Particulier") or a company
 * ("Entreprise"). This distinction affects how the full name is displayed
 * and may influence business rules around billing.
 *
 * Relationships:
 *   - hasMany Dossier  (legal cases filed for this client)
 *   - hasMany Facture  (invoices issued to this client)
 */
class Client extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',       // Family name (or company name for Entreprise)
        'prenom',    // Given name (empty/null for Entreprise)
        'telephone',
        'email',
        'adresse',   // Mailing address
        'type',      // "Particulier" | "Entreprise"
    ];

    /**
     * Automatic type casts.
     * 'type' is stored as a VARCHAR and cast to a native PHP string on read.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'string',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * All legal case files (dossiers) belonging to this client.
     *
     * Usage : $client->dossiers  →  Collection<Dossier>
     * Similar: factures() — same hasMany pattern on Client.
     *
     * @return HasMany<Dossier, $this>
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    /**
     * All invoices (factures) issued to this client.
     *
     * Usage : $client->factures  →  Collection<Facture>
     * Similar: dossiers() — same hasMany pattern on Client.
     *
     * @return HasMany<Facture, $this>
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    // =========================================================================
    // Eloquent local scopes
    // =========================================================================

    /**
     * Filters clients by a free-text search across nom, prenom, and email
     * using case-insensitive SQL LIKE queries.
     *
     * @param  Builder $query  Injected automatically by Eloquent.
     * @param  string  $search The term to search for; wildcards added automatically.
     *
     * Usage:
     *   Client::recherche('dupont')->paginate(15)
     *
     * Similar: User::scopeRecherche(), Dossier::scopeRecherche(),
     *          Facture::scopeRecherche() — same pattern across all models.
     */
    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // =========================================================================
    // State helpers
    // =========================================================================

    /**
     * Returns true when this client is a company ("Entreprise").
     *
     * Usage : if ($client->isEntreprise()) { ... }
     * Similar: isParticulier() — mutually exclusive counterpart.
     */
    public function isEntreprise(): bool
    {
        return $this->type === 'Entreprise';
    }

    /**
     * Returns true when this client is a private individual ("Particulier").
     *
     * Usage : if ($client->isParticulier()) { ... }
     * Similar: isEntreprise() — mutually exclusive counterpart.
     */
    public function isParticulier(): bool
    {
        return $this->type === 'Particulier';
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * Accessor — returns the client's display name.
     *
     * For companies  : returns $this->nom (the company/trading name).
     * For individuals: returns "prenom nom" (first name then family name).
     *
     * Usage : $client->nom_complet  →  "Ali Dupont" or "ACME Corp"
     * Similar: User::getNomCompletAttribute() — same prenom+nom pattern.
     */
    public function getNomCompletAttribute(): string
    {
        // Companies only have a single trade name stored in 'nom'.
        if ($this->isEntreprise()) {
            return $this->nom;
        }

        // Trim handles the edge case where prenom is null/empty.
        return trim("{$this->prenom} {$this->nom}");
    }
}
