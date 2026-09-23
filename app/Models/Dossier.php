<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Attributs modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'numero_dossier', // Numéro de référence annuel unique (ex: "DOS-2026-00001")
        'type_affaire',   // Type d'affaire juridique (ex: "Civil", "Pénal", "Travail")
        'statut',         // État d'avancement ("En cours", "Gagné", "Perdu", "Fermé")
        'date_ouverture', // Date d'ouverture du dossier
        'date_fermeture', // Date de clôture (obligatoire si statut = "Fermé")
        'archive',        // Booléen indiquant si le dossier est archivé
        'client_id',      // Clé étrangère vers la table 'clients'
        'avocat_id',      // Clé étrangère vers la table 'users' (doit avoir le rôle Avocat)
    ];

    // -------------------------------------------------------------------------
    // Castings automatiques des colonnes de base de données
    // -------------------------------------------------------------------------
    protected $casts = [
        'date_ouverture' => 'date',
        'date_fermeture' => 'date',
        'archive' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : lie ce dossier au client qui l'a mandaté.
        // Utilise la clé étrangère par défaut 'client_id' -> clients.id.
        // ---------------------------------------------------------------------
        return $this->belongsTo(Client::class);
    }

    public function avocat(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo personnalisée :
        // Cible le modèle User en spécifiant explicitement la clé 'avocat_id'.
        // ---------------------------------------------------------------------
        return $this->belongsTo(User::class, 'avocat_id');
    }

    public function audiences(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : renvoie toutes les audiences planifiées pour ce dossier.
        // SQL : WHERE audiences.dossier_id = dossiers.id
        // ---------------------------------------------------------------------
        return $this->hasMany(Audience::class);
    }

    public function documents(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : pièces jointes et fichiers rattachés au dossier.
        // SQL : WHERE documents.dossier_id = dossiers.id
        // ---------------------------------------------------------------------
        return $this->hasMany(Document::class);
    }

    public function factures(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : factures d'honoraires et frais émis pour cette affaire.
        // SQL : WHERE factures.dossier_id = dossiers.id
        // ---------------------------------------------------------------------
        return $this->hasMany(Facture::class);
    }

    public function historiques(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : journal d'audit chronologique de toutes les actions sur ce dossier.
        // SQL : WHERE historiques.dossier_id = dossiers.id
        // ---------------------------------------------------------------------
        return $this->hasMany(Historique::class);
    }

    public function scopeRecherche(Builder $query, string $search): Builder
    {
        // ---------------------------------------------------------------------
        // Encapsulation dans une closure pour générer des parenthèses SQL WHERE (a OR b OR c)
        // ---------------------------------------------------------------------
        return $query->where(function (Builder $query) use ($search) {
            // -----------------------------------------------------------------
            // Recherche par correspondance partielle sur le numéro de dossier
            // -----------------------------------------------------------------
            $query->where('numero_dossier', 'like', "%{$search}%")
                // -------------------------------------------------------------
                // OR recherche par type d'affaire
                // -------------------------------------------------------------
                ->orWhere('type_affaire', 'like', "%{$search}%")
                // -------------------------------------------------------------
                // OR recherche sur le client lié via son propre scope local
                // -------------------------------------------------------------
                ->orWhereHas('client', fn (Builder $q) => $q->recherche($search));
        });
    }

    public function isArchive(): bool
    {
        // ---------------------------------------------------------------------
        // Vérifie si le dossier est marqué comme archivé
        // ---------------------------------------------------------------------
        return $this->archive === true;
    }

    public function isEnCours(): bool
    {
        // ---------------------------------------------------------------------
        // Vérifie si le dossier est actif ("En cours")
        // ---------------------------------------------------------------------
        return $this->statut === 'En cours';
    }

    public function enregistrerAction(string $action, User $user): Historique
    {
        // ---------------------------------------------------------------------
        // Ajoute une entrée dans la table 'historiques' rattachée à ce dossier
        // et à l'utilisateur qui a déclenché l'événement.
        // ---------------------------------------------------------------------
        return $this->historiques()->create([
            'action' => $action,
            'date_action' => now(),
            'user_id' => $user->id,
        ]);
    }
}
