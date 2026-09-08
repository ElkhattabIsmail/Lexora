<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'chemin',
        'type',
        'taille',
        'dossier_id',
        'uploaded_by',
    ];

    protected $casts = [
        'taille' => 'integer',
    ];

    /**
     * Le dossier contenant ce document.
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * L'utilisateur ayant téléversé le document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Retourne la taille du fichier en format lisible (Ko, Mo).
     */
    public function getTailleFormatteeAttribute(): string
    {
        if ($this->taille === null) {
            return 'Inconnue';
        }

        if ($this->taille < 1024) {
            return "{$this->taille} o";
        }

        if ($this->taille < 1048576) {
            return round($this->taille / 1024, 1).' Ko';
        }

        return round($this->taille / 1048576, 1).' Mo';
    }
}
