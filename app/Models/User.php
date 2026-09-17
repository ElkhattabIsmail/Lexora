<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model — represents a staff member of the law firm.
 *
 * Each user is assigned exactly one Role (Administrateur, Avocat, or
 * Assistant Juridique). The role drives every access-control decision
 * in the application via CheckRole middleware and hasRole() helper.
 *
 * Relationships:
 *   - belongsTo Role         (the user's permission level)
 *   - hasMany   Dossier      (legal files the user manages as lawyer)
 *   - hasMany   Audience     (court hearings the user handles as lawyer)
 *   - hasMany   Document     (files the user has uploaded)
 *   - hasMany   Historique   (activity log entries created by the user)
 *   - hasMany   Notification (in-app alerts sent to the user)
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Mass-assignable attributes.
     * Only these fields may be filled via create() / fill() / update().
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',        // Family name
        'prenom',     // Given name
        'email',
        'telephone',
        'password',
        'role_id',    // Foreign key → roles.id
    ];

    /**
     * Attributes excluded from JSON / array serialisation.
     * Prevents the raw password hash and remember-me token from leaking
     * in API responses or log output.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts applied automatically when reading from the database.
     *
     * - email_verified_at → Carbon datetime instance
     * - password          → automatically hashed by Laravel when set via assignment
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The role assigned to this user (Administrateur / Avocat / Assistant Juridique).
     *
     * Usage : $user->role->nom
     * Similar: Dossier::avocat(), Audience::avocat() also use belongsTo(User).
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * All legal case files (dossiers) where this user is the responsible lawyer.
     * Uses the non-default foreign key 'avocat_id' instead of 'user_id'.
     *
     * Usage : $user->dossiers  →  Collection<Dossier>
     * Similar: audiences() — same avocat_id pattern.
     *
     * @return HasMany<Dossier, $this>
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'avocat_id');
    }

    /**
     * All court hearings (audiences) that this user manages as the assigned lawyer.
     * Uses the non-default foreign key 'avocat_id'.
     *
     * Usage : $user->audiences  →  Collection<Audience>
     * Similar: dossiers() — same avocat_id pattern.
     *
     * @return HasMany<Audience, $this>
     */
    public function audiences(): HasMany
    {
        return $this->hasMany(Audience::class, 'avocat_id');
    }

    /**
     * All documents uploaded by this user.
     * Uses the non-default foreign key 'uploaded_by'.
     *
     * Usage : $user->documents  →  Collection<Document>
     * Similar: Document::uploader() is the inverse side of this relationship.
     *
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * All activity-log entries (historiques) authored by this user.
     *
     * Usage : $user->historiques  →  Collection<Historique>
     * Similar: Historique::user() is the inverse side.
     *
     * @return HasMany<Historique, $this>
     */
    public function historiques(): HasMany
    {
        return $this->hasMany(Historique::class, 'user_id');
    }

    /**
     * All in-app notifications addressed to this user.
     *
     * Note: this overrides Laravel's built-in Notifiable::notifications()
     * to use our own custom Notification Eloquent model.
     *
     * Usage : $user->notifications  →  Collection<Notification>
     * Similar: Notification::user() is the inverse side.
     *
     * @return HasMany<Notification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // =========================================================================
    // Role-check helpers
    // Thin, readable wrappers around hasRole() used in controllers & Blade.
    // =========================================================================

    /**
     * Returns true when this user has the "Administrateur" role.
     *
     * Usage : if ($user->isAdministrateur()) { ... }
     * Similar: isAvocat(), isAssistantJuridique(), hasRole()
     */
    public function isAdministrateur(): bool
    {
        return $this->role?->nom === 'Administrateur';
    }

    /**
     * Returns true when this user has the "Avocat" role.
     *
     * Usage : if ($user->isAvocat()) { ... }
     * Similar: isAdministrateur(), isAssistantJuridique(), hasRole()
     */
    public function isAvocat(): bool
    {
        return $this->role?->nom === 'Avocat';
    }

    /**
     * Returns true when this user has the "Assistant Juridique" role.
     *
     * Usage : if ($user->isAssistantJuridique()) { ... }
     * Similar: isAdministrateur(), isAvocat(), hasRole()
     */
    public function isAssistantJuridique(): bool
    {
        return $this->role?->nom === 'Assistant Juridique';
    }

    /**
     * Returns true when this user holds at least one of the given roles.
     * Used by CheckRole middleware and role-gate checks across the app.
     *
     * @param  string|array<string>  $roles
     *         - A single role name  : 'Avocat'
     *         - Comma-separated     : 'Avocat,Administrateur'
     *         - Array               : ['Avocat', 'Administrateur']
     *
     * Usage:
     *   $user->hasRole('Avocat')
     *   $user->hasRole(['Avocat', 'Administrateur'])
     *
     * Similar: CheckRole::handle() calls this method to enforce route protection.
     */
    public function hasRole(string|array $roles): bool
    {
        // If the user has no role assigned yet, deny access.
        $roleName = $this->role?->nom;

        if (! $roleName) {
            return false;
        }

        // Convert a comma-separated string to an array for uniform comparison.
        if (is_string($roles)) {
            $roles = array_map('trim', explode(',', $roles));
        }

        // Strict comparison prevents "Avocat" from matching "avocat".
        return in_array($roleName, $roles, true);
    }

    // =========================================================================
    // Static query helpers
    // =========================================================================

    /**
     * Returns a Builder pre-scoped to users whose role is "Avocat",
     * ordered alphabetically by family name then given name.
     *
     * This is a static factory method, NOT an Eloquent local scope,
     * so it must be called directly (no `scope` prefix).
     *
     * Usage:
     *   $avocats = User::avocats()->get();
     *   $avocats = User::avocats()->where('prenom', 'Ali')->first();
     *
     * Similar: scopeRecherche() — also returns a constrained Builder.
     */
    public static function avocats(): Builder
    {
        return static::query()
            ->whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))
            ->orderBy('nom')
            ->orderBy('prenom');
    }

    // =========================================================================
    // Eloquent local scopes
    // Called as User::recherche('term') — Laravel strips the "scope" prefix.
    // =========================================================================

    /**
     * Filters the query to users whose nom, prenom, or email matches
     * the given search term (case-insensitive SQL LIKE on all three columns).
     *
     * @param  Builder $query  Injected automatically by Eloquent.
     * @param  string  $search The term to search for; wildcards are added automatically.
     *
     * Usage:
     *   User::recherche('dupont')->paginate(15)
     *
     * Similar: Client::scopeRecherche(), Dossier::scopeRecherche(),
     *          Facture::scopeRecherche() — identical LIKE pattern across all models.
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
    // Accessors & Mutators
    // =========================================================================

    /**
     * Accessor — concatenates prenom + nom into a single readable string.
     * Trailing/leading spaces are trimmed in case either part is empty.
     *
     * Usage : $user->nom_complet  →  "Ali Dupont"
     * Similar: Client::getNomCompletAttribute() — same pattern.
     */
    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    /**
     * Accessor — compatibility alias so any component that reads $user->name
     * (e.g. Breeze navigation partial) receives the full name automatically.
     *
     * Usage : $user->name  →  delegates to getNomCompletAttribute()
     * Similar: getNomCompletAttribute()
     */
    public function getNameAttribute(): string
    {
        return $this->getNomCompletAttribute();
    }

    /**
     * Mutator — splits a full-name string on the first space and stores
     * the result in the separate 'prenom' and 'nom' database columns.
     * This keeps Breeze flows (which set $user->name) compatible with our
     * split-name schema.
     *
     * @param  string|null  $value  Full name e.g. "Ali Dupont".
     *                              Null / empty values are silently ignored.
     *
     * Usage : $user->name = "Ali Dupont";
     */
    public function setNameAttribute(?string $value): void
    {
        if (! $value) {
            return;
        }

        // Split on the FIRST space only so compound family names are preserved.
        // "Ali Ben Dupont" → prenom="Ali", nom="Ben Dupont"
        $parts = explode(' ', trim($value), 2);
        $this->attributes['prenom'] = $parts[0] ?? '';
        $this->attributes['nom'] = $parts[1] ?? ($this->attributes['nom'] ?? $parts[0]);
    }
}
