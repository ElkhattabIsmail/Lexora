<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'nom',        // Nom de famille
        'prenom',     // Prénom
        'email',      // Adresse email unique
        'telephone',  // Numéro de téléphone
        'password',   // Mot de passe hashé
        'role_id',    // Clé étrangère vers roles.id
    ];

    // -------------------------------------------------------------------------
    // Attributs masqués lors de la sérialisation (tableaux et JSON)
    // -------------------------------------------------------------------------
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        // ---------------------------------------------------------------------
        // Castings automatiques :
        // 'hashed' garantit le hashage automatique bcrypt/argon lors de l'assignation de mot de passe
        // ---------------------------------------------------------------------
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : le rôle attribué à l'utilisateur (Administrateur, Avocat, etc.)
        // ---------------------------------------------------------------------
        return $this->belongsTo(Role::class);
    }

    public function dossiers(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : dossiers confiés à cet utilisateur en tant qu'avocat référent
        // Clé étrangère personnalisée : 'avocat_id'
        // ---------------------------------------------------------------------
        return $this->hasMany(Dossier::class, 'avocat_id');
    }

    public function audiences(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : audiences assignées à cet avocat
        // Clé étrangère personnalisée : 'avocat_id'
        // ---------------------------------------------------------------------
        return $this->hasMany(Audience::class, 'avocat_id');
    }

    public function documents(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : documents téléversés par cet utilisateur
        // Clé étrangère personnalisée : 'uploaded_by'
        // ---------------------------------------------------------------------
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function historiques(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : historique des actions effectuées par cet utilisateur
        // ---------------------------------------------------------------------
        return $this->hasMany(Historique::class, 'user_id');
    }

    public function notifications(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : alertes et notifications internes adressées à cet utilisateur
        // ---------------------------------------------------------------------
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function isAdministrateur(): bool
    {
        // ---------------------------------------------------------------------
        // Helper de rôle : vérifie si l'utilisateur possède le rôle "Administrateur"
        // ---------------------------------------------------------------------
        return $this->role?->nom === 'Administrateur';
    }

    public function isAvocat(): bool
    {
        // ---------------------------------------------------------------------
        // Helper de rôle : vérifie si l'utilisateur possède le rôle "Avocat"
        // ---------------------------------------------------------------------
        return $this->role?->nom === 'Avocat';
    }

    public function isAssistantJuridique(): bool
    {
        // ---------------------------------------------------------------------
        // Helper de rôle : vérifie si l'utilisateur possède le rôle "Assistant Juridique"
        // ---------------------------------------------------------------------
        return $this->role?->nom === 'Assistant Juridique';
    }

    public function hasRole(string|array $roles): bool
    {
        // ---------------------------------------------------------------------
        // Vérification des autorisations par rôle (utilisé par le middleware CheckRole) :
        // ---------------------------------------------------------------------
        $roleName = $this->role?->nom;

        // Si aucun rôle n'est rattaché, accès refusé
        if (! $roleName) {
            return false;
        }

        // Supporte une chaîne avec virgules 'Avocat,Administrateur' convertie en tableau
        if (is_string($roles)) {
            $roles = array_map('trim', explode(',', $roles));
        }

        // Comparaison stricte sensible à la casse
        return in_array($roleName, $roles, true);
    }

    public static function avocats(): Builder
    {
        // ---------------------------------------------------------------------
        // Méthode de fabrique statique :
        // Renvoie une instance de requête pré-filtrée sur les utilisateurs ayant le rôle "Avocat",
        // triés alphabétiquement par nom puis prénom.
        // ---------------------------------------------------------------------
        return static::query()
            ->whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))
            ->orderBy('nom')
            ->orderBy('prenom');
    }

    public function scopeRecherche(Builder $query, string $search): Builder
    {
        // ---------------------------------------------------------------------
        // Scope local : recherche sur nom, prénom ou adresse email
        // ---------------------------------------------------------------------
        return $query->where(function (Builder $query) use ($search) {
            $query->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function getNomCompletAttribute(): string
    {
        // ---------------------------------------------------------------------
        // Accesseur ($user->nom_complet) : concatène proprement prénom et nom
        // ---------------------------------------------------------------------
        return trim("{$this->prenom} {$this->nom}");
    }

    public function getNameAttribute(): string
    {
        // ---------------------------------------------------------------------
        // Accesseur de compatibilité Laravel Breeze ($user->name)
        // ---------------------------------------------------------------------
        return $this->getNomCompletAttribute();
    }

    public function setNameAttribute(?string $value): void
    {
        // ---------------------------------------------------------------------
        // Mutateur de compatibilité Laravel Breeze :
        // Découpe la chaîne passée sur le premier espace pour renseigner 'prenom' et 'nom'
        // Exemple : "Jean Dupont" -> prenom="Jean", nom="Dupont"
        // ---------------------------------------------------------------------
        if (! $value) {
            return;
        }

        $parts = explode(' ', trim($value), 2);
        $this->attributes['prenom'] = $parts[0] ?? '';
        $this->attributes['nom'] = $parts[1] ?? ($this->attributes['nom'] ?? $parts[0]);
    }
}
