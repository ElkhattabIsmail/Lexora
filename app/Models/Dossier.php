<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_dossier',
        'type_affaire',
        'statut',
        'date_ouverture',
        'date_fermeture',
        'archive',
        'client_id',
        'avocat_id',
    ];

    protected $casts = [
        'date_ouverture' => 'date',
        'date_fermeture' => 'date',
        'archive' => 'boolean',
    ];

    /**
     * Le client propriétaire du dossier.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * L'avocat responsable du dossier.
     */
    public function avocat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avocat_id');
    }

    /**
     * Les audiences liées au dossier.
     */
    public function audiences(): HasMany
    {
        return $this->hasMany(Audience::class);
    }

    /**
     * Les documents contenus dans le dossier.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Les factures générées par le dossier.
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    /**
     * L'historique des actions sur le dossier.
     */
    public function historiques(): HasMany
    {
        return $this->hasMany(Historique::class);
    }

    /**
     * Vérifie si le dossier est archivé.
     */
    public function isArchive(): bool
    {
        return $this->archive === true;
    }

    /**
     * Vérifie si le dossier est en cours.
     */
    public function isEnCours(): bool
    {
        return $this->statut === 'En cours';
    }
}
