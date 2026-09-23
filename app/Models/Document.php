<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'nom',         // Nom d'affichage du fichier (ex: "Contrat_Prestation.pdf")
        'chemin',      // Chemin relatif de stockage sur le disque public
        'type',        // Catégorie du document (Contrat, Pièce d'identité, Jugement, etc.)
        'taille',      // Taille du fichier en octets (bytes)
        'dossier_id',  // Clé étrangère vers dossiers.id
        'uploaded_by', // Clé étrangère vers users.id (utilisateur ayant téléversé la pièce)
    ];

    // -------------------------------------------------------------------------
    // Castings de types
    // -------------------------------------------------------------------------
    protected $casts = [
        'taille' => 'integer',
    ];

    public function dossier(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo : le dossier juridique propriétaire de ce document
        // ---------------------------------------------------------------------
        return $this->belongsTo(Dossier::class);
    }

    public function uploader(): BelongsTo
    {
        // ---------------------------------------------------------------------
        // Relation BelongsTo personnalisée :
        // Cible l'utilisateur auteur du téléversement via la clé 'uploaded_by'
        // ---------------------------------------------------------------------
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTailleFormatteeAttribute(): string
    {
        // ---------------------------------------------------------------------
        // Accesseur Eloquent ($document->taille_formattee) :
        // Convertit la taille brute en octets en chaîne lisible (o, Ko, Mo)
        // ---------------------------------------------------------------------
        if ($this->taille === null) {
            return 'Inconnue';
        }

        // Moins de 1 Ko : affichage direct en octets
        if ($this->taille < 1024) {
            return "{$this->taille} o";
        }

        // Entre 1 Ko et 1 Mo : conversion en Ko avec 1 décimale
        if ($this->taille < 1048576) {
            return round($this->taille / 1024, 1).' Ko';
        }

        // Plus de 1 Mo : conversion en Mo avec 1 décimale
        return round($this->taille / 1048576, 1).' Mo';
    }
}
