<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
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

    /**
     * Le rôle de l'utilisateur.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Les dossiers dont l'utilisateur est responsable (avocat).
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'avocat_id');
    }

    /**
     * Les audiences gérées par l'utilisateur.
     */
    public function audiences(): HasMany
    {
        return $this->hasMany(Audience::class, 'avocat_id');
    }

    /**
     * Les documents téléversés par l'utilisateur.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * Les entrées d'historique créées par l'utilisateur.
     */
    public function historiques(): HasMany
    {
        return $this->hasMany(Historique::class, 'user_id');
    }

    /**
     * Les notifications reçues par l'utilisateur.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdministrateur(): bool
    {
        return $this->role?->nom === 'Administrateur';
    }

    /**
     * Vérifie si l'utilisateur est avocat.
     */
    public function isAvocat(): bool
    {
        return $this->role?->nom === 'Avocat';
    }

    /**
     * Vérifie si l'utilisateur est assistant juridique.
     */
    public function isAssistantJuridique(): bool
    {
        return $this->role?->nom === 'Assistant Juridique';
    }

    /**
     * Vérifie si l'utilisateur possède l'un des rôles spécifiés.
     *
     * @param  string|array<string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roleName = $this->role?->nom;

        if (! $roleName) {
            return false;
        }

        if (is_string($roles)) {
            $roles = array_map('trim', explode(',', $roles));
        }

        return in_array($roleName, $roles, true);
    }

    /**
     * Nom complet de l'utilisateur.
     */
    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    /**
     * Compatibilité accessor avec les composants attendant "name".
     */
    public function getNameAttribute(): string
    {
        return $this->getNomCompletAttribute();
    }

    /**
     * Compatibilité mutator pour "name".
     */
    public function setNameAttribute(?string $value): void
    {
        if (! $value) {
            return;
        }

        $parts = explode(' ', trim($value), 2);
        $this->attributes['prenom'] = $parts[0] ?? '';
        $this->attributes['nom'] = $parts[1] ?? ($this->attributes['nom'] ?? $parts[0]);
    }
}
