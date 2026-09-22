<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Dossier Model — represents a legal case file in the law firm.
 *
 * A dossier is created for a specific client and assigned to one lawyer
 * (avocat). It aggregates all related entities: audiences (hearings),
 * documents, invoices (factures), and a chronological action log
 * (historiques).
 *
 * Relationships:
 *   - belongsTo Client     (the client this case belongs to)
 *   - belongsTo User       (the lawyer responsible, via avocat_id)
 *   - hasMany   Audience   (scheduled court hearings)
 *   - hasMany   Document   (uploaded files)
 *   - hasMany   Facture    (invoices generated for this case)
 *   - hasMany   Historique (action log entries)
 */
class Dossier extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'numero_dossier', // Auto-generated reference e.g. "DOS-2024-00001"
        'type_affaire',   // Type of legal matter e.g. "Civil", "Pénal"
        'statut',         // "En cours" | "Gagné" | "Perdu" | "Fermé"
        'date_ouverture', // Date the case was opened
        'date_fermeture', // Date the case was closed (nullable)
        'archive',        // Boolean — whether the case is archived
        'client_id',      // FK → clients.id
        'avocat_id',      // FK → users.id (must have role Avocat)
    ];

    /**
     * Automatic type casts for database columns.
     *
     * - date_ouverture / date_fermeture → Carbon date (no time component)
     * - archive                         → native PHP bool
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_ouverture' => 'date',
        'date_fermeture' => 'date',
        'archive' => 'boolean',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The client this legal case belongs to.
     *
     * Usage : $dossier->client->nom_complet
     * Similar: Facture::client() — both link an entity to a Client.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * The lawyer (User with role Avocat) responsible for this case.
     * Uses 'avocat_id' as the foreign key instead of the default 'user_id'.
     *
     * Usage : $dossier->avocat->nom_complet
     * Similar: Audience::avocat() — identical foreign key pattern.
     *
     * @return BelongsTo<User, $this>
     */
    public function avocat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avocat_id');
    }

    /**
     * All court hearings scheduled for this case.
     *
     * Usage : $dossier->audiences  →  Collection<Audience>
     * Similar: documents(), factures(), historiques() — all hasMany on Dossier.
     *
     * @return HasMany<Audience, $this>
     */
    public function audiences(): HasMany
    {
        return $this->hasMany(Audience::class);
    }

    /**
     * All uploaded files (documents) attached to this case.
     *
     * Usage : $dossier->documents  →  Collection<Document>
     * Similar: audiences(), factures(), historiques() — all hasMany on Dossier.
     *
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * All invoices (factures) generated for this case.
     *
     * Usage : $dossier->factures  →  Collection<Facture>
     * Similar: audiences(), documents(), historiques() — all hasMany on Dossier.
     *
     * @return HasMany<Facture, $this>
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    /**
     * Chronological activity log for this case (every action recorded).
     *
     * Usage : $dossier->historiques  →  Collection<Historique>
     * Similar: audiences(), documents(), factures() — all hasMany on Dossier.
     *
     * @return HasMany<Historique, $this>
     */
    public function historiques(): HasMany
    {
        return $this->hasMany(Historique::class);
    }

    // =========================================================================
    // Eloquent local scopes
    // =========================================================================

    /**
     * Filters dossiers by a free-text search across multiple fields:
     *   - numero_dossier (case reference number)
     *   - type_affaire   (type of legal matter)
     *   - client         (delegated to Client::scopeRecherche)
     *
     * @param  Builder $query  Injected automatically by Eloquent.
     * @param  string  $search Term to search; wildcards added automatically.
     *
     * Usage:
     *   Dossier::recherche('DOS-2024')
     *   Dossier::recherche('dupont')   ← matches client name
     *
     * Similar: Client::scopeRecherche(), User::scopeRecherche(),
     *          Facture::scopeRecherche() — all follow the same LIKE pattern.
     */
    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('numero_dossier', 'like', "%{$search}%")
                ->orWhere('type_affaire', 'like', "%{$search}%")
                // Delegates client name matching to the Client scope.
                ->orWhereHas('client', fn (Builder $q) => $q->recherche($search));                                                                                                                                                                                                                                                                                                        
        });
    }

    // =========================================================================
    // State helpers
    // =========================================================================

    /**
     * Returns true when this case has been archived.
     * Equivalent to checking $dossier->archive === true directly.
     *
     * Usage : if ($dossier->isArchive()) { ... }
     * Similar: isEnCours() — same boolean-state pattern.
     */
    public function isArchive(): bool
    {
        return $this->archive === true;
    }

    /**
     * Returns true when this case's status is "En cours" (active / open).
     *
     * Usage : if ($dossier->isEnCours()) { ... }
     * Similar: isArchive() — same boolean-state pattern.
     */
    public function isEnCours(): bool
    {
        return $this->statut === 'En cours';
    }

    /**
     * Appends an entry to this case's activity log (historiques table).
     * Called throughout the application whenever a significant action occurs
     * (case opened, status changed, document uploaded, hearing scheduled, etc.).
     *
     * @param  string  $action  Human-readable description of what happened,
     *                          e.g. "Statut modifié : En cours → Gagné".
     * @param  User    $user    The staff member who performed the action.
     *
     * @return Historique  The newly created log entry.
     *
     * Usage:
     *   $dossier->enregistrerAction('Dossier ouvert', $request->user());
     *
     * Called by: DossierController, AudienceController, DocumentController,
     *            FactureController, PaiementController, TraiterTeleversementDocument.
     */
    public function enregistrerAction(string $action, User $user): Historique
    {
        return $this->historiques()->create([
            'action' => $action,
            'date_action' => now(),
            'user_id' => $user->id,
        ]);
    }
}
