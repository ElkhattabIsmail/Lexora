<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Document Model — represents a file uploaded to a legal case (dossier).
 *
 * Files are stored on the 'public' disk under documents/{dossier_id}/.
 * The upload process is asynchronous: DocumentController writes the file
 * to a tmp/ path and dispatches TraiterTeleversementDocument to move it
 * and create this record in the background.
 *
 * Relationships:
 *   - belongsTo Dossier  (the case this document belongs to)
 *   - belongsTo User     (the staff member who uploaded it, via uploaded_by)
 */
class Document extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',         // Display name of the file (may differ from filename on disk)
        'chemin',      // Storage path relative to the 'public' disk root
        'type',        // Document category e.g. "Contrat", "Jugement", "Autre"
        'taille',      // File size in bytes (integer)
        'dossier_id',  // FK → dossiers.id
        'uploaded_by', // FK → users.id
    ];

    /**
     * Automatic type casts.
     * - taille → native PHP int (bytes).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'taille' => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The legal case (dossier) that contains this document.
     *
     * Usage : $document->dossier->numero_dossier
     * Similar: Audience::dossier(), Historique::dossier() — same belongsTo(Dossier).
     *
     * @return BelongsTo<Dossier, $this>
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * The staff member (User) who uploaded this document.
     * Uses 'uploaded_by' as the foreign key instead of the default 'user_id'.
     *
     * Usage : $document->uploader->nom_complet
     * Similar: Audience::avocat() — both use a non-default FK to User.
     *
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * Accessor — returns the file size as a human-readable string.
     *
     * Conversion thresholds:
     *   < 1 024 bytes  →  "{n} o"    (octets)
     *   < 1 048 576    →  "{n} Ko"   (kilobytes)
     *   ≥ 1 048 576    →  "{n} Mo"   (megabytes)
     *
     * Returns "Inconnue" when $this->taille is null.
     *
     * Usage : $document->taille_formattee  →  "1.5 Mo"
     */
    public function getTailleFormatteeAttribute(): string
    {
        // Guard: size not yet recorded (e.g. upload still processing).
        if ($this->taille === null) {
            return 'Inconnue';
        }

        // Sub-kilobyte: display raw bytes.
        if ($this->taille < 1024) {
            return "{$this->taille} o";
        }

        // Sub-megabyte: convert to kilobytes with one decimal place.
        if ($this->taille < 1048576) {
            return round($this->taille / 1024, 1).' Ko';
        }

        // Megabytes with one decimal place.
        return round($this->taille / 1048576, 1).' Mo';
    }
}
